<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\EventBudgetAllocation;
use App\Models\EventServiceSelection;
use App\Services\BudgetAllocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SmartBudgetController extends Controller
{
    public function __construct(
        private BudgetAllocationService $budgetService
    ) {}

    /**
     * Smart Budget Planner page.
     */
    public function index(Request $request, string $id)
    {
        $event = $this->ownEvent($request, $id);

        $services    = config('smart_budget.services', []);
        $luxuryLevels = config('smart_budget.luxury_levels', []);

        // Existing selections & allocations for this event
        $selections  = EventServiceSelection::where('event_id', (string) $event->getKey())->get();
        $allocations = EventBudgetAllocation::where('event_id', (string) $event->getKey())->get();

        $hasAllocations = $allocations->isNotEmpty();

        // If allocations exist, build confidence & warnings
        $confidence = null;
        $warnings   = [];
        $savings    = null;

        if ($hasAllocations) {
            $allocMap = $allocations->pluck('allocated_amount', 'category')->all();

            $confidence = $this->budgetService->confidenceScore(
                $allocMap,
                (string) $event->getKey(),
                $event->location ?? '',
                $event->guest_count_expected ?? 150,
                $event->luxury_level ?? 'balanced'
            );

            $warnings = $confidence['warnings'] ?? [];

            $savings = $this->budgetService->suggestSavings(
                (string) $event->getKey(),
                $allocMap,
                $event->location ?? ''
            );
        }

        return view('smart-budget.index', compact(
            'event', 'services', 'luxuryLevels',
            'selections', 'allocations', 'hasAllocations',
            'confidence', 'warnings', 'savings'
        ));
    }

    /**
     * Generate or regenerate smart budget allocations.
     */
    public function generate(Request $request, string $id)
    {
        $event = $this->ownEvent($request, $id);

        $request->validate([
            'luxury_level'     => ['required', 'in:budget,balanced,premium,luxury'],
            'selected_services' => ['required', 'array', 'min:1'],
            'selected_services.*' => ['string'],
            'priorities'        => ['nullable', 'array'],
            'priorities.*'      => ['in:high,medium,low'],
        ]);

        $luxuryLevel = $request->luxury_level;
        $selectedServices = $request->selected_services;
        $priorities = $request->priorities ?? [];
        $eventId = (string) $event->getKey();

        // Save luxury level on event
        $event->update(['luxury_level' => $luxuryLevel]);

        // Save service selections
        EventServiceSelection::where('event_id', $eventId)->delete();
        foreach ($selectedServices as $svc) {
            EventServiceSelection::create([
                'event_id'       => $eventId,
                'service_name'   => $svc,
                'priority_level' => $priorities[$svc] ?? 'medium',
            ]);
        }

        // Generate allocations
        $allocations = $this->budgetService->allocate(
            (float) $event->total_budget,
            $event->category ?? 'Wedding',
            (int) ($event->guest_count_expected ?? 150),
            $luxuryLevel,
            $selectedServices,
            $priorities
        );

        // Persist allocations (preserve locked state if re-generating)
        $existingLocks = EventBudgetAllocation::where('event_id', $eventId)
            ->where('is_locked', true)
            ->pluck('allocated_amount', 'category')
            ->all();

        EventBudgetAllocation::where('event_id', $eventId)->delete();

        foreach ($allocations as $category => $amount) {
            $isLocked = isset($existingLocks[$category]);
            EventBudgetAllocation::create([
                'event_id'         => $eventId,
                'category'         => $category,
                'allocated_amount' => $isLocked ? $existingLocks[$category] : $amount,
                'used_amount'      => 0,
                'priority_level'   => $priorities[$category] ?? 'medium',
                'is_locked'        => $isLocked,
            ]);
        }

        // Clear recommendation cache for this event
        $this->clearRecommendationCache($eventId);

        // Build response with confidence + warnings
        $allocMap = EventBudgetAllocation::where('event_id', $eventId)
            ->pluck('allocated_amount', 'category')->all();

        $confidence = $this->budgetService->confidenceScore(
            $allocMap, $eventId, $event->location ?? '',
            (int) ($event->guest_count_expected ?? 150), $luxuryLevel
        );

        return response()->json([
            'ok'           => true,
            'allocations'  => EventBudgetAllocation::where('event_id', $eventId)->get(),
            'confidence'   => $confidence,
            'warnings'     => $confidence['warnings'] ?? [],
        ]);
    }

    /**
     * Update priorities and rebalance.
     */
    public function updatePriorities(Request $request, string $id)
    {
        $event = $this->ownEvent($request, $id);
        $eventId = (string) $event->getKey();

        $request->validate([
            'priorities'  => ['required', 'array'],
            'priorities.*' => ['in:high,medium,low'],
            'locked'      => ['nullable', 'array'],
            'locked.*'    => ['boolean'],
        ]);

        $priorities = $request->priorities;
        $locked     = $request->locked ?? [];

        // Update individual allocations
        foreach ($priorities as $cat => $level) {
            EventBudgetAllocation::where('event_id', $eventId)
                ->where('category', $cat)
                ->update([
                    'priority_level' => $level,
                    'is_locked'      => !empty($locked[$cat]),
                ]);

            EventServiceSelection::where('event_id', $eventId)
                ->where('service_name', $cat)
                ->update(['priority_level' => $level]);
        }

        // Build rebalance input
        $currentAllocations = [];
        $allocs = EventBudgetAllocation::where('event_id', $eventId)->get();
        foreach ($allocs as $a) {
            $currentAllocations[$a->category] = [
                'amount'    => $a->allocated_amount,
                'priority'  => $a->priority_level,
                'is_locked' => $a->is_locked,
            ];
        }

        $newAmounts = $this->budgetService->rebalance(
            $currentAllocations,
            (float) $event->total_budget,
            $event->category ?? 'Wedding',
            (int) ($event->guest_count_expected ?? 150),
            $event->luxury_level ?? 'balanced'
        );

        // Persist rebalanced amounts
        foreach ($newAmounts as $cat => $amount) {
            EventBudgetAllocation::where('event_id', $eventId)
                ->where('category', $cat)
                ->update(['allocated_amount' => $amount]);
        }

        // Clear recommendation cache
        $this->clearRecommendationCache($eventId);

        $allocMap = EventBudgetAllocation::where('event_id', $eventId)
            ->pluck('allocated_amount', 'category')->all();

        $confidence = $this->budgetService->confidenceScore(
            $allocMap, $eventId, $event->location ?? '',
            (int) ($event->guest_count_expected ?? 150),
            $event->luxury_level ?? 'balanced'
        );

        return response()->json([
            'ok'          => true,
            'allocations' => EventBudgetAllocation::where('event_id', $eventId)->get(),
            'confidence'  => $confidence,
            'warnings'    => $confidence['warnings'] ?? [],
        ]);
    }

    /**
     * Fetch vendor recommendations for a category (dynamic, cached).
     */
    public function recommendations(Request $request, string $id)
    {
        $event   = $this->ownEvent($request, $id);
        $eventId = (string) $event->getKey();

        $category   = $request->get('category');
        $filterMode = $request->get('filter', 'best_match'); // best_match, cheaper, premium

        if (!$category) {
            return response()->json(['error' => 'Category required'], 400);
        }

        $alloc = EventBudgetAllocation::where('event_id', $eventId)
            ->where('category', $category)
            ->first();

        $budget = $alloc->allocated_amount ?? 50000;

        $vendors = $this->budgetService->recommendVendors(
            $eventId,
            $category,
            $budget,
            $event->location ?? '',
            optional($event->event_date)->format('Y-m-d'),
            $event->luxury_level ?? 'balanced',
            $filterMode,
            5
        );

        return response()->json([
            'category' => $category,
            'budget'   => $budget,
            'vendors'  => $vendors,
        ]);
    }

    /**
     * Real-time budget tracking data.
     */
    public function trackBudget(Request $request, string $id)
    {
        $event   = $this->ownEvent($request, $id);
        $eventId = (string) $event->getKey();

        $allocations = EventBudgetAllocation::where('event_id', $eventId)->get();

        // Compute used amounts from bookings
        $bookings = Booking::where('event_id', $eventId)->get();
        $usedByCategory = [];

        foreach ($bookings as $booking) {
            $vendor = $booking->vendor;
            if (!$vendor) continue;

            $vendorCategory = $vendor->category ?? '';
            $categoryMap = config('smart_budget.service_vendor_category_map', []);

            // Find which smart-budget category this vendor maps to
            foreach ($categoryMap as $smartCat => $vendorCats) {
                foreach ($vendorCats as $vc) {
                    if (stripos($vendorCategory, $vc) !== false) {
                        $usedByCategory[$smartCat] = ($usedByCategory[$smartCat] ?? 0) + ($booking->amount ?? 0);
                        break 2;
                    }
                }
            }
        }

        $tracking = [];
        $overSpendAlerts = [];

        foreach ($allocations as $alloc) {
            $used = $usedByCategory[$alloc->category] ?? 0;
            $tracking[] = [
                'category'         => $alloc->category,
                'allocated_amount' => $alloc->allocated_amount,
                'used_amount'      => $used,
                'remaining'        => max(0, $alloc->allocated_amount - $used),
                'percent_used'     => $alloc->allocated_amount > 0
                    ? min(100, round($used / $alloc->allocated_amount * 100))
                    : 0,
                'is_locked'        => $alloc->is_locked,
            ];

            if ($used > $alloc->allocated_amount && $alloc->allocated_amount > 0) {
                $label = config("smart_budget.services.{$alloc->category}.label", ucfirst($alloc->category));
                $excess = round($used - $alloc->allocated_amount);
                $overSpendAlerts[] = [
                    'category' => $alloc->category,
                    'message'  => "{$label} budget exceeded by ₹" . number_format($excess) . ".",
                    'excess'   => $excess,
                ];
            }
        }

        return response()->json([
            'tracking'   => $tracking,
            'alerts'     => $overSpendAlerts,
            'total_budget'    => $event->total_budget,
            'total_allocated' => $allocations->sum('allocated_amount'),
            'total_used'      => array_sum($usedByCategory),
        ]);
    }

    /**
     * Get savings suggestions.
     */
    public function savings(Request $request, string $id)
    {
        $event   = $this->ownEvent($request, $id);
        $eventId = (string) $event->getKey();

        $allocMap = EventBudgetAllocation::where('event_id', $eventId)
            ->pluck('allocated_amount', 'category')->all();

        $savings = $this->budgetService->suggestSavings(
            $eventId, $allocMap, $event->location ?? ''
        );

        return response()->json($savings);
    }

    /**
     * Clear recommendation cache for an event.
     */
    private function clearRecommendationCache(string $eventId): void
    {
        // We use tagged cache keys with event prefix; flush by pattern
        // For file/array cache driver, this is a best-effort approach
        $services = config('smart_budget.services', []);
        foreach (array_keys($services) as $svc) {
            foreach (['best_match', 'cheaper', 'premium'] as $mode) {
                Cache::forget("smart_reco:{$eventId}:{$svc}:{$mode}:" . md5(''));
            }
        }
    }

    private function ownEvent(Request $request, string $id): Event
    {
        return Event::where('_id', $id)
            ->where('user_id', (string) $request->user()->getKey())
            ->firstOrFail();
    }
}

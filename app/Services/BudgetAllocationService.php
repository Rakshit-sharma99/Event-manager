<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Event;
use App\Models\EventBudgetAllocation;
use App\Models\Vendor;
use Illuminate\Support\Facades\Cache;

class BudgetAllocationService
{
    /**
     * Generate budget allocations for an event.
     *
     * @return array<string, float> [category => allocatedAmount]
     */
    public function allocate(
        float  $budget,
        string $eventType,
        int    $guestCount,
        string $luxuryLevel,
        array  $selectedServices,
        array  $priorities = []
    ): array {
        $templates = config('smart_budget.templates');
        $template  = $templates[$eventType] ?? $templates['Wedding'];

        // Step 1: Filter to selected services only
        $weights = [];
        foreach ($selectedServices as $svc) {
            $weights[$svc] = $template[$svc] ?? 5; // default 5% for unknown
        }

        // Step 2: Apply guest-count scaling
        $scalingRules = config('smart_budget.guest_scaling', []);
        foreach ($scalingRules as $rule) {
            if ($guestCount >= $rule['threshold']) {
                foreach ($rule['boost'] as $cat => $multiplier) {
                    if (isset($weights[$cat])) {
                        $weights[$cat] *= $multiplier;
                    }
                }
            }
        }

        // Step 3: Apply luxury multiplier to premium categories
        $luxuryConfig     = config("smart_budget.luxury_levels.{$luxuryLevel}", config('smart_budget.luxury_levels.balanced'));
        $premiumCategories = config('smart_budget.premium_categories', []);
        $luxuryMultiplier = $luxuryConfig['multiplier'];

        foreach ($weights as $cat => &$w) {
            if (in_array($cat, $premiumCategories)) {
                $w *= $luxuryMultiplier;
            }
        }
        unset($w);

        // Step 4: Apply priority weights
        $priorityWeights = config('smart_budget.priority_weights', []);
        foreach ($priorities as $cat => $level) {
            if (isset($weights[$cat]) && isset($priorityWeights[$level])) {
                $weights[$cat] *= $priorityWeights[$level];
            }
        }

        // Step 5: Normalize to 100% and convert to amounts
        $total = array_sum($weights);
        if ($total <= 0) {
            return [];
        }

        $allocations = [];
        foreach ($weights as $cat => $w) {
            $allocations[$cat] = round(($w / $total) * $budget);
        }

        return $allocations;
    }

    /**
     * Rebalance allocations respecting locked categories.
     *
     * @param  array  $currentAllocations [category => ['amount' => float, 'priority' => string, 'is_locked' => bool]]
     * @param  float  $totalBudget
     * @param  string $eventType
     * @param  int    $guestCount
     * @param  string $luxuryLevel
     * @return array<string, float> [category => newAmount]
     */
    public function rebalance(
        array  $currentAllocations,
        float  $totalBudget,
        string $eventType,
        int    $guestCount,
        string $luxuryLevel
    ): array {
        $lockedTotal    = 0;
        $lockedItems    = [];
        $unlocked       = [];
        $priorities     = [];

        foreach ($currentAllocations as $cat => $info) {
            if (!empty($info['is_locked'])) {
                $lockedTotal += $info['amount'];
                $lockedItems[$cat] = $info['amount'];
            } else {
                $unlocked[] = $cat;
                $priorities[$cat] = $info['priority'] ?? 'medium';
            }
        }

        $remainingBudget = max(0, $totalBudget - $lockedTotal);

        // Re-allocate only the unlocked categories
        $freshAllocations = $this->allocate(
            $remainingBudget,
            $eventType,
            $guestCount,
            $luxuryLevel,
            $unlocked,
            $priorities
        );

        // Merge locked + fresh
        return array_merge($lockedItems, $freshAllocations);
    }

    /**
     * Generate vendor recommendations for a category (dynamically, cached).
     *
     * @return array
     */
    public function recommendVendors(
        string  $eventId,
        string  $category,
        float   $categoryBudget,
        string  $location,
        ?string $eventDate = null,
        string  $luxuryLevel = 'balanced',
        string  $filterMode = 'best_match',
        int     $limit = 5
    ): array {
        $cacheKey = "smart_reco:{$eventId}:{$category}:{$filterMode}:" . md5("{$categoryBudget}{$location}{$eventDate}");
        $ttl      = config('smart_budget.recommendation_cache_ttl', 30);

        return Cache::remember($cacheKey, $ttl * 60, function () use (
            $category, $categoryBudget, $location, $eventDate, $luxuryLevel, $filterMode, $limit, $eventId
        ) {
            $categoryMap = config('smart_budget.service_vendor_category_map', []);
            $vendorCategories = $categoryMap[$category] ?? [$category];

            $query = Vendor::query();

            // Match vendor category
            $query->where(function ($q) use ($vendorCategories) {
                foreach ($vendorCategories as $vc) {
                    $q->orWhere('category', 'like', "%{$vc}%");
                    $q->orWhere('speciality', 'like', "%{$vc}%");
                }
            });

            // Filter mode adjustments
            if ($filterMode === 'cheaper') {
                $query->where('price_min', '<=', $categoryBudget * 0.6);
            } elseif ($filterMode === 'premium') {
                $query->where('price_min', '>=', $categoryBudget * 0.5);
                $query->orderByDesc('rating');
            }

            $vendors = $query->get();

            // Already booked vendors for this event
            $bookedVendorIds = Booking::where('event_id', $eventId)
                ->pluck('vendor_id')
                ->map(fn ($id) => (string) $id)
                ->all();

            $scored = [];
            foreach ($vendors as $vendor) {
                $vendorId = (string) $vendor->getKey();

                // Skip already booked
                if (in_array($vendorId, $bookedVendorIds)) {
                    continue;
                }

                $scoreBreakdown = $this->calculateMatchScore(
                    $vendor, $categoryBudget, $location, $eventDate
                );

                $score   = $scoreBreakdown['total'];
                $reasons = $scoreBreakdown['reasons'];
                $label   = $this->getRecommendationLabel($vendor, $categoryBudget, $luxuryLevel);

                $scored[] = [
                    'vendor_id'      => $vendorId,
                    'business_name'  => $vendor->business_name ?? $vendor->name ?? 'Vendor',
                    'category'       => $vendor->category,
                    'price_min'      => $vendor->price_min ?? $vendor->budget_min ?? 0,
                    'price_max'      => $vendor->price_max ?? $vendor->budget_max ?? 0,
                    'rating'         => $vendor->rating ?? 0,
                    'total_reviews'  => $vendor->total_reviews ?? 0,
                    'location'       => $vendor->location ?? $vendor->base_location ?? '',
                    'description'    => $vendor->description ?? '',
                    'match_score'    => $score,
                    'label'          => $label,
                    'reasons'        => $reasons,
                    'is_available'   => $scoreBreakdown['is_available'],
                ];
            }

            // Sort by score descending
            usort($scored, fn ($a, $b) => $b['match_score'] <=> $a['match_score']);

            return array_slice($scored, 0, $limit);
        });
    }

    /**
     * Calculate vendor match score (0–100).
     */
    public function calculateMatchScore(
        Vendor  $vendor,
        float   $categoryBudget,
        string  $location,
        ?string $eventDate = null
    ): array {
        $weights = config('smart_budget.vendor_scoring');
        $reasons = [];
        $scores  = [];

        // 1. Price fit (35%)
        $priceMin = $vendor->price_min ?? $vendor->budget_min ?? 0;
        $priceMax = $vendor->price_max ?? $vendor->budget_max ?? $priceMin;

        if ($priceMin <= 0 && $priceMax <= 0) {
            $scores['price_fit'] = 50; // unknown pricing — neutral
            $reasons[] = 'Pricing information unavailable';
        } elseif ($categoryBudget >= $priceMin && $categoryBudget >= $priceMax * 0.8) {
            $scores['price_fit'] = 95;
            $reasons[] = 'Fits budget very well';
        } elseif ($categoryBudget >= $priceMin) {
            $ratio = min(1, $categoryBudget / max(1, $priceMax));
            $scores['price_fit'] = (int) (60 + $ratio * 35);
            $reasons[] = 'Within budget range';
        } else {
            $ratio = min(1, $categoryBudget / max(1, $priceMin));
            $scores['price_fit'] = max(10, (int) ($ratio * 55));
            $reasons[] = 'May exceed category budget';
        }

        // 2. Rating (20%)
        $rating = $vendor->rating ?? 0;
        $scores['rating'] = min(100, (int) ($rating * 20));
        if ($rating >= 4.5) {
            $reasons[] = 'Highly rated (' . number_format($rating, 1) . '★)';
        } elseif ($rating >= 3.5) {
            $reasons[] = 'Well rated (' . number_format($rating, 1) . '★)';
        }

        // 3. Availability (25%)
        $isAvailable = true;
        $availJson   = $vendor->availability_json ?? [];

        if ($eventDate && !empty($availJson)) {
            $dayOfWeek = strtolower(date('l', strtotime($eventDate)));
            if (isset($availJson[$dayOfWeek]) && !$availJson[$dayOfWeek]) {
                $isAvailable = false;
            }
            // Check blocked dates
            $blockedDates = $availJson['blocked_dates'] ?? [];
            if (in_array($eventDate, $blockedDates)) {
                $isAvailable = false;
            }
        }

        if ($isAvailable) {
            $scores['availability'] = 90;
            $reasons[] = 'Available on event date';
        } else {
            $scores['availability'] = 10;
            $reasons[] = 'May not be available on event date';
        }

        // 4. Location (10%)
        $vendorLoc = strtolower($vendor->location ?? $vendor->base_location ?? $vendor->work_location ?? '');
        $eventLoc  = strtolower($location);

        if ($vendorLoc && $eventLoc && str_contains($vendorLoc, $eventLoc)) {
            $scores['location'] = 95;
            $reasons[] = 'Located in your event city';
        } elseif ($vendorLoc && $eventLoc && (str_contains($eventLoc, $vendorLoc) || similar_text($vendorLoc, $eventLoc) > 5)) {
            $scores['location'] = 60;
            $reasons[] = 'Near your event location';
        } else {
            $scores['location'] = 30;
        }

        // 5. Reviews volume (10%)
        $reviews = $vendor->total_reviews ?? 0;
        $scores['reviews'] = min(100, $reviews * 5); // 20 reviews = 100%
        if ($reviews >= 10) {
            $reasons[] = $reviews . ' verified reviews';
        }

        // Calculate weighted total
        $total = 0;
        foreach ($scores as $key => $score) {
            $total += $score * ($weights[$key] ?? 0) / 100;
        }
        $total = min(100, max(0, (int) round($total)));

        return [
            'total'        => $total,
            'breakdown'    => $scores,
            'reasons'      => $reasons,
            'is_available' => $isAvailable,
        ];
    }

    /**
     * Get recommendation label for a vendor.
     */
    private function getRecommendationLabel(Vendor $vendor, float $budget, string $luxuryLevel): string
    {
        $priceMin = $vendor->price_min ?? $vendor->budget_min ?? 0;

        if ($priceMin <= 0) {
            return 'Available';
        }

        $ratio = $priceMin / max(1, $budget);

        if ($ratio <= 0.4) {
            return 'Budget Friendly';
        }
        if ($ratio >= 0.8 || $luxuryLevel === 'luxury' || $luxuryLevel === 'premium') {
            return ($vendor->rating ?? 0) >= 4.0 ? 'Premium Pick' : 'Premium';
        }

        return 'Best Value';
    }

    /**
     * Generate per-guest cost warnings.
     *
     * @return array of warning strings
     */
    public function perGuestWarnings(array $allocations, int $guestCount, string $luxuryLevel): array
    {
        $benchmarks = config('smart_budget.per_guest_benchmarks', []);
        $warnings   = [];

        foreach ($benchmarks as $category => $levels) {
            if (!isset($allocations[$category])) {
                continue;
            }

            $benchmark   = $levels[$luxuryLevel] ?? $levels['balanced'] ?? 0;
            $perGuest    = $guestCount > 0 ? $allocations[$category] / $guestCount : 0;
            $label       = config("smart_budget.services.{$category}.label", ucfirst($category));

            if ($benchmark > 0 && $perGuest < $benchmark * 0.7) {
                $warnings[] = [
                    'category'  => $category,
                    'message'   => "{$label} budget may be low for {$guestCount} guests (₹" . number_format(round($perGuest)) . "/guest vs recommended ₹" . number_format($benchmark) . "/guest).",
                    'severity'  => $perGuest < $benchmark * 0.5 ? 'critical' : 'warning',
                    'per_guest' => round($perGuest),
                    'benchmark' => $benchmark,
                ];
            }
        }

        return $warnings;
    }

    /**
     * Generate savings suggestions by comparing allocations with cheaper vendor options.
     */
    public function suggestSavings(string $eventId, array $allocations, string $location): array
    {
        $suggestions = [];
        $totalSavings = 0;

        foreach ($allocations as $category => $amount) {
            $vendors = $this->recommendVendors($eventId, $category, $amount, $location, null, 'budget', 'cheaper', 3);

            if (empty($vendors)) {
                continue;
            }

            // Compare cheapest vendor with allocated budget
            $cheapest = $vendors[0];
            $cheapPrice = $cheapest['price_min'] ?? 0;

            if ($cheapPrice > 0 && $cheapPrice < $amount * 0.7) {
                $saving = round($amount - $cheapPrice);
                $totalSavings += $saving;
                $label = config("smart_budget.services.{$category}.label", ucfirst($category));

                $suggestions[] = [
                    'category'    => $category,
                    'message'     => "Choose {$cheapest['business_name']} for {$label} — save ₹" . number_format($saving),
                    'saving'      => $saving,
                    'vendor_name' => $cheapest['business_name'],
                    'vendor_id'   => $cheapest['vendor_id'],
                ];
            }
        }

        // Add priority-based suggestion
        $highPriority = EventBudgetAllocation::where('event_id', $eventId)
            ->where('priority_level', 'low')
            ->where('allocated_amount', '>', 0)
            ->get();

        foreach ($highPriority as $alloc) {
            $label = config("smart_budget.services.{$alloc->category}.label", ucfirst($alloc->category));
            $potentialSave = round($alloc->allocated_amount * 0.15);
            if ($potentialSave > 1000) {
                $totalSavings += $potentialSave;
                $suggestions[] = [
                    'category' => $alloc->category,
                    'message'  => "Reduce {$label} priority — potential saving ₹" . number_format($potentialSave),
                    'saving'   => $potentialSave,
                ];
            }
        }

        return [
            'total_savings' => $totalSavings,
            'suggestions'   => $suggestions,
        ];
    }

    /**
     * Calculate a confidence score (0–100) for the current budget plan.
     */
    public function confidenceScore(
        array  $allocations,
        string $eventId,
        string $location,
        int    $guestCount,
        string $luxuryLevel
    ): array {
        $factors = [];

        // Factor 1: Enough vendors available (35%)
        $vendorCoverage = 0;
        $totalCategories = count($allocations);

        foreach ($allocations as $category => $amount) {
            $vendors = $this->recommendVendors($eventId, $category, $amount, $location, null, $luxuryLevel, 'best_match', 3);
            if (count($vendors) >= 2) {
                $vendorCoverage++;
            } elseif (count($vendors) >= 1) {
                $vendorCoverage += 0.6;
            }
        }

        $factors['vendor_availability'] = $totalCategories > 0
            ? min(100, (int) round(($vendorCoverage / $totalCategories) * 100))
            : 50;

        // Factor 2: Balanced allocations (30%) — no single category > 45%
        $total = array_sum($allocations);
        $maxShare = $total > 0 ? max($allocations) / $total * 100 : 0;
        $factors['balance'] = $maxShare > 50 ? 40 : ($maxShare > 40 ? 70 : 95);

        // Factor 3: Realistic per-guest costs (35%)
        $warnings = $this->perGuestWarnings($allocations, $guestCount, $luxuryLevel);
        $criticalWarnings = count(array_filter($warnings, fn ($w) => $w['severity'] === 'critical'));
        $totalWarnings = count($warnings);

        if ($criticalWarnings > 0) {
            $factors['per_guest_realism'] = max(20, 60 - $criticalWarnings * 20);
        } elseif ($totalWarnings > 0) {
            $factors['per_guest_realism'] = max(50, 85 - $totalWarnings * 10);
        } else {
            $factors['per_guest_realism'] = 95;
        }

        $overall = (int) round(
            $factors['vendor_availability'] * 0.35 +
            $factors['balance']             * 0.30 +
            $factors['per_guest_realism']   * 0.35
        );

        return [
            'overall'  => min(100, max(0, $overall)),
            'factors'  => $factors,
            'warnings' => $warnings,
        ];
    }
}

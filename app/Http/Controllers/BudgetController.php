<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Event;
use App\Models\EventBudget;
use App\Models\EventExpense;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request, string $eventId)
    {
        $event = $this->ownEvent($request, $eventId);
        $budgets = EventBudget::where('event_id', $eventId)->get();
        $expenses = EventExpense::where('event_id', $eventId)->latest()->paginate(12);
        $spent = EventExpense::where('event_id', $eventId)->sum('amount');
        $alerts = $this->alertsFor($event);

        return view('budget.index', compact('event', 'budgets', 'expenses', 'spent', 'alerts'));
    }

    public function addExpense(ExpenseRequest $request, string $eventId)
    {
        $event = $this->ownEvent($request, $eventId);
        $expense = EventExpense::create([...$request->validated(), 'event_id' => (string) $event->getKey()]);

        EventBudget::where('event_id', $eventId)->where('category', $expense->category)
            ->increment('spent_amount', (float) $expense->amount);

        return back()->with('success', 'Expense added and budget recalculated.');
    }

    public function deleteExpense(Request $request, string $id)
    {
        $expense = EventExpense::findOrFail($id);
        $event = $this->ownEvent($request, $expense->event_id);
        EventBudget::where('event_id', (string) $event->getKey())->where('category', $expense->category)
            ->decrement('spent_amount', (float) $expense->amount);
        $expense->delete();

        return back()->with('success', 'Expense removed.');
    }

    public function chart(Request $request, string $eventId)
    {
        $this->ownEvent($request, $eventId);

        return response()->json(EventBudget::where('event_id', $eventId)->get()->map(fn ($budget) => [
            'label' => str($budget->category)->headline()->toString(),
            'budget' => (float) $budget->budgeted_amount,
            'spent' => (float) $budget->spent_amount,
        ]));
    }

    public function alerts(Request $request, string $eventId)
    {
        return response()->json($this->alertsFor($this->ownEvent($request, $eventId)));
    }

    private function alertsFor(Event $event): array
    {
        $spent = EventExpense::where('event_id', (string) $event->getKey())->sum('amount');
        $percent = $event->total_budget > 0 ? ($spent / $event->total_budget) * 100 : 0;

        return [
            ['level' => $percent > 90 ? 'critical' : 'normal', 'message' => $percent > 90 ? 'Budget usage is above 90%.' : 'Budget is under control.'],
            ['level' => $percent > 75 ? 'warning' : 'normal', 'message' => round($percent).'% of total budget used.'],
        ];
    }

    private function ownEvent(Request $request, string $id): Event
    {
        return Event::where('_id', $id)->where('user_id', (string) $request->user()->getKey())->firstOrFail();
    }
}

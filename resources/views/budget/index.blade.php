@extends('layouts.app', ['title' => 'Budget - Eventra'])
@section('page-title','Budget')

@section('content')
<section class="plain-section">
    <p class="chip">{{ $event->event_name }}</p>
    <h2>Budget</h2>
    <p><a href="{{ route('events.show', $event) }}">Back to event</a></p>
</section>

<section class="mobile-safe-grid plain-section">
    <div class="stat-card plain-stat"><p>Total Budget</p><strong>Rs. {{ number_format($event->total_budget) }}</strong></div>
    <div class="stat-card plain-stat"><p>Spent</p><strong>Rs. {{ number_format($spent) }}</strong></div>
    <div class="stat-card plain-stat"><p>Remaining</p><strong>Rs. {{ number_format(max(0, $event->total_budget - $spent)) }}</strong></div>
    <div class="stat-card plain-stat"><p>Burn</p><strong>{{ $event->total_budget ? round($spent / $event->total_budget * 100) : 0 }}%</strong></div>
</section>

<section class="plain-section grid-list">
    <form method="POST" action="{{ route('budget.expense.store', $event) }}" class="panel">
        @csrf
        <h3>Add expense</h3>

        <label class="field-label">Expense</label>
        <input name="expense_name" required>

        <label class="field-label">Amount</label>
        <input name="amount" type="number" required>

        <label class="field-label">Category</label>
        <select name="category">
            @foreach($budgets as $budget)
                <option value="{{ $budget->category }}">{{ str($budget->category)->headline() }}</option>
            @endforeach
        </select>

        <label class="field-label">Date</label>
        <input name="date" type="date" value="{{ now()->toDateString() }}" required>

        <p><button class="btn-primary">Add expense</button></p>
    </form>

    <article class="panel">
        <h3>Category totals</h3>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Spent</th>
                    <th>Budgeted</th>
                    <th>Used</th>
                </tr>
            </thead>
            <tbody>
                @foreach($budgets as $budget)
                    @php($pct = $budget->budgeted_amount ? min(100, round($budget->spent_amount / $budget->budgeted_amount * 100)) : 0)
                    <tr>
                        <td>{{ str($budget->category)->headline() }}</td>
                        <td>Rs. {{ number_format($budget->spent_amount) }}</td>
                        <td>Rs. {{ number_format($budget->budgeted_amount) }}</td>
                        <td>{{ $pct }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </article>
</section>

<section class="plain-section">
    <h3>Expense history</h3>
    <table class="lux-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
                <tr>
                    <td>{{ $expense->expense_name }}</td>
                    <td>{{ str($expense->category)->headline() }}</td>
                    <td>{{ optional($expense->date)->format('M d') }}</td>
                    <td>Rs. {{ number_format($expense->amount) }}</td>
                    <td>
                        <form method="POST" action="{{ route('budget.expense.destroy', $expense) }}">
                            @csrf
                            @method('DELETE')
                            <button>Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</section>

<section class="plain-section">
    <h3>Alerts</h3>
    @forelse($alerts as $alert)
        <p class="panel">{{ $alert['message'] }}</p>
    @empty
        <p>No alerts.</p>
    @endforelse
</section>
@endsection

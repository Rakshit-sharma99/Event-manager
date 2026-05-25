@extends('layouts.app', ['title' => 'Budget - Eventra'])
@section('page-title','Budget')

@section('content')
<section class="plain-section">
    <p class="chip">{{ $event->event_name }}</p>
    <h2>Budget</h2>
    <p><a href="{{ route('events.show', $event) }}">Back to event</a> · <a href="{{ route('smart-budget.index', $event) }}">🧠 Smart Budget Planner</a></p>
</section>

<section class="mobile-safe-grid plain-section">
    <div class="stat-card plain-stat"><p>Total Budget</p><strong>Rs. {{ number_format($event->total_budget) }}</strong></div>
    <div class="stat-card plain-stat clickable-card" onclick="openSpentBreakdownModal()"><p>Spent <span style="font-size: 0.8rem; opacity: 0.7;">🔍</span></p><strong>Rs. {{ number_format($spent) }}</strong></div>
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

<!-- Spent Breakdown Modal -->
<div id="spentModal" class="modal-backdrop" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Budget Expenditure Breakdown</h3>
            <button class="close-btn" onclick="closeSpentBreakdownModal()">&times;</button>
        </div>
        <div class="modal-body">
            <!-- Category Progress Bars -->
            <div class="section-title">Spent by Category</div>
            <div class="category-breakdown-list">
                @foreach($budgets as $budget)
                    <?php
                        $pct = $budget->budgeted_amount ? min(100, round(($budget->spent_amount / $budget->budgeted_amount) * 100)) : 0;
                        $barColor = $pct > 90 ? '#ef4444' : ($pct > 75 ? '#f59e0b' : '#10b981');
                    ?>
                    <div class="category-item" style="margin-bottom: 16px;">
                        <div class="category-info" style="display: flex; justify-content: space-between; font-weight: 600; margin-bottom: 4px; font-size: 0.9rem;">
                            <span>{{ str($budget->category)->headline() }}</span>
                            <span style="color: #4b5563;">Rs. {{ number_format($budget->spent_amount) }} / Rs. {{ number_format($budget->budgeted_amount) }}</span>
                        </div>
                        <div class="progress-bar-container" style="width: 100%; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                            <div class="progress-bar-fill" style="width: {{ $pct }}%; height: 100%; background: {{ $barColor }}; transition: width 0.3s ease;"></div>
                        </div>
                        <div style="font-size: 0.75rem; text-align: right; color: #6b7280; margin-top: 2px;">{{ $pct }}% utilized</div>
                    </div>
                @endforeach
            </div>

            <!-- Expense List -->
            <div class="section-title" style="margin-top: 24px; margin-bottom: 12px;">Recent Transactions</div>
            <div class="recent-expenses-list" style="max-height: 250px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px; background: #f9fafb;">
                @if($expenses->isEmpty())
                    <p style="padding: 16px; text-align: center; color: #9ca3af; margin: 0;">No expenses logged yet.</p>
                @else
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                        <thead>
                            <tr style="background: #f3f4f6; border-bottom: 1px solid #e5e7eb; position: sticky; top: 0;">
                                <th style="padding: 10px 12px; color: #374151;">Name</th>
                                <th style="padding: 10px 12px; color: #374151;">Category</th>
                                <th style="padding: 10px 12px; color: #374151; text-align: right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expenses as $expense)
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 10px 12px; font-weight: 600; color: #111827;">{{ $expense->expense_name }}</td>
                                    <td style="padding: 10px 12px; color: #4b5563;">{{ str($expense->category)->headline() }}</td>
                                    <td style="padding: 10px 12px; text-align: right; font-weight: 700; color: #047857;">Rs. {{ number_format($expense->amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* Premium Modal Styles */
.stat-card.plain-stat.clickable-card {
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
}
.stat-card.plain-stat.clickable-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.08);
    border-color: #008069;
}

.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(5px);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
    animation: fadeIn 0.2s ease-out;
}
.modal-content {
    background: #ffffff;
    border-radius: 16px;
    width: 90%;
    max-width: 550px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    animation: slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.modal-header {
    padding: 16px 24px;
    background: #f9fafb;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.modal-header h3 {
    margin: 0;
    font-size: 1.2rem;
    color: #111827;
    font-weight: 800;
}
.close-btn {
    background: none;
    border: none;
    font-size: 1.8rem;
    color: #9ca3af;
    cursor: pointer;
    line-height: 1;
    padding: 0;
    transition: color 0.15s;
}
.close-btn:hover {
    color: #374151;
}
.modal-body {
    padding: 24px;
}
.section-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: #6b7280;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes slideUp {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>

<script>
function openSpentBreakdownModal() {
    document.getElementById('spentModal').style.display = 'flex';
}
function closeSpentBreakdownModal() {
    document.getElementById('spentModal').style.display = 'none';
}
// Close if clicked outside the content
window.addEventListener('click', function(event) {
    const modal = document.getElementById('spentModal');
    if (event.target === modal) {
        closeSpentBreakdownModal();
    }
});
</script>
@endsection

@extends('layouts.app', ['title' => 'Budget - Eventra'])
@section('page-title','Budget Planner')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div><p class="chip">{{ $event->event_name }}</p><h2 class="mt-3 font-display text-4xl font-bold">Budget intelligence</h2></div>
    <a class="btn-ghost" href="{{ route('events.show',$event) }}">Back to event</a>
</div>
<section class="mobile-safe-grid">
    <div class="stat-card"><p class="text-white/45">Total Budget</p><b class="text-3xl">₹{{ number_format($event->total_budget) }}</b></div>
    <div class="stat-card"><p class="text-white/45">Spent</p><b class="text-3xl text-eventra-cyan">₹{{ number_format($spent) }}</b></div>
    <div class="stat-card"><p class="text-white/45">Remaining</p><b class="text-3xl">₹{{ number_format(max(0,$event->total_budget-$spent)) }}</b></div>
    <div class="stat-card"><p class="text-white/45">Burn</p><b class="text-3xl">{{ $event->total_budget ? round($spent/$event->total_budget*100) : 0 }}%</b></div>
</section>
<section class="mt-6 grid gap-6 xl:grid-cols-[.8fr_1.2fr]">
    <form method="POST" action="{{ route('budget.expense.store',$event) }}" class="glass-strong rounded-[2rem] p-6">
        @csrf
        <h3 class="font-display mb-4 text-2xl font-bold">Add expense</h3>
        <label class="field-label">Expense</label><input class="mb-3 w-full" name="expense_name" required>
        <label class="field-label">Amount</label><input class="mb-3 w-full" name="amount" type="number" required>
        <label class="field-label">Category</label><select class="mb-3 w-full" name="category">@foreach($budgets as $budget)<option value="{{ $budget->category }}">{{ str($budget->category)->headline() }}</option>@endforeach</select>
        <label class="field-label">Date</label><input class="mb-5 w-full" name="date" type="date" value="{{ now()->toDateString() }}" required>
        <button class="btn-primary w-full">Add expense</button>
    </form>
    <div class="glass rounded-[2rem] p-6">
        <h3 class="font-display mb-4 text-2xl font-bold">Category breakdown</h3>
        <canvas id="budgetChart" height="150"></canvas>
        <div class="mt-6 space-y-3">
            @foreach($budgets as $budget)
                @php($pct = $budget->budgeted_amount ? min(100, round($budget->spent_amount/$budget->budgeted_amount*100)) : 0)
                <div class="rounded-2xl bg-white/[.04] p-3"><div class="flex justify-between text-sm"><span>{{ str($budget->category)->headline() }}</span><span>₹{{ number_format($budget->spent_amount) }} / ₹{{ number_format($budget->budgeted_amount) }}</span></div><div class="mt-2 h-2 rounded-full bg-white/10"><div class="h-2 rounded-full bg-eventra-blue" style="width:{{ $pct }}%"></div></div></div>
            @endforeach
        </div>
    </div>
</section>
<section class="mt-6 grid gap-6 xl:grid-cols-[1fr_.6fr]">
    <div class="glass rounded-[2rem] p-6"><h3 class="font-display mb-4 text-2xl font-bold">Expense history</h3><table class="lux-table"><thead><tr><th>Name</th><th>Category</th><th>Date</th><th>Amount</th><th></th></tr></thead><tbody>@foreach($expenses as $expense)<tr><td>{{ $expense->expense_name }}</td><td>{{ str($expense->category)->headline() }}</td><td>{{ optional($expense->date)->format('M d') }}</td><td>₹{{ number_format($expense->amount) }}</td><td><form method="POST" action="{{ route('budget.expense.destroy',$expense) }}">@csrf @method('DELETE')<button class="text-rose-300">Delete</button></form></td></tr>@endforeach</tbody></table></div>
    <div class="glass rounded-[2rem] p-6"><h3 class="font-display mb-4 text-2xl font-bold">Alerts</h3>@foreach($alerts as $alert)<div class="mb-3 rounded-2xl border {{ $alert['level']==='critical' ? 'border-rose-400/40 bg-rose-500/10' : 'border-white/10 bg-white/[.04]' }} p-4">{{ $alert['message'] }}</div>@endforeach</div>
</section>
<script>
document.addEventListener('DOMContentLoaded', async () => {
 const rows = await fetch('{{ route('api.budget.chart',$event) }}').then(r=>r.json());
 const ctx = document.getElementById('budgetChart');
 if(ctx) new Chart(ctx,{type:'pie',data:{labels:rows.map(r=>r.label),datasets:[{data:rows.map(r=>r.spent),backgroundColor:['#1687ff','#49d8ff','#ff4fb8','#ffb454','#6a5cff','#16c784','#f43f5e']}]},options:{plugins:{legend:{labels:{color:'#c8d6e5'}}}}});
});
</script>
@endsection

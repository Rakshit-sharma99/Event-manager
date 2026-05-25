@extends('layouts.app', ['title' => 'Budget — Eventra'])

@section('content')
<div class="space-y-6" x-data>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="badge bg-primary-50 text-primary-600 font-semibold mb-2">{{ $event->event_name }}</span>
            <h1 class="text-h2 font-extrabold text-neutral-dark">Budget Board</h1>
        </div>
        <div class="flex gap-2">
            <x-btn href="{{ route('events.show', $event) }}" variant="ghost" icon="arrow-left" size="sm">
                Back to Event
            </x-btn>
            <x-btn href="{{ route('smart-budget.index', $event) }}" variant="outline" size="sm" icon="info">
                🧠 Smart Budget Planner
            </x-btn>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4" data-animate="stagger">
        <x-stat-card 
            label="Total Budget" 
            :value="'₹' . number_format($event->total_budget)" 
            sub="Set budget limit" 
            icon="💰" 
        />
        
        {{-- Clickable Spent Card --}}
        <div @click="$dispatch('open-modal', 'spent-breakdown')" class="cursor-pointer group">
            <x-card class="hover:-translate-y-1 hover:shadow-glow bg-primary-500 text-white flex flex-col justify-between h-full relative overflow-hidden transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                <div class="relative z-10 flex items-center justify-between gap-2">
                    <span class="text-caption font-semibold opacity-90">Total Spent</span>
                    <span class="text-white/80 text-xs">🔍 Click details</span>
                </div>
                <div class="relative z-10 mt-3 flex items-baseline gap-2">
                    <span class="text-h2 font-extrabold">₹{{ number_format($spent) }}</span>
                </div>
                <p class="relative z-10 mt-1 text-[11px] opacity-75 truncate">Click for Category Breakdown</p>
            </x-card>
        </div>

        <x-stat-card 
            label="Remaining Budget" 
            :value="'₹' . number_format(max(0, $event->total_budget - $spent))" 
            sub="Available money" 
            icon="⚖️" 
        />

        @php
            $burnPercent = $event->total_budget > 0 ? min(100, round(($spent / $event->total_budget) * 100)) : 0;
            $burnVariant = $burnPercent > 90 ? 'danger' : ($burnPercent > 75 ? 'warning' : 'success');
        @endphp
        <x-stat-card 
            label="Burn Rate" 
            :value="$burnPercent . '%'" 
            sub="Budget utilized" 
            icon="🔥" 
        />
    </div>

    {{-- Alert Banner --}}
    @if(count($alerts))
        <div class="space-y-2">
            @foreach($alerts as $alert)
                <div class="p-4 rounded-md bg-danger-50 border border-danger-200 text-danger-700 flex items-center gap-3">
                    <span class="text-xl">⚠️</span>
                    <span class="text-body font-semibold">{{ $alert['message'] }}</span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Main Sections (Add Expense & Category Totals) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Form Column --}}
        <div>
            <x-card class="space-y-5" data-animate="fade-up">
                <h3 class="text-h4 font-bold text-neutral-dark border-b border-surface-100 pb-2">Add Expense</h3>
                
                <form method="POST" action="{{ route('budget.expense.store', $event) }}" class="space-y-4">
                    @csrf

                    <x-input 
                        label="Expense Title" 
                        name="expense_name" 
                        placeholder="e.g. Stage Sound System" 
                        required 
                    />

                    <x-input 
                        label="Amount (INR)" 
                        name="amount" 
                        type="number" 
                        placeholder="e.g. 15000" 
                        required 
                    />

                    <div class="space-y-1.5">
                        <label for="category" class="block text-body font-medium text-surface-700">Category</label>
                        <select name="category" id="category" class="input" required>
                            @foreach($budgets as $budget)
                                <option value="{{ $budget->category }}">{{ str($budget->category)->headline() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <x-input 
                        label="Expense Date" 
                        name="date" 
                        type="date" 
                        required 
                        :value="now()->toDateString()" 
                    />

                    <button type="submit" class="btn-primary w-full py-2.5">
                        Add Expense
                    </button>
                </form>
            </x-card>
        </div>

        {{-- Right Categories totals Column --}}
        <div class="lg:col-span-2">
            <x-card class="!p-0 overflow-hidden border border-surface-200 shadow-2xs" data-animate="fade-up">
                <div class="bg-surface-50 px-6 py-4 border-b border-surface-200">
                    <h3 class="text-body-lg font-bold text-neutral-dark">Category Allocations</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-50/50 border-b border-surface-100">
                                <th class="px-6 py-3.5 text-caption font-bold text-surface-600 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3.5 text-caption font-bold text-surface-600 uppercase tracking-wider">Spent</th>
                                <th class="px-6 py-3.5 text-caption font-bold text-surface-600 uppercase tracking-wider">Budgeted</th>
                                <th class="px-6 py-3.5 text-caption font-bold text-surface-600 uppercase tracking-wider text-right">Used %</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-100">
                            @foreach($budgets as $budget)
                                @php
                                    $pct = $budget->budgeted_amount ? min(100, round(($budget->spent_amount / $budget->budgeted_amount) * 100)) : 0;
                                    $barColor = $pct > 90 ? 'bg-red-500' : ($pct > 75 ? 'bg-amber-500' : 'bg-green-500');
                                @endphp
                                <tr class="hover:bg-surface-50/30 transition-colors">
                                    <td class="px-6 py-4 text-body font-bold text-neutral-dark">
                                        {{ str($budget->category)->headline() }}
                                    </td>
                                    <td class="px-6 py-4 text-body text-surface-500 font-semibold">
                                        ₹{{ number_format($budget->spent_amount) }}
                                    </td>
                                    <td class="px-6 py-4 text-body text-surface-500 font-medium">
                                        ₹{{ number_format($budget->budgeted_amount) }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <div class="w-20 bg-surface-100 h-1.5 rounded-full overflow-hidden hidden sm:block">
                                                <div class="h-full rounded-full {{ $barColor }}" style="width: {{ $pct }}%"></div>
                                            </div>
                                            <span class="text-body font-bold text-neutral-dark">{{ $pct }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>

    {{-- Expense History list --}}
    <x-card class="!p-0 overflow-hidden border border-surface-200 shadow-2xs" data-animate="fade-up">
        <div class="bg-surface-50 px-6 py-4 border-b border-surface-200">
            <h3 class="text-body-lg font-bold text-neutral-dark">Expense History</h3>
        </div>
        
        @if($expenses->isEmpty())
            <p class="text-body text-surface-400 py-12 text-center">No expenses registered for this event yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-50/50 border-b border-surface-100">
                            <th class="px-6 py-3.5 text-caption font-bold text-surface-600 uppercase tracking-wider">Expense Name</th>
                            <th class="px-6 py-3.5 text-caption font-bold text-surface-600 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3.5 text-caption font-bold text-surface-600 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3.5 text-caption font-bold text-surface-600 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3.5 text-caption font-bold text-surface-600 uppercase tracking-wider text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach($expenses as $expense)
                            <tr class="hover:bg-surface-50/30 transition-colors">
                                <td class="px-6 py-4 text-body font-semibold text-neutral-dark">
                                    {{ $expense->expense_name }}
                                </td>
                                <td class="px-6 py-4 text-body text-surface-500">
                                    {{ str($expense->category)->headline() }}
                                </td>
                                <td class="px-6 py-4 text-body text-surface-500 font-medium">
                                    {{ optional($expense->date)->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-body font-bold text-primary-500">
                                    ₹{{ number_format($expense->amount) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form method="POST" action="{{ route('budget.expense.destroy', $expense) }}" onsubmit="return confirm('Remove this expense?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-caption font-bold text-danger hover:text-danger-dark transition-colors uppercase tracking-wider">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>

    {{-- Spent Breakdown Modal using x-modal --}}
    <x-modal name="spent-breakdown" maxWidth="2xl">
        <div class="space-y-6">
            {{-- Modal Header --}}
            <div class="border-b border-surface-150 pb-4">
                <h3 class="text-h3 font-extrabold text-neutral-dark">Budget Expenditure Breakdown</h3>
                <p class="text-caption text-surface-500 font-medium">Detailed category limits and utilization ratios.</p>
            </div>

            {{-- Category utilization progress bars --}}
            <div class="space-y-4">
                <span class="text-[10px] font-extrabold tracking-wider uppercase text-surface-400">Spent by Category</span>
                <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2">
                    @foreach($budgets as $budget)
                        @php
                            $pct = $budget->budgeted_amount ? min(100, round(($budget->spent_amount / $budget->budgeted_amount) * 100)) : 0;
                            $barBg = $pct > 90 ? 'bg-red-500' : ($pct > 75 ? 'bg-amber-500' : 'bg-green-500');
                            $txtColor = $pct > 90 ? 'text-red-600' : ($pct > 75 ? 'text-amber-600' : 'text-green-600');
                        @endphp
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-body font-semibold">
                                <span class="text-neutral-dark">{{ str($budget->category)->headline() }}</span>
                                <span class="text-surface-500">₹{{ number_format($budget->spent_amount) }} / <span class="font-normal text-surface-400">₹{{ number_format($budget->budgeted_amount) }}</span></span>
                            </div>
                            <div class="w-full bg-surface-100 h-2 rounded-full overflow-hidden">
                                <div class="h-full rounded-full {{ $barBg }}" style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="font-medium text-surface-400">Limit Utilization</span>
                                <span class="font-bold {{ $txtColor }}">{{ $pct }}% utilized</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Recent Transactions list in modal --}}
            <div class="space-y-2 pt-2">
                <span class="text-[10px] font-extrabold tracking-wider uppercase text-surface-400">Recent Transactions</span>
                <div class="rounded-md border border-surface-200 bg-surface-50/50 max-h-48 overflow-y-auto shadow-inner">
                    @if($expenses->isEmpty())
                        <p class="text-caption text-surface-400 py-6 text-center">No transactions logged.</p>
                    @else
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-surface-100 border-b border-surface-200 sticky top-0">
                                    <th class="px-4 py-2 font-bold text-surface-600">Name</th>
                                    <th class="px-4 py-2 font-bold text-surface-600">Category</th>
                                    <th class="px-4 py-2 font-bold text-surface-600 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-150">
                                @foreach($expenses as $expense)
                                    <tr>
                                        <td class="px-4 py-2 font-semibold text-neutral-dark">{{ $expense->expense_name }}</td>
                                        <td class="px-4 py-2 text-surface-500">{{ str($expense->category)->headline() }}</td>
                                        <td class="px-4 py-2 text-right font-bold text-green-600">₹{{ number_format($expense->amount) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- Close CTA button --}}
            <div class="border-t border-surface-150 pt-4 flex justify-end">
                <x-btn @click="open = false" variant="primary" size="sm">
                    Close Details
                </x-btn>
            </div>
        </div>
    </x-modal>
</div>
@endsection

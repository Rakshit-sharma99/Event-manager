@extends('layouts.app', ['title' => 'Bookings — Eventra'])

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="badge bg-primary-50 text-primary-600 font-semibold mb-2">{{ $event->event_name }}</span>
            <h1 class="text-h2 font-extrabold text-neutral-dark">Booked Vendors</h1>
        </div>
        <div class="flex gap-2">
            <x-btn href="{{ route('bookings.create', $event) }}" variant="primary" size="sm" icon="plus">
                Add Booking
            </x-btn>
            <x-btn href="{{ route('bookings.timeline', $event) }}" variant="outline" size="sm" icon="calendar">
                View Timeline
            </x-btn>
        </div>
    </div>

    {{-- Bookings Table --}}
    @if($bookings->isEmpty())
        <x-card class="py-16">
            <x-empty-state 
                title="No bookings yet" 
                description="You haven't booked any vendors for this event yet. Browse the directory to find and book services." 
                icon="🏪"
                action="Find Vendors"
                :actionUrl="route('vendors.index')"
            />
        </x-card>
    @else
        <x-card class="!p-0 overflow-hidden border border-surface-200 shadow-2xs" data-animate="fade-up">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-50 border-b border-surface-200">
                            <th class="px-6 py-4 text-caption font-bold text-surface-600 uppercase tracking-wider">Vendor</th>
                            <th class="px-6 py-4 text-caption font-bold text-surface-600 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 text-caption font-bold text-surface-600 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-caption font-bold text-surface-600 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-4 text-caption font-bold text-surface-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-caption font-bold text-surface-600 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-4 text-caption font-bold text-surface-600 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach($bookings as $booking)
                            @php $bookingId = (string) $booking->getKey(); @endphp
                            <tr class="hover:bg-surface-50/50 transition-colors">
                                <td class="px-6 py-4 text-body font-bold text-neutral-dark">
                                    {{ optional($booking->vendor)->business_name ?? 'Vendor' }}
                                </td>
                                <td class="px-6 py-4 text-body text-surface-500">
                                    {{ str(optional($booking->vendor)->category ?? 'misc')->headline() }}
                                </td>
                                <td class="px-6 py-4 text-body text-surface-500 font-semibold">
                                    {{ optional($booking->booking_date)->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-body text-surface-500 font-medium">
                                    {{ $booking->booking_time_from }} - {{ $booking->booking_time_to }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $status = $booking->status ?? 'pending';
                                        $badgeVar = match($status) {
                                            'accepted', 'confirmed' => 'success',
                                            'declined', 'cancelled' => 'danger',
                                            'negotiating' => 'info',
                                            default => 'warning',
                                        };
                                    @endphp
                                    <x-badge variant="{{ $badgeVar }}">
                                        {{ ucfirst($status) }}
                                    </x-badge>
                                </td>
                                <td class="px-6 py-4 text-body font-bold text-primary-500">
                                    ₹{{ number_format($booking->amount) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button 
                                            type="button" 
                                            onclick="togglePlannerChat('{{ $bookingId }}')" 
                                            class="text-caption font-bold text-primary-500 hover:text-primary-600 transition-colors uppercase tracking-wider"
                                        >
                                            💬 Chat
                                        </button>
                                        <form method="POST" action="{{ route('bookings.cancel', [$event, $booking]) }}" class="inline" onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="text-caption font-bold text-danger hover:text-danger-dark transition-colors uppercase tracking-wider">
                                                Cancel
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            {{-- Inline chat row --}}
                            <tr id="planner-chat-row-{{ $bookingId }}" style="display: none;">
                                <td colspan="7" class="px-6 py-4 bg-surface-50/30">
                                    <div class="max-w-3xl mx-auto rounded-md border border-surface-200 shadow-sm overflow-hidden bg-white">
                                        {{-- Chat Header --}}
                                        <div class="bg-surface-50 border-b border-surface-200 px-4 py-3 flex items-center justify-between">
                                            <span class="text-body font-bold text-neutral-dark">
                                                Chat with {{ optional($booking->vendor)->business_name ?? 'Vendor' }}
                                            </span>
                                            <span class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></span>
                                        </div>

                                        {{-- Messages list --}}
                                        <div 
                                            id="planner-chat-messages-{{ $bookingId }}" 
                                            class="h-64 overflow-y-auto p-4 space-y-3 flex flex-col bg-surface-50/50"
                                        >
                                            <p class="text-caption text-surface-400 font-medium text-center m-auto">Loading messages...</p>
                                        </div>

                                        {{-- Chat Send Input --}}
                                        <div class="flex gap-2 p-3 border-t border-surface-200 bg-white">
                                            <input 
                                                id="planner-chat-input-{{ $bookingId }}" 
                                                type="text" 
                                                placeholder="Type a message..." 
                                                class="input !py-2" 
                                                onkeydown="if(event.key==='Enter'){sendPlannerMsg('{{ $bookingId }}');event.preventDefault();}"
                                            >
                                            <button 
                                                onclick="sendPlannerMsg('{{ $bookingId }}')" 
                                                class="btn-primary !py-2 px-5"
                                            >
                                                Send
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif
</div>

<script>
const plannerChatIntervals = {};
const plannerCsrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
const eventId = '{{ (string) $event->getKey() }}';

function togglePlannerChat(bookingId) {
    const row = document.getElementById('planner-chat-row-' + bookingId);
    if (row.style.display === 'none') {
        row.style.display = '';
        loadPlannerMessages(bookingId);
        if (!plannerChatIntervals[bookingId]) {
            plannerChatIntervals[bookingId] = setInterval(() => loadPlannerMessages(bookingId), 3000);
        }
    } else {
        row.style.display = 'none';
        if (plannerChatIntervals[bookingId]) {
            clearInterval(plannerChatIntervals[bookingId]);
            delete plannerChatIntervals[bookingId];
        }
    }
}

function loadPlannerMessages(bookingId) {
    fetch('/events/' + eventId + '/bookings/' + bookingId + '/messages', {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(msgs => {
        const container = document.getElementById('planner-chat-messages-' + bookingId);
        if (!msgs.length) {
            container.innerHTML = '<p class="text-caption text-surface-400 font-medium text-center m-auto">No messages yet. Start the conversation!</p>';
            return;
        }
        container.innerHTML = msgs.map(m => {
            const isMine = m.is_mine;
            const align = isMine ? 'self-end' : 'self-start';
            const bg = isMine ? 'bg-primary-500 text-white rounded-tr-none' : 'bg-surface-150 text-neutral-dark rounded-tl-none';
            const roleColor = isMine ? 'text-white/80' : (m.sender_role === 'planner' ? 'text-primary-600' : 'text-green-600');
            const timeColor = isMine ? 'text-white/70' : 'text-surface-400';
            
            return `<div class="${align} max-w-[75%] p-3 rounded-lg shadow-2xs ${bg}">
                <strong class="text-[10px] font-bold tracking-wider uppercase ${roleColor}">${m.sender_name} (${m.sender_role})</strong>
                <div class="text-body mt-0.5 leading-relaxed">${escPlannerHtml(m.message)}</div>
                <small class="text-[9px] block text-right mt-1.5 ${timeColor}">${m.time}</small>
            </div>`;
        }).join('');
        container.scrollTop = container.scrollHeight;
    })
    .catch(() => {});
}

function sendPlannerMsg(bookingId) {
    const input = document.getElementById('planner-chat-input-' + bookingId);
    const msg = input.value.trim();
    if (!msg) return;
    input.value = '';
    fetch('/events/' + eventId + '/bookings/' + bookingId + '/messages', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': plannerCsrf },
        body: JSON.stringify({ message: msg })
    }).then(() => loadPlannerMessages(bookingId));
}

function escPlannerHtml(s) {
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
}
</script>
@endsection

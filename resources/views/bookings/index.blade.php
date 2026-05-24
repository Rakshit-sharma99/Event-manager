@extends('layouts.app', ['title' => 'Bookings - Eventra'])
@section('page-title','Vendor Bookings')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="chip">{{ $event->event_name }}</p>
        <h2 class="mt-3 font-display text-4xl font-bold">Booked vendors</h2>
    </div>
    <div class="flex gap-3">
        <a class="btn-primary" href="{{ route('bookings.create',$event) }}">Add booking</a>
        <a class="btn-ghost" href="{{ route('bookings.timeline',$event) }}">Timeline</a>
    </div>
</div>

<div class="glass rounded-[2rem] p-2">
    <table class="lux-table">
        <thead>
            <tr>
                <th>Vendor</th>
                <th>Category</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $booking)
                @php $bookingId = (string) $booking->getKey(); @endphp
                <tr>
                    <td>{{ optional($booking->vendor)->business_name ?? 'Vendor' }}</td>
                    <td>{{ str(optional($booking->vendor)->category ?? 'misc')->headline() }}</td>
                    <td>{{ optional($booking->booking_date)->format('M d, Y') }}</td>
                    <td>{{ $booking->booking_time_from }} - {{ $booking->booking_time_to }}</td>
                    <td>
                        <span class="chip" style="
                            @if($booking->status === 'accepted') background: rgba(16,185,129,0.12); color: #059669; border-color: rgba(16,185,129,0.3);
                            @elseif($booking->status === 'declined') background: rgba(239,68,68,0.12); color: #dc2626; border-color: rgba(239,68,68,0.3);
                            @elseif($booking->status === 'confirmed') background: rgba(37,99,235,0.12); color: #2563eb; border-color: rgba(37,99,235,0.3);
                            @elseif($booking->status === 'negotiating') background: rgba(245,158,11,0.12); color: #d97706; border-color: rgba(245,158,11,0.3);
                            @else background: rgba(136,136,136,0.1); color: #888; border-color: rgba(136,136,136,0.2);
                            @endif
                            font-weight: 700; text-transform: uppercase;
                        ">{{ $booking->status }}</span>
                    </td>
                    <td>₹{{ number_format($booking->amount) }}</td>
                    <td>
                        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                            <button type="button" onclick="togglePlannerChat('{{ $bookingId }}')" style="background: transparent; border: none; color: #2563eb; padding: 0; font-weight: 600; font-size: 0.9rem; cursor: pointer;">💬 Chat</button>
                            <form method="POST" action="{{ route('bookings.cancel',[$event,$booking]) }}">@csrf @method('DELETE')<button style="background: transparent; border: none; color: #d9534f; padding: 0; font-weight: 600; font-size: 0.9rem; cursor: pointer;">Cancel</button></form>
                        </div>
                    </td>
                </tr>
                {{-- Inline chat row --}}
                <tr id="planner-chat-row-{{ $bookingId }}" style="display: none;">
                    <td colspan="7" style="padding: 0;">
                        <div style="border: 1px solid #d8d8d8; border-radius: 8px; overflow: hidden; margin: 8px;">
                            <div style="background: #e8e8e8; padding: 10px 16px; font-weight: 700; font-size: 0.95rem;">
                                Chat with {{ optional($booking->vendor)->business_name ?? 'Vendor' }}
                            </div>
                            <div id="planner-chat-messages-{{ $bookingId }}" style="height: 240px; overflow-y: auto; padding: 12px; background: #fff; display: flex; flex-direction: column; gap: 8px;">
                                <p class="plain-muted" style="text-align: center; margin: auto 0;">Loading messages...</p>
                            </div>
                            <div style="display: flex; gap: 8px; padding: 10px; background: #f5f5f5; border-top: 1px solid #d8d8d8;">
                                <input id="planner-chat-input-{{ $bookingId }}" type="text" placeholder="Type a message..." style="flex: 1; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;" onkeydown="if(event.key==='Enter'){sendPlannerMsg('{{ $bookingId }}');event.preventDefault();}">
                                <button onclick="sendPlannerMsg('{{ $bookingId }}')" style="background: #2563eb; color: #fff; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 700; cursor: pointer;">Send</button>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
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
            container.innerHTML = '<p class="plain-muted" style="text-align: center; margin: auto 0;">No messages yet. Start the conversation!</p>';
            return;
        }
        container.innerHTML = msgs.map(m => {
            const align = m.is_mine ? 'flex-end' : 'flex-start';
            const bg = m.is_mine ? '#dbeafe' : '#f3f4f6';
            const nameColor = m.sender_role === 'planner' ? '#2563eb' : '#059669';
            return `<div style="align-self:${align};max-width:75%;padding:8px 12px;border-radius:12px;background:${bg};">
                <strong style="font-size:0.8rem;color:${nameColor};">${m.sender_name} (${m.sender_role})</strong>
                <div style="margin-top:2px;">${escPlannerHtml(m.message)}</div>
                <small style="color:#999;font-size:0.75rem;">${m.time}</small>
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

@extends('layouts.app', ['title' => 'Vendor Dashboard - Eventra'])
@section('page-title', 'Vendor Dashboard')

@section('content')
<section class="plain-section">
    <h2>Welcome, {{ $user->name }}</h2>
    <p class="plain-muted">Manage your vendor profile and business details below.</p>
</section>

<!-- Stats -->
<section class="mobile-safe-grid plain-section">
    @foreach([
        ['Bookings', $stats['bookings'], 'Total bookings received'],
        ['Rating', number_format($stats['rating'], 1) . ' / 5', 'Average rating'],
        ['Reviews', $stats['reviews'], 'Total customer reviews'],
    ] as [$label, $value, $copy])
        <article class="stat-card plain-stat">
            <p>{{ $label }}</p>
            <strong>{{ $value }}</strong>
            <small class="plain-muted">{{ $copy }}</small>
        </article>
    @endforeach
</section>

@php
    $isProfileIncomplete = !$vendor || 
        empty($vendor->business_name) || 
        empty($vendor->budget_min) || 
        empty($vendor->speciality) || 
        empty($vendor->services_provided) || 
        empty($vendor->contact_number) || 
        empty($vendor->contact_email);
@endphp

@if($isProfileIncomplete)
    <section class="plain-section" data-reveal>
        <div style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.08), rgba(239, 68, 68, 0.08)); border: 1px solid rgba(245, 158, 11, 0.25); border-radius: 12px; padding: 20px; color: #b45309; display: flex; gap: 16px; align-items: flex-start; margin-bottom: 24px; box-shadow: 0 8px 32px 0 rgba(245, 158, 11, 0.03); backdrop-filter: blur(8px);">
            <div style="background: rgba(245, 158, 11, 0.15); border-radius: 50%; padding: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #d97706;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h4 style="margin: 0 0 6px 0; font-size: 1.15rem; font-weight: 700; color: #78350f;">Complete Your Business Profile</h4>
                <p style="margin: 0; font-size: 0.95rem; line-height: 1.5; color: #92400e;">
                    To list your services in the planner directory and receive vendor bookings, you must complete your business details. Please fill in your <strong>business name, speciality, charges range, services, contact email and phone number</strong> below.
                </p>
            </div>
        </div>
    </section>
@endif

<!-- Booking Requests Portal -->
<section class="plain-section" id="booking-requests">
    <div class="panel" style="padding: 24px;">
        <h3>Booking Requests</h3>
        <p class="plain-muted">Booking requests from event planners. Accept, decline, or negotiate.</p>

        @if($bookingRequests->isEmpty())
            <div style="text-align: center; padding: 32px 16px; color: #888;">
                <p style="font-size: 1.1rem; font-weight: 600;">No booking requests yet.</p>
                <p>When an event planner books your services, requests will appear here.</p>
            </div>
        @else
            <div style="display: grid; gap: 16px; margin-top: 16px;">
                @foreach($bookingRequests as $bReq)
                    @php
                        $bEvent = $bReq->loadedEvent;
                        $bPlanner = $bReq->loadedPlanner;
                        $bId = (string) $bReq->getKey();
                    @endphp
                    <div style="border: 1px solid #d8d8d8; border-radius: 8px; padding: 16px; background: #fafafa;">
                        {{-- Header: Event + Status --}}
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 8px;">
                            <div>
                                <strong style="font-size: 1.1rem;">{{ $bEvent->event_name ?? 'Unknown Event' }}</strong>
                                <br>
                                <small class="plain-muted">
                                    Planner: {{ $bPlanner->name ?? 'Unknown' }} &middot;
                                    {{ optional($bEvent->event_date)->format('M d, Y') ?? 'TBD' }} &middot;
                                    {{ $bEvent->venue_name ?? $bEvent->location ?? '' }}
                                </small>
                            </div>
                            <span class="chip" style="
                                @if($bReq->status === 'accepted') background: rgba(16,185,129,0.12); color: #059669; border-color: rgba(16,185,129,0.3);
                                @elseif($bReq->status === 'declined') background: rgba(239,68,68,0.12); color: #dc2626; border-color: rgba(239,68,68,0.3);
                                @elseif($bReq->status === 'confirmed') background: rgba(37,99,235,0.12); color: #2563eb; border-color: rgba(37,99,235,0.3);
                                @elseif($bReq->status === 'negotiating') background: rgba(245,158,11,0.12); color: #d97706; border-color: rgba(245,158,11,0.3);
                                @else background: rgba(136,136,136,0.1); color: #888; border-color: rgba(136,136,136,0.2);
                                @endif
                                font-weight: 700; text-transform: uppercase;
                            ">{{ $bReq->status }}</span>
                        </div>

                        {{-- Details row --}}
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-top: 12px; padding: 12px; background: #f0f0f0; border-radius: 6px;">
                            <div><small class="plain-muted">Date</small><br><strong>{{ optional($bReq->booking_date)->format('M d, Y') }}</strong></div>
                            <div><small class="plain-muted">Time</small><br><strong>{{ $bReq->booking_time_from }} – {{ $bReq->booking_time_to }}</strong></div>
                            <div><small class="plain-muted">Amount</small><br><strong>₹{{ number_format($bReq->amount) }}</strong></div>
                            @if($bReq->notes)
                                <div style="grid-column: 1 / -1;"><small class="plain-muted">Notes</small><br>{{ $bReq->notes }}</div>
                            @endif
                        </div>

                        {{-- Action buttons --}}
                        @if($bReq->status === 'pending' || $bReq->status === 'negotiating')
                            <div style="display: flex; gap: 8px; margin-top: 12px; flex-wrap: wrap;">
                                @if($bReq->status === 'pending')
                                    <form method="POST" action="{{ route('vendor.booking.respond', $bId) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="accepted">
                                        <button type="submit" style="background: #059669; color: #fff; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 700; cursor: pointer;">Accept</button>
                                    </form>
                                    <form method="POST" action="{{ route('vendor.booking.respond', $bId) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="declined">
                                        <button type="submit" style="background: #dc2626; color: #fff; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 700; cursor: pointer;">Decline</button>
                                    </form>
                                @endif
                                <button type="button" onclick="toggleChat('{{ $bId }}')" style="background: #2563eb; color: #fff; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 700; cursor: pointer;">
                                    💬 Negotiate / Chat
                                </button>
                            </div>
                        @endif

                        {{-- Chat panel (hidden by default) --}}
                        <div id="chat-{{ $bId }}" style="display: none; margin-top: 16px; border: 1px solid #d8d8d8; border-radius: 8px; overflow: hidden;">
                            <div style="background: #e8e8e8; padding: 10px 16px; font-weight: 700; font-size: 0.95rem;">Chat with {{ $bPlanner->name ?? 'Planner' }}</div>
                            <div id="chat-messages-{{ $bId }}" style="height: 240px; overflow-y: auto; padding: 12px; background: #fff; display: flex; flex-direction: column; gap: 8px;">
                                <p class="plain-muted" style="text-align: center; margin: auto 0;">Loading messages...</p>
                            </div>
                            <div style="display: flex; gap: 8px; padding: 10px; background: #f5f5f5; border-top: 1px solid #d8d8d8;">
                                <input id="chat-input-{{ $bId }}" type="text" placeholder="Type a message..." style="flex: 1; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;" onkeydown="if(event.key==='Enter'){sendChatMsg('{{ $bId }}');event.preventDefault();}">
                                <button onclick="sendChatMsg('{{ $bId }}')" style="background: #2563eb; color: #fff; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 700; cursor: pointer;">Send</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<script>
const chatIntervals = {};
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

function toggleChat(bookingId) {
    const panel = document.getElementById('chat-' + bookingId);
    if (panel.style.display === 'none') {
        panel.style.display = 'block';
        loadMessages(bookingId);
        if (!chatIntervals[bookingId]) {
            chatIntervals[bookingId] = setInterval(() => loadMessages(bookingId), 3000);
        }
    } else {
        panel.style.display = 'none';
        if (chatIntervals[bookingId]) {
            clearInterval(chatIntervals[bookingId]);
            delete chatIntervals[bookingId];
        }
    }
}

function loadMessages(bookingId) {
    fetch('/vendor-bookings/' + bookingId + '/messages', {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(msgs => {
        const container = document.getElementById('chat-messages-' + bookingId);
        if (!msgs.length) {
            container.innerHTML = '<p class="plain-muted" style="text-align: center; margin: auto 0;">No messages yet. Start the conversation!</p>';
            return;
        }
        container.innerHTML = msgs.map(m => {
            const align = m.is_mine ? 'flex-end' : 'flex-start';
            const bg = m.is_mine ? '#dbeafe' : '#f3f4f6';
            const nameColor = m.sender_role === 'vendor' ? '#059669' : '#2563eb';
            return `<div style="align-self:${align};max-width:75%;padding:8px 12px;border-radius:12px;background:${bg};">
                <strong style="font-size:0.8rem;color:${nameColor};">${m.sender_name} (${m.sender_role})</strong>
                <div style="margin-top:2px;">${escHtml(m.message)}</div>
                <small style="color:#999;font-size:0.75rem;">${m.time}</small>
            </div>`;
        }).join('');
        container.scrollTop = container.scrollHeight;
    })
    .catch(() => {});
}

function sendChatMsg(bookingId) {
    const input = document.getElementById('chat-input-' + bookingId);
    const msg = input.value.trim();
    if (!msg) return;
    input.value = '';
    fetch('/vendor-bookings/' + bookingId + '/messages', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ message: msg })
    }).then(() => loadMessages(bookingId));
}

function escHtml(s) {
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
}
</script>

<!-- Vendor Profile Form -->
<section class="plain-section">
    <div class="panel" style="padding: 24px;">
        <h3>Business Profile</h3>
        <p class="plain-muted">Fill in your business details. This information will be visible to event planners.</p>

        <form method="POST" action="{{ route('vendor.dashboard.update') }}">
            @csrf

            <div class="auth-grid">
                <div>
                    <label for="vendor-name">Your Name</label>
                    <input id="vendor-name" name="name" value="{{ old('name', $vendor->name ?? $user->name) }}" required placeholder="Your full name">
                    @error('name') <small class="otp-error">{{ $message }}</small> @enderror
                </div>
                <div>
                    <label for="vendor-business">Business Name</label>
                    <input id="vendor-business" name="business_name" value="{{ old('business_name', $vendor->business_name ?? '') }}" required placeholder="e.g. Sharma Photography">
                    @error('business_name') <small class="otp-error">{{ $message }}</small> @enderror
                </div>
                <div>
                    <label for="vendor-base">Based Location</label>
                    <input id="vendor-base" name="base_location" value="{{ old('base_location', $vendor->base_location ?? '') }}" required placeholder="e.g. Mumbai, Maharashtra">
                    @error('base_location') <small class="otp-error">{{ $message }}</small> @enderror
                </div>
                <div>
                    <label for="vendor-work">Work Location(s)</label>
                    <input id="vendor-work" name="work_location" value="{{ old('work_location', $vendor->work_location ?? '') }}" required placeholder="e.g. Mumbai, Pune, Delhi">
                    @error('work_location') <small class="otp-error">{{ $message }}</small> @enderror
                </div>
                <div>
                    <label for="vendor-budget-min">Budget Range (Min ₹) *</label>
                    <input id="vendor-budget-min" name="budget_min" type="number" step="100" min="0" value="{{ old('budget_min', $vendor->budget_min ?? '') }}" required placeholder="e.g. 5000">
                    @error('budget_min') <small class="otp-error">{{ $message }}</small> @enderror
                </div>
                <div>
                    <label for="vendor-budget-max">Budget Range (Max ₹) *</label>
                    <input id="vendor-budget-max" name="budget_max" type="number" step="100" min="0" value="{{ old('budget_max', $vendor->budget_max ?? '') }}" required placeholder="e.g. 50000">
                    @error('budget_max') <small class="otp-error">{{ $message }}</small> @enderror
                </div>
                <div>
                    <label for="vendor-category">Service Category *</label>
                    <select id="vendor-category" name="vendor_category" required>
                        <option value="">-- Select your category --</option>
                        @foreach(config('smart_budget.service_vendor_category_map', []) as $smartCat => $vendorCats)
                            @php $catLabel = config("smart_budget.services.{$smartCat}.label", ucfirst(str_replace('_', ' ', $smartCat))); @endphp
                            <option value="{{ $smartCat }}" {{ old('vendor_category', $vendor->category ?? '') === $smartCat ? 'selected' : '' }}>{{ $catLabel }}</option>
                        @endforeach
                    </select>
                    @error('vendor_category') <small class="otp-error">{{ $message }}</small> @enderror
                </div>
                <div>
                    <label for="vendor-speciality">Speciality *</label>
                    <input id="vendor-speciality" name="speciality" value="{{ old('speciality', $vendor->speciality ?? '') }}" required placeholder="e.g. Wedding Photography, Candid Shots">
                    <small class="plain-muted">Describe your specific expertise within the category</small>
                    @error('speciality') <small class="otp-error">{{ $message }}</small> @enderror
                </div>
                <div>
                    <label for="vendor-contact">Contact Number *</label>
                    <input id="vendor-contact" name="contact_number" value="{{ old('contact_number', $vendor->contact_number ?? '') }}" required placeholder="+91 98765 43210">
                    @error('contact_number') <small class="otp-error">{{ $message }}</small> @enderror
                </div>
                <div>
                    <label for="vendor-email">Contact Email *</label>
                    <input id="vendor-email" name="contact_email" type="email" value="{{ old('contact_email', $vendor->contact_email ?? $user->email) }}" required placeholder="e.g. business@example.com">
                    @error('contact_email') <small class="otp-error">{{ $message }}</small> @enderror
                </div>
                <div style="grid-column: 1 / -1;">
                    <label for="vendor-services">Services Provided *</label>
                    <input id="vendor-services" name="services_provided" value="{{ old('services_provided', is_array($vendor->services_provided ?? null) ? implode(', ', $vendor->services_provided) : ($vendor->services_provided ?? '')) }}" required placeholder="e.g. Photography, Videography, Drone Shots, Album Design">
                    <small class="plain-muted">Separate multiple services with commas</small>
                    @error('services_provided') <small class="otp-error">{{ $message }}</small> @enderror
                </div>
                <div style="grid-column: 1 / -1;">
                    <label for="vendor-desc">Description / About</label>
                    <textarea id="vendor-desc" name="description" rows="4" placeholder="Tell planners about your business, experience, and what makes you unique...">{{ old('description', $vendor->description ?? '') }}</textarea>
                    @error('description') <small class="otp-error">{{ $message }}</small> @enderror
                </div>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="auth-submit">Save Profile</button>
            </div>
        </form>
    </div>
</section>

<!-- Portfolio placeholder -->
<section class="plain-section">
    <div class="panel" style="padding: 24px;">
        <h3>Portfolio Images</h3>
        <p class="plain-muted">Portfolio image upload will be available in a future update. Your work samples will be displayed to event planners here.</p>
    </div>
</section>
@endsection

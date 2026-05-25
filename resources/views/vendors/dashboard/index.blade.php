@extends('layouts.app', ['title' => 'Vendor Dashboard - Eventra'])
@section('page-title', 'Vendor Dashboard')

@section('content')
<section class="plain-section">
    <h2>Welcome, {{ $user->name }}</h2>
    <p class="plain-muted">Manage your vendor profile and business details below.</p>
</section>

<!-- Business Switcher Panel -->
<section class="plain-section" style="margin-bottom: 24px;">
    <div class="glass-strong rounded-[2rem] p-6" style="border: 1px solid rgba(255, 255, 255, 0.08); background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(20px);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 20px;">
            <div>
                <h3 class="font-display text-2xl font-bold" style="margin: 0; color: #fff;">Your Businesses</h3>
                <p class="text-sm text-white/55" style="margin: 4px 0 0 0;">Switch between your active business profiles or add a new one.</p>
            </div>
            <a href="{{ route('vendor.dashboard', ['business_id' => 'new']) }}" class="btn-primary" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 10px 20px; font-size: 0.9rem; border-radius: 9999px;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block; vertical-align: middle;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Register New Business
            </a>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px;">
            @forelse($businesses as $biz)
                @php
                    $isActive = $vendor && (string)$vendor->getKey() === (string)$biz->getKey();
                @endphp
                <a href="{{ route('vendor.dashboard', ['business_id' => (string)$biz->getKey()]) }}" style="text-decoration: none; display: block;">
                    <div style="
                        border: 1px solid {{ $isActive ? 'rgba(0, 128, 105, 0.4)' : 'rgba(255, 255, 255, 0.08)' }}; 
                        background: {{ $isActive ? 'rgba(0, 128, 105, 0.08)' : 'rgba(255, 255, 255, 0.03)' }}; 
                        border-radius: 20px; 
                        padding: 16px; 
                        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                        cursor: pointer;
                        position: relative;
                        overflow: hidden;
                    " 
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.borderColor='{{ $isActive ? 'rgba(0, 128, 105, 0.6)' : 'rgba(255, 255, 255, 0.15)' }}'; this.style.background='{{ $isActive ? 'rgba(0, 128, 105, 0.12)' : 'rgba(255, 255, 255, 0.06)' }}';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='{{ $isActive ? 'rgba(0, 128, 105, 0.4)' : 'rgba(255, 255, 255, 0.08)' }}'; this.style.background='{{ $isActive ? 'rgba(0, 128, 105, 0.08)' : 'rgba(255, 255, 255, 0.03)' }}';">
                        
                        @if($isActive)
                            <div style="position: absolute; top: 0; right: 0; background: #008069; color: #fff; font-size: 0.7rem; font-weight: 700; padding: 4px 12px; border-bottom-left-radius: 12px; text-transform: uppercase; letter-spacing: 0.05em;">
                                Active
                            </div>
                        @endif

                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="
                                width: 44px; 
                                height: 44px; 
                                border-radius: 12px; 
                                background: {{ $isActive ? 'rgba(0, 128, 105, 0.2)' : 'rgba(255, 255, 255, 0.05)' }}; 
                                display: flex; 
                                align-items: center; 
                                justify-content: center;
                                font-size: 1.25rem;
                            ">
                                @if($biz->category === 'catering') 🍽️
                                @elseif($biz->category === 'photography') 📸
                                @elseif($biz->category === 'decoration') 🌸
                                @elseif($biz->category === 'music') 🎵
                                @elseif($biz->category === 'florist') 💐
                                @elseif($biz->category === 'venue') 🏰
                                @else 💼
                                @endif
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <h4 style="margin: 0; font-weight: 700; font-size: 1.05rem; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-right: 40px; text-align: left;">
                                    {{ $biz->business_name }}
                                </h4>
                                <p style="margin: 2px 0 0 0; font-size: 0.8rem; color: rgba(255, 255, 255, 0.45); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-align: left;">
                                    {{ ucfirst($biz->category) }} &middot; {{ $biz->base_location }}
                                </p>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 24px; color: rgba(255, 255, 255, 0.4); border: 1px dashed rgba(255, 255, 255, 0.1); border-radius: 20px;">
                    <p style="margin: 0 0 8px 0; font-size: 1rem; font-weight: 600;">No businesses registered yet.</p>
                    <p style="margin: 0; font-size: 0.85rem;">Click "Register New Business" to list your first service profile.</p>
                </div>
            @endforelse
        </div>
    </div>
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
        <h3>{{ $vendor ? 'Edit Business Profile: ' . $vendor->business_name : 'Register New Business Profile' }}</h3>
        <p class="plain-muted">{{ $vendor ? 'Update details for your selected business. This will instantly refresh your showcase and bookings portal.' : 'Enter details to register another business profile under your account.' }}</p>

        <form method="POST" action="{{ route('vendor.dashboard.update') }}">
            @csrf
            <input type="hidden" name="vendor_id" value="{{ $vendor ? (string)$vendor->getKey() : 'new' }}">

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

<!-- Showcase Portfolio Gallery -->
<style>
    .gallery-upload-zone {
        border: 2px dashed rgba(0, 128, 105, 0.25);
        border-radius: 1rem;
        background: rgba(0, 128, 105, 0.02);
        padding: 30px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.25s ease;
        margin-bottom: 24px;
    }
    .gallery-upload-zone:hover, .gallery-upload-zone.dragover {
        border-color: #008069;
        background: rgba(0, 128, 105, 0.05);
    }
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 16px;
    }
    .gallery-item {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        aspect-ratio: 3/2;
        border: 1px solid #d8d8d8;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        group: hover;
    }
    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
        cursor: pointer;
    }
    .gallery-item:hover img {
        transform: scale(1.06);
    }
    .gallery-item-delete {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(239, 68, 68, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        opacity: 0;
        transition: opacity 0.2s ease, background 0.2s ease;
        font-weight: 800;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    .gallery-item:hover .gallery-item-delete {
        opacity: 1;
    }
    .gallery-item-delete:hover {
        background: rgba(220, 38, 38, 1);
    }
    
    /* Lightbox Modal styles */
    .lightbox-modal {
        display: none;
        position: fixed;
        z-index: 99999;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.9);
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(5px);
    }
    .lightbox-content {
        max-width: 90%;
        max-height: 80%;
        border-radius: 8px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    }
    .lightbox-close {
        position: absolute;
        top: 24px;
        right: 24px;
        color: white;
        font-size: 2.5rem;
        font-weight: 300;
        cursor: pointer;
        user-select: none;
    }
</style>

<section class="plain-section" id="portfolio-gallery-section">
    <div class="panel" style="padding: 24px;">
        <h3>Business Portfolio Showcase</h3>
        <p class="plain-muted" style="margin-bottom: 20px;">Upload samples of your best work (decoration designs, photography, catering spreads, venue halls) to wow event planners.</p>

        @if($vendor)
            <!-- Multi uploader -->
            <div id="gallery-drop-zone" class="gallery-upload-zone" onclick="document.getElementById('gallery-images-input').click()">
                <span style="font-size: 2.2rem; display: block; margin-bottom: 8px;">📸</span>
                <span style="font-weight: 700; font-size: 0.95rem; color: #111b21;">Drag & Drop work photos here, or click to browse</span>
                <span style="font-size: 0.75rem; display: block; color: #667781; margin-top: 4px;">Supports JPG, PNG, and WEBP up to 5MB each (upload multiple at once)</span>
                <input type="file" id="gallery-images-input" multiple style="display: none;" accept="image/*" onchange="handleGallerySelect(event)">
            </div>

            <!-- Upload progress -->
            <div id="gallery-progress-container" style="display: none; margin-bottom: 20px; background: #f0f0f0; border-radius: 8px; padding: 12px; border: 1px solid #d8d8d8;">
                <div style="display: flex; justify-content: space-between; font-size: 0.82rem; margin-bottom: 6px; font-weight: 700;">
                    <span id="gallery-progress-text" style="color: #008069;">Uploading showcase files...</span>
                    <span id="gallery-progress-percent">0%</span>
                </div>
                <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                    <div id="gallery-progress-fill" style="width: 0%; height: 100%; background: #008069; transition: width 0.15s ease;"></div>
                </div>
            </div>

            <!-- Showcase Gallery Grid -->
            <div class="gallery-grid" id="vendor-showcase-grid">
                @php
                    $uploadedImages = $vendor->galleryImages;
                @endphp
                
                @if($uploadedImages->isEmpty())
                    <div id="empty-gallery-state" style="grid-column: 1 / -1; text-align: center; padding: 32px 16px; color: #888; border: 1px dashed #d8d8d8; border-radius: 12px; background: #fafafa;">
                        <p style="font-size: 1.05rem; font-weight: 600; margin-bottom: 4px;">Your showcase portfolio is empty.</p>
                        <p style="font-size: 0.85rem; margin: 0;">Drag and drop work photos above to start building your gallery.</p>
                    </div>
                @else
                    @foreach($uploadedImages as $gImg)
                        <div class="gallery-item" id="gallery-item-{{ $gImg->id }}">
                            <img src="{{ asset('storage/' . $gImg->image_path) }}" alt="Showcase Image" onclick="openLightbox(this.src)">
                            <button type="button" class="gallery-item-delete" title="Delete image" onclick="deleteGalleryImage('{{ $gImg->id }}')">×</button>
                        </div>
                    @endforeach
                @endif
            </div>
        @else
            <div style="text-align: center; padding: 24px; border: 1px dashed #d97706; background: rgba(245, 158, 11, 0.04); border-radius: 12px; color: #b45309;">
                <strong>Please fill and save your Business Profile details first before uploading portfolio showcase images.</strong>
            </div>
        @endif
    </div>
</section>

<!-- Lightbox Modal -->
<div id="gallery-lightbox" class="lightbox-modal" onclick="closeLightbox()">
    <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
    <img class="lightbox-content" id="lightbox-image" src="" alt="Showcase Preview" onclick="event.stopPropagation()">
</div>

<script>
    const galleryDropZone = document.getElementById('gallery-drop-zone');
    const galleryCsrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    if (galleryDropZone) {
        // Drag and drop events
        ['dragenter', 'dragover'].forEach(eventName => {
            galleryDropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                galleryDropZone.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            galleryDropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                galleryDropZone.classList.remove('dragover');
            }, false);
        });

        galleryDropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                uploadGalleryImages(files);
            }
        }, false);
    }

    function handleGallerySelect(event) {
        const files = event.target.files;
        if (files.length > 0) {
            uploadGalleryImages(files);
        }
    }

    /**
     * AJAX Upload Showcase Images.
     */
    function uploadGalleryImages(files) {
        const formData = new FormData();
        let imageCount = 0;

        for (let i = 0; i < files.length; i++) {
            if (files[i].type.match('image.*')) {
                formData.append('images[]', files[i]);
                imageCount++;
            }
        }

        if (imageCount === 0) {
            alert('Please select valid work image files (JPG, PNG, or WEBP).');
            return;
        }

        const progContainer = document.getElementById('gallery-progress-container');
        const progFill = document.getElementById('gallery-progress-fill');
        const progText = document.getElementById('gallery-progress-text');
        const progPercent = document.getElementById('gallery-progress-percent');

        progContainer.style.display = 'block';
        progFill.style.width = '0%';
        progPercent.textContent = '0%';
        progText.textContent = `Uploading ${imageCount} showcase photo(s)...`;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route("vendor.gallery.upload") }}', true);
        xhr.setRequestHeader('X-CSRF-TOKEN', galleryCsrf);
        xhr.setRequestHeader('Accept', 'application/json');

        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                progFill.style.width = percent + '%';
                progPercent.textContent = percent + '%';
            }
        };

        xhr.onload = function() {
            progContainer.style.display = 'none';
            if (xhr.status === 200) {
                try {
                    const res = JSON.parse(xhr.responseText);
                    if (res.success && res.images) {
                        const grid = document.getElementById('vendor-showcase-grid');
                        const emptyState = document.getElementById('empty-gallery-state');
                        
                        if (emptyState) emptyState.remove();

                        res.images.forEach(img => {
                            const item = document.createElement('div');
                            item.className = 'gallery-item';
                            item.id = `gallery-item-${img.id}`;
                            item.innerHTML = `
                                <img src="${img.url}" alt="Showcase Image" onclick="openLightbox(this.src)">
                                <button type="button" class="gallery-item-delete" title="Delete image" onclick="deleteGalleryImage('${img.id}')">×</button>
                            `;
                            grid.appendChild(item);
                        });
                    }
                } catch(e) {
                    alert('Upload was successful but failed to parse responses.');
                }
            } else {
                try {
                    const response = JSON.parse(xhr.responseText);
                    alert(response.message || 'Failed to upload portfolio images.');
                } catch(e) {
                    alert('An error occurred during portfolio upload.');
                }
            }
        };

        xhr.onerror = function() {
            progContainer.style.display = 'none';
            alert('An error occurred during upload.');
        };

        xhr.send(formData);
    }

    /**
     * AJAX Delete Showcase Image.
     */
    function deleteGalleryImage(id) {
        if (!confirm('Are you sure you want to delete this showcase image from your portfolio?')) return;

        fetch(`/vendor/gallery/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': galleryCsrf,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const item = document.getElementById(`gallery-item-${id}`);
                if (item) {
                    item.remove();
                }

                // If grid is empty, show empty state
                const grid = document.getElementById('vendor-showcase-grid');
                if (grid && grid.querySelectorAll('.gallery-item').length === 0) {
                    grid.innerHTML = `
                        <div id="empty-gallery-state" style="grid-column: 1 / -1; text-align: center; padding: 32px 16px; color: #888; border: 1px dashed #d8d8d8; border-radius: 12px; background: #fafafa;">
                            <p style="font-size: 1.05rem; font-weight: 600; margin-bottom: 4px;">Your showcase portfolio is empty.</p>
                            <p style="font-size: 0.85rem; margin: 0;">Drag and drop work photos above to start building your gallery.</p>
                        </div>
                    `;
                }
            } else {
                alert(data.error || 'Failed to delete portfolio image.');
            }
        })
        .catch(() => {
            alert('An error occurred.');
        });
    }

    /* ── Lightbox Logic ── */
    function openLightbox(src) {
        const lightbox = document.getElementById('gallery-lightbox');
        const img = document.getElementById('lightbox-image');
        img.src = src;
        lightbox.style.display = 'flex';
    }

    function closeLightbox() {
        document.getElementById('gallery-lightbox').style.display = 'none';
    }
</script>
@endsection

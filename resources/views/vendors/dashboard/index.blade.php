@extends('layouts.app', ['title' => 'Vendor Dashboard - Eventra'])
@section('page-title', 'Vendor Dashboard')

@section('content')
<div class="space-y-8 pb-12">
    {{-- Header section --}}
    <div class="mb-8" data-animate="fade-up">
        <h2 class="text-h2 font-extrabold text-neutral-dark">Welcome back, {{ $user->name }}!</h2>
        <p class="text-body text-surface-500 mt-1">Manage your vendor profile and business details below.</p>
    </div>

    {{-- Business Switcher Panel --}}
    <x-card class="relative" data-animate="fade-up">
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <div>
                <h3 class="text-h3 font-bold text-neutral-dark">Your Businesses</h3>
                <p class="text-caption text-surface-500 mt-1">Switch between your active business profiles or add a new one.</p>
            </div>
            <x-btn href="{{ route('vendor.dashboard', ['business_id' => 'new']) }}" icon="plus" size="sm">
                Register New Business
            </x-btn>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($businesses as $biz)
                @php
                    $isActive = $vendor && (string)$vendor->getKey() === (string)$biz->getKey();
                @endphp
                <a href="{{ route('vendor.dashboard', ['business_id' => (string)$biz->getKey()]) }}" class="block group">
                    <div class="p-4 rounded-md border transition-all duration-200 relative overflow-hidden h-full flex flex-col justify-between
                        {{ $isActive ? 'border-success bg-success/5 shadow-sm' : 'border-surface-200 bg-white hover:-translate-y-0.5 hover:shadow-sm hover:border-primary-200' }}">
                        
                        @if($isActive)
                            <div class="absolute top-0 right-0 bg-success text-white text-[10px] font-bold px-3 py-1 rounded-bl-md uppercase tracking-wider">
                                Active
                            </div>
                        @endif

                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-sm flex items-center justify-center text-2xl
                                {{ $isActive ? 'bg-success/15' : 'bg-surface-100' }}">
                                @if($biz->category === 'catering') 🍽️
                                @elseif($biz->category === 'photography') 📸
                                @elseif($biz->category === 'decoration') 🌸
                                @elseif($biz->category === 'music') 🎵
                                @elseif($biz->category === 'florist') 💐
                                @elseif($biz->category === 'venue') 🏰
                                @else 💼
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-body text-neutral-dark truncate pr-10">
                                    {{ $biz->business_name }}
                                </h4>
                                <p class="text-caption text-surface-500 mt-0.5 truncate">
                                    {{ ucfirst($biz->category) }} &middot; {{ $biz->base_location }}
                                </p>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full border border-dashed border-surface-200 rounded-md p-8 text-center text-surface-400">
                    <p class="font-bold text-body mb-1">No businesses registered yet.</p>
                    <p class="text-caption">Click "Register New Business" to list your first service profile.</p>
                </div>
            @endforelse
        </div>
    </x-card>

    {{-- Stats cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" data-animate="stagger">
        <x-stat-card label="Bookings" value="{{ $stats['bookings'] }}" sub="Total bookings received" icon="📅" />
        <x-stat-card label="Rating" value="{{ number_format($stats['rating'], 1) }} / 5" sub="Average rating" icon="⭐" />
        <x-stat-card label="Reviews" value="{{ $stats['reviews'] }}" sub="Total customer reviews" icon="💬" />
    </div>

    @php
        $isProfileIncomplete = !$vendor || 
            empty($vendor->business_name) || 
            empty($vendor->budget_min) || 
            empty($vendor->speciality) || 
            empty($vendor->services_provided) || 
            empty($vendor->contact_number) || 
            empty($vendor->contact_email);
    @endphp

    {{-- Profile warning --}}
    @if($isProfileIncomplete)
        <div class="p-5 rounded-md border border-warning/30 bg-warning/5 flex gap-4 items-start" data-animate="fade-up">
            <div class="w-10 h-10 rounded-full bg-warning/15 flex items-center justify-center flex-shrink-0 text-warning-dark">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h4 class="font-bold text-body-lg text-warning-dark mb-1">Complete Your Business Profile</h4>
                <p class="text-body text-surface-600 leading-relaxed">
                    To list your services in the planner directory and receive vendor bookings, you must complete your business details. Please fill in your <strong>business name, speciality, charges range, services, contact email and phone number</strong> below.
                </p>
            </div>
        </div>
    @endif

    {{-- Booking Requests Portal --}}
    <x-card id="booking-requests" data-animate="fade-up">
        <h3 class="text-h3 font-bold text-neutral-dark">Booking Requests</h3>
        <p class="text-caption text-surface-500 mt-1 mb-6">Booking requests from event planners. Accept, decline, or negotiate.</p>

        @if($bookingRequests->isEmpty())
            <x-empty-state title="No booking requests yet" description="When an event planner books your services, requests will appear here." icon="📥" />
        @else
            <div class="space-y-4">
                @foreach($bookingRequests as $bReq)
                    @php
                        $bEvent = $bReq->loadedEvent;
                        $bPlanner = $bReq->loadedPlanner;
                        $bId = (string) $bReq->getKey();
                    @endphp
                    <div class="border border-surface-200 rounded-md p-5 bg-white hover:shadow-xs transition-shadow">
                        {{-- Header: Event + Status --}}
                        <div class="flex justify-between items-start flex-wrap gap-4">
                           <div>
                               <h4 class="font-bold text-body-lg text-neutral-dark">{{ $bEvent->event_name ?? 'Unknown Event' }}</h4>
                               <p class="text-caption text-surface-500 mt-1">
                                   Planner: <span class="font-medium text-neutral-dark">{{ $bPlanner->name ?? 'Unknown' }}</span> &middot;
                                   {{ optional($bEvent->event_date)->format('M d, Y') ?? 'TBD' }} &middot;
                                   {{ $bEvent->venue_name ?? $bEvent->location ?? '' }}
                               </p>
                           </div>
                           @php
                               $statusVariant = match($bReq->status) {
                                   'accepted' => 'success',
                                   'declined' => 'danger',
                                   'confirmed' => 'info',
                                   'negotiating' => 'warning',
                                   default => 'gray',
                               };
                           @endphp
                           <x-badge :variant="$statusVariant" class="uppercase text-[10px] tracking-wider">{{ $bReq->status }}</x-badge>
                        </div>

                        {{-- Details row --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 p-4 bg-surface-50 rounded-sm">
                           <div>
                               <p class="text-caption text-surface-400 font-medium">Date</p>
                               <p class="text-body font-bold text-neutral-dark mt-0.5">{{ optional($bReq->booking_date)->format('M d, Y') }}</p>
                           </div>
                           <div>
                               <p class="text-caption text-surface-400 font-medium">Time</p>
                               <p class="text-body font-bold text-neutral-dark mt-0.5">{{ $bReq->booking_time_from }} – {{ $bReq->booking_time_to }}</p>
                           </div>
                           <div>
                               <p class="text-caption text-surface-400 font-medium">Amount</p>
                               <p class="text-body font-bold text-neutral-dark mt-0.5">₹{{ number_format($bReq->amount) }}</p>
                           </div>
                           @if($bReq->notes)
                               <div class="col-span-full sm:col-span-1">
                                   <p class="text-caption text-surface-400 font-medium">Notes</p>
                                   <p class="text-body text-surface-600 mt-0.5 truncate" title="{{ $bReq->notes }}">{{ $bReq->notes }}</p>
                               </div>
                           @endif
                        </div>

                        {{-- Action buttons --}}
                        @if($bReq->status === 'pending' || $bReq->status === 'negotiating')
                           <div class="flex flex-wrap gap-3 mt-4">
                               @if($bReq->status === 'pending')
                                   <form method="POST" action="{{ route('vendor.booking.respond', $bId) }}" class="inline">
                                       @csrf
                                       <input type="hidden" name="action" value="accepted">
                                       <x-btn type="submit" size="sm">Accept</x-btn>
                                   </form>
                                   <form method="POST" action="{{ route('vendor.booking.respond', $bId) }}" class="inline">
                                       @csrf
                                       <input type="hidden" name="action" value="declined">
                                       <x-btn type="submit" variant="danger" size="sm">Decline</x-btn>
                                   </form>
                               @endif
                               <x-btn type="button" variant="outline" size="sm" onclick="toggleChat('{{ $bId }}')" icon="message">
                                   Negotiate / Chat
                               </x-btn>
                           </div>
                        @endif

                        {{-- Chat panel (hidden by default) --}}
                        <div id="chat-{{ $bId }}" style="display: none;" class="mt-4 border border-surface-200 rounded-md overflow-hidden bg-white shadow-xs">
                           <div class="bg-surface-100 px-4 py-3 border-b border-surface-200 flex justify-between items-center">
                               <span class="font-bold text-body text-neutral-dark">Chat with {{ $bPlanner->name ?? 'Planner' }}</span>
                               <button onclick="toggleChat('{{ $bId }}')" class="text-surface-400 hover:text-neutral-dark">
                                   <x-icon name="x" class="w-4 h-4" />
                               </button>
                           </div>
                           <div id="chat-messages-{{ $bId }}" class="h-64 overflow-y-auto p-4 flex flex-col gap-3 bg-surface-50/50">
                               <p class="text-caption text-surface-400 text-center my-auto">Loading messages...</p>
                           </div>
                           <div class="flex gap-2 p-3 bg-white border-t border-surface-200">
                               <input id="chat-input-{{ $bId }}" type="text" placeholder="Type a message..." class="flex-1 px-3 py-2 border border-surface-200 rounded-sm text-body focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500/20" onkeydown="if(event.key==='Enter'){sendChatMsg('{{ $bId }}');event.preventDefault();}">
                               <x-btn onclick="sendChatMsg('{{ $bId }}')" size="sm">Send</x-btn>
                           </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>

    {{-- Vendor Profile Form --}}
    <x-card data-animate="fade-up">
        <h3 class="text-h3 font-bold text-neutral-dark">{{ $vendor ? 'Edit Business Profile: ' . $vendor->business_name : 'Register New Business Profile' }}</h3>
        <p class="text-caption text-surface-500 mt-1 mb-6">{{ $vendor ? 'Update details for your selected business. This will instantly refresh your showcase and bookings portal.' : 'Enter details to register another business profile under your account.' }}</p>

        <form method="POST" action="{{ route('vendor.dashboard.update') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="vendor_id" value="{{ $vendor ? (string)$vendor->getKey() : 'new' }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input label="Your Name" name="name" :value="old('name', $vendor->name ?? $user->name)" required placeholder="Your full name" :error="$errors->first('name')" />
                <x-input label="Business Name" name="business_name" :value="old('business_name', $vendor->business_name ?? '')" required placeholder="e.g. Sharma Photography" :error="$errors->first('business_name')" />
                <x-input label="Based Location" name="base_location" :value="old('base_location', $vendor->base_location ?? '')" required placeholder="e.g. Mumbai, Maharashtra" :error="$errors->first('base_location')" />
                <x-input label="Work Location(s)" name="work_location" :value="old('work_location', $vendor->work_location ?? '')" required placeholder="e.g. Mumbai, Pune, Delhi" :error="$errors->first('work_location')" />
                <x-input type="number" label="Budget Range (Min ₹)" name="budget_min" :value="old('budget_min', $vendor->budget_min ?? '')" required placeholder="e.g. 5000" :error="$errors->first('budget_min')" />
                <x-input type="number" label="Budget Range (Max ₹)" name="budget_max" :value="old('budget_max', $vendor->budget_max ?? '')" required placeholder="e.g. 50000" :error="$errors->first('budget_max')" />
                
                {{-- Category selection --}}
                <div class="space-y-1.5">
                    <label for="vendor-category" class="block text-body font-medium text-surface-700">
                        Service Category <span class="text-danger">*</span>
                    </label>
                    <select id="vendor-category" name="vendor_category" required class="input">
                        <option value="">-- Select your category --</option>
                        @foreach(config('smart_budget.service_vendor_category_map', []) as $smartCat => $vendorCats)
                            @php $catLabel = config("smart_budget.services.{$smartCat}.label", ucfirst(str_replace('_', ' ', $smartCat))); @endphp
                            <option value="{{ $smartCat }}" {{ old('vendor_category', $vendor->category ?? '') === $smartCat ? 'selected' : '' }}>{{ $catLabel }}</option>
                        @endforeach
                    </select>
                    @error('vendor_category') <p class="text-caption text-danger mt-1">{{ $message }}</p> @enderror
                </div>

                <x-input label="Speciality" name="speciality" :value="old('speciality', $vendor->speciality ?? '')" required placeholder="e.g. Wedding Photography, Candid Shots" :error="$errors->first('speciality')" />
                <x-input label="Contact Number" name="contact_number" :value="old('contact_number', $vendor->contact_number ?? '')" required placeholder="+91 98765 43210" :error="$errors->first('contact_number')" />
                <x-input type="email" label="Contact Email" name="contact_email" :value="old('contact_email', $vendor->contact_email ?? $user->email)" required placeholder="e.g. business@example.com" :error="$errors->first('contact_email')" />
                
                <div class="md:col-span-2">
                    <x-input label="Services Provided (comma separated)" name="services_provided" :value="old('services_provided', is_array($vendor->services_provided ?? null) ? implode(', ', $vendor->services_provided) : ($vendor->services_provided ?? ''))" required placeholder="e.g. Photography, Videography, Drone Shots, Album Design" :error="$errors->first('services_provided')" />
                </div>
                
                <div class="md:col-span-2">
                    <x-input type="textarea" label="Description / About" name="description" :value="old('description', $vendor->description ?? '')" placeholder="Tell planners about your business, experience, and what makes you unique..." rows="4" :error="$errors->first('description')" />
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <x-btn type="submit">Save Profile</x-btn>
            </div>
        </form>
    </x-card>

    {{-- Showcase Portfolio Gallery --}}
    <x-card id="portfolio-gallery-section" data-animate="fade-up">
        <h3 class="text-h3 font-bold text-neutral-dark">Business Portfolio Showcase</h3>
        <p class="text-caption text-surface-500 mt-1 mb-6">Upload samples of your best work (decoration designs, photography, catering spreads, venue halls) to wow event planners.</p>

        @if($vendor)
            {{-- Multi uploader drag & drop zone --}}
            <div id="gallery-drop-zone" class="border-2 border-dashed border-primary-200 hover:border-primary-500 hover:bg-primary-50/10 rounded-md p-8 text-center cursor-pointer transition-all duration-200" onclick="document.getElementById('gallery-images-input').click()">
                <span class="text-4xl block mb-3">📸</span>
                <span class="block font-bold text-body text-neutral-dark">Drag & Drop work photos here, or click to browse</span>
                <span class="block text-caption text-surface-400 mt-1">Supports JPG, PNG, and WEBP up to 5MB each (upload multiple at once)</span>
                <input type="file" id="gallery-images-input" multiple class="hidden" accept="image/*" onchange="handleGallerySelect(event)">
            </div>

            {{-- Upload progress --}}
            <div id="gallery-progress-container" style="display: none;" class="mt-4 bg-white border border-surface-200 rounded-md p-4 shadow-xs">
                <div class="flex justify-between text-caption font-bold mb-2">
                    <span id="gallery-progress-text" class="text-primary-600">Uploading showcase files...</span>
                    <span id="gallery-progress-percent">0%</span>
                </div>
                <div class="w-full h-2 bg-surface-100 rounded-full overflow-hidden">
                    <div id="gallery-progress-fill" class="w-0 h-full bg-primary-500 transition-all duration-150"></div>
                </div>
            </div>

            {{-- Showcase Gallery Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mt-8" id="vendor-showcase-grid">
                @php
                    $uploadedImages = $vendor->galleryImages;
                @endphp
                
                @if($uploadedImages->isEmpty())
                    <div id="empty-gallery-state" class="col-span-full border border-dashed border-surface-200 rounded-md p-8 text-center text-surface-400">
                        <p class="font-bold text-body mb-1">Your showcase portfolio is empty.</p>
                        <p class="text-caption">Drag and drop work photos above to start building your gallery.</p>
                    </div>
                @else
                    @foreach($uploadedImages as $gImg)
                        <div class="gallery-item relative rounded-md overflow-hidden aspect-[3/2] border border-surface-200 group" id="gallery-item-{{ $gImg->id }}">
                            <img src="{{ asset('storage/' . $gImg->image_path) }}" alt="Showcase Image" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105 cursor-pointer" onclick="openLightbox(this.src)">
                            <button type="button" class="absolute top-2 right-2 bg-danger hover:bg-danger-dark text-white rounded-full w-7 h-7 flex items-center justify-center shadow-md transition-opacity duration-200 md:opacity-0 md:group-hover:opacity-100 font-bold" title="Delete image" onclick="deleteGalleryImage('{{ $gImg->id }}')">×</button>
                        </div>
                    @endforeach
                @endif
            </div>
        @else
            <div class="p-5 rounded-md border border-warning/30 bg-warning/5 text-warning-dark text-center font-bold">
                Please fill and save your Business Profile details first before uploading portfolio showcase images.
            </div>
        @endif
    </x-card>
</div>

{{-- Lightbox Modal --}}
<div id="gallery-lightbox" style="display: none;" class="fixed inset-0 z-[100] bg-neutral-dark/95 backdrop-blur-xs flex items-center justify-center" onclick="closeLightbox()">
    <span class="absolute top-6 right-6 text-white text-4xl font-light cursor-pointer hover:text-surface-300" onclick="closeLightbox()">&times;</span>
    <img class="max-w-[90%] max-h-[85%] rounded-sm shadow-lg" id="lightbox-image" src="" alt="Showcase Preview" onclick="event.stopPropagation()">
</div>

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
            container.innerHTML = '<p class="text-caption text-surface-400 text-center my-auto">No messages yet. Start the conversation!</p>';
            return;
        }
        container.innerHTML = msgs.map(m => {
            const align = m.is_mine ? 'self-end bg-primary-50 text-neutral-dark' : 'self-start bg-surface-100 text-neutral-dark';
            const nameColor = m.sender_role === 'vendor' ? 'text-success' : 'text-primary-500';
            return `<div class="${align} max-w-[75%] px-3 py-2 rounded-md shadow-xs flex flex-col gap-0.5">
                <span class="text-[10px] font-bold ${nameColor}">${m.sender_name} (${m.sender_role})</span>
                <span class="text-body">${escHtml(m.message)}</span>
                <span class="text-[9px] text-surface-400 self-end mt-0.5">${m.time}</span>
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

<script>
    const galleryDropZone = document.getElementById('gallery-drop-zone');
    const galleryCsrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    if (galleryDropZone) {
        // Drag and drop events
        ['dragenter', 'dragover'].forEach(eventName => {
            galleryDropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                galleryDropZone.classList.add('border-primary-500', 'bg-primary-50/10');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            galleryDropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                galleryDropZone.classList.remove('border-primary-500', 'bg-primary-50/10');
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
                            item.className = 'gallery-item relative rounded-md overflow-hidden aspect-[3/2] border border-surface-200 group';
                            item.id = `gallery-item-${img.id}`;
                            item.innerHTML = `
                                <img src="${img.url}" alt="Showcase Image" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105 cursor-pointer" onclick="openLightbox(this.src)">
                                <button type="button" class="absolute top-2 right-2 bg-danger hover:bg-danger-dark text-white rounded-full w-7 h-7 flex items-center justify-center shadow-md transition-opacity duration-200 md:opacity-0 md:group-hover:opacity-100 font-bold" title="Delete image" onclick="deleteGalleryImage('${img.id}')">×</button>
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
                        <div id="empty-gallery-state" class="col-span-full border border-dashed border-surface-200 rounded-md p-8 text-center text-surface-400">
                            <p class="font-bold text-body mb-1">Your showcase portfolio is empty.</p>
                            <p class="text-caption">Drag and drop work photos above to start building your gallery.</p>
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

    // Exported function for onclick handler
    window.openLightbox = openLightbox;

    function closeLightbox() {
        document.getElementById('gallery-lightbox').style.display = 'none';
    }
    
    // Exported function for onclick handler
    window.closeLightbox = closeLightbox;
</script>
@endsection

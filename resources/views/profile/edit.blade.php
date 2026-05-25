@extends('layouts.app', ['title' => 'Profile — Eventra'])

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="border-b border-surface-100 pb-4">
        <h1 class="text-h2 font-extrabold text-neutral-dark">Profile Settings</h1>
        <p class="text-body text-surface-500 mt-1">Manage your account information, company parameters, and profile avatar.</p>
    </div>

    {{-- Content Layout --}}
    <div class="grid gap-6 lg:grid-cols-3">
        
        {{-- Left: Profile Photo Card (1 col) --}}
        <div>
            <x-card class="flex flex-col items-center text-center space-y-6" data-animate="fade-up">
                <h3 class="text-body-lg font-bold text-neutral-dark w-full text-left border-b border-surface-100 pb-2">Profile Photo</h3>
                
                {{-- Circular Avatar Preview --}}
                <div class="relative">
                    <img 
                        id="avatar-preview-element" 
                        src="{{ auth()->user()->avatar_url }}" 
                        alt="Profile Photo" 
                        class="h-40 w-40 rounded-full object-cover ring-4 ring-primary-500/20 shadow-md transition-all duration-300"
                    >
                </div>

                <div>
                    <h2 class="text-h3 font-extrabold text-neutral-dark">{{ auth()->user()->name }}</h2>
                    <p class="text-caption font-semibold text-primary-500 uppercase tracking-wider mt-1">{{ ucfirst(auth()->user()->role) }} Account</p>
                </div>

                {{-- Drag & Drop Zone --}}
                <div class="w-full space-y-4">
                    <div 
                        id="avatar-drop-zone" 
                        onclick="document.getElementById('profile-photo-input').click()"
                        class="border-2 border-dashed border-primary-350 bg-primary-50/10 hover:bg-primary-50/20 hover:border-primary-500 p-6 rounded-md text-center cursor-pointer transition-all duration-200"
                    >
                        <span class="text-2xl block mb-2">📸</span>
                        <span class="text-body font-bold text-neutral-dark block">Drag & Drop or Click</span>
                        <span class="text-[10px] text-surface-400 font-semibold block mt-1">JPG, PNG, or WEBP up to 2MB</span>
                        <input type="file" id="profile-photo-input" class="hidden" accept="image/*" onchange="handleFileSelect(event)">
                    </div>

                    {{-- Upload Progress --}}
                    <div id="upload-progress-container" class="w-full bg-surface-100 rounded-full h-1.5 hidden overflow-hidden">
                        <div id="upload-progress-fill" class="bg-primary-500 h-full w-0 transition-all duration-150 ease-out"></div>
                    </div>

                    <div class="flex gap-2">
                        <button 
                            type="button" 
                            onclick="document.getElementById('profile-photo-input').click()"
                            class="btn-outline flex-1 py-2 text-xs"
                        >
                            Upload New
                        </button>
                        
                        <button 
                            type="button" 
                            id="remove-avatar-btn" 
                            onclick="removeAvatar()"
                            class="btn-ghost flex-1 py-2 text-xs text-danger border border-danger-200 hover:bg-danger-50 font-bold"
                            style="{{ auth()->user()->profile_photo ? '' : 'display: none;' }}"
                        >
                            Remove
                        </button>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Right: Account Details Form (2 cols) --}}
        <div class="lg:col-span-2">
            <x-card class="space-y-5" data-animate="fade-up">
                <h3 class="text-h3 font-bold text-neutral-dark border-b border-surface-100 pb-2">Account Details</h3>

                <form method="POST" action="{{ route('profile.update') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @csrf

                    {{-- Company Name --}}
                    <x-input 
                        label="Company Name" 
                        name="company_name" 
                        type="text" 
                        placeholder="e.g. Dream Weddings Inc." 
                        :value="old('company_name', $profile->company_name)" 
                        :error="$errors->first('company_name')" 
                    />

                    {{-- Phone --}}
                    <x-input 
                        label="Phone Number" 
                        name="phone" 
                        type="text" 
                        placeholder="+91 98765 43210" 
                        :value="old('phone', $profile->phone ?? auth()->user()->phone)" 
                        :error="$errors->first('phone')" 
                    />

                    {{-- Location --}}
                    <x-input 
                        label="Location / City" 
                        name="location" 
                        type="text" 
                        placeholder="e.g. Mumbai, Maharashtra" 
                        :value="old('location', $profile->location)" 
                        :error="$errors->first('location')" 
                    />

                    {{-- Website --}}
                    <x-input 
                        label="Website URL" 
                        name="website" 
                        type="url" 
                        placeholder="https://example.com" 
                        :value="old('website', $profile->website)" 
                        :error="$errors->first('website')" 
                    />

                    {{-- Bio --}}
                    <div class="sm:col-span-2">
                        <x-input 
                            label="Bio / Profile Summary" 
                            name="bio" 
                            type="textarea" 
                            placeholder="Tell us about yourself or your business... (Max 600 characters)" 
                            rows="5"
                            :value="old('bio', $profile->bio)" 
                            :error="$errors->first('bio')" 
                        />
                    </div>

                    {{-- Save button --}}
                    <div class="sm:col-span-2 border-t border-surface-100 pt-4 flex justify-end">
                        <button type="submit" class="btn-primary py-2.5 px-8">
                            Save Profile Details
                        </button>
                    </div>
                </form>
            </x-card>
        </div>

    </div>
</div>

<script>
    const dropZone = document.getElementById('avatar-drop-zone');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // Drag and drop events
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropZone.classList.add('border-primary-500', 'bg-primary-50/30');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-primary-500', 'bg-primary-50/30');
        }, false);
    });

    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            uploadProfilePhoto(files[0]);
        }
    }, false);

    function handleFileSelect(event) {
        const files = event.target.files;
        if (files.length > 0) {
            uploadProfilePhoto(files[0]);
        }
    }

    function uploadProfilePhoto(file) {
        if (!file.type.match('image.*')) {
            alert('Please select a valid image file (JPG, PNG, or WEBP).');
            return;
        }

        const formData = new FormData();
        formData.append('profile_photo', file);

        const progressContainer = document.getElementById('upload-progress-container');
        const progressBarFill = document.getElementById('upload-progress-fill');
        
        progressContainer.classList.remove('hidden');
        progressBarFill.style.width = '0%';

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route("profile.photo.upload") }}', true);
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        xhr.setRequestHeader('Accept', 'application/json');

        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBarFill.style.width = percentComplete + '%';
            }
        };

        xhr.onload = function() {
            progressContainer.classList.add('hidden');
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    document.getElementById('avatar-preview-element').src = response.url;
                    
                    const sidebarAvatar = document.getElementById('sidebar-avatar');
                    if (sidebarAvatar) sidebarAvatar.src = response.url;
                    
                    const headerAvatar = document.getElementById('header-avatar');
                    if (headerAvatar) headerAvatar.src = response.url;

                    document.getElementById('remove-avatar-btn').style.display = 'block';
                }
            } else {
                try {
                    const response = JSON.parse(xhr.responseText);
                    alert(response.message || 'Failed to upload profile photo.');
                } catch(e) {
                    alert('An error occurred during upload.');
                }
            }
        };

        xhr.onerror = function() {
            progressContainer.classList.add('hidden');
            alert('An error occurred during upload.');
        };

        xhr.send(formData);
    }

    function removeAvatar() {
        if (!confirm('Are you sure you want to remove your profile photo?')) return;

        fetch('{{ route("profile.photo.delete") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('avatar-preview-element').src = data.url;
                
                const sidebarAvatar = document.getElementById('sidebar-avatar');
                if (sidebarAvatar) sidebarAvatar.src = data.url;
                
                const headerAvatar = document.getElementById('header-avatar');
                if (headerAvatar) headerAvatar.src = data.url;

                document.getElementById('remove-avatar-btn').style.display = 'none';
            } else {
                alert(data.message || 'Failed to remove profile photo.');
            }
        })
        .catch(() => {
            alert('An error occurred.');
        });
    }
</script>
@endsection

@extends('layouts.app', ['title' => 'Profile - Eventra'])
@section('page-title','Profile Settings')
@section('content')

<style>
    .avatar-upload-zone {
        border: 2px dashed rgba(0, 128, 105, 0.25);
        border-radius: 1.5rem;
        background: rgba(0, 128, 105, 0.02);
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.25s ease;
        position: relative;
    }
    .avatar-upload-zone:hover, .avatar-upload-zone.dragover {
        border-color: #008069;
        background: rgba(0, 128, 105, 0.05);
    }
    .progress-bar-container {
        display: none;
        width: 100%;
        background-color: #e2e8f0;
        border-radius: 9999px;
        height: 6px;
        margin-top: 12px;
        overflow: hidden;
    }
    .progress-bar-fill {
        width: 0%;
        height: 100%;
        background-color: #008069;
        transition: width 0.15s ease-out;
    }
</style>

<div class="grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
    <!-- Avatar Card -->
    <div class="glass rounded-[2rem] p-6 flex flex-col items-center">
        <h3 class="font-display text-2xl font-bold mb-4 w-full">Profile Photo</h3>
        
        <div style="position: relative; margin-bottom: 20px;">
            <!-- Circular Avatar Preview -->
            <img id="avatar-preview-element" class="h-40 w-40 rounded-full object-cover ring-4 ring-[#008069]/30 shadow-lg" 
                 src="{{ auth()->user()->avatar_url }}" alt="Profile Photo" style="transition: all 0.3s ease;">
        </div>

        <h2 class="font-display text-2xl font-bold text-center">{{ auth()->user()->name }}</h2>
        <p class="text-white/50 text-center mb-6 text-sm">{{ ucfirst(auth()->user()->role) }} Account</p>

        <!-- Dynamic Drag & Drop Uploader -->
        <div class="w-full">
            <div id="avatar-drop-zone" class="avatar-upload-zone" onclick="document.getElementById('profile-photo-input').click()">
                <span style="font-size: 1.8rem; display: block; margin-bottom: 6px;">📸</span>
                <span style="font-size: 0.85rem; font-weight: 700; color: #111b21;">Drag & Drop or Click to Upload</span>
                <span style="font-size: 0.72rem; display: block; color: #667781; margin-top: 4px;">JPG, PNG, or WEBP up to 2MB</span>
                <input type="file" id="profile-photo-input" style="display: none;" accept="image/*" onchange="handleFileSelect(event)">
            </div>

            <!-- Upload Progress Indicator -->
            <div id="upload-progress-container" class="progress-bar-container">
                <div id="upload-progress-fill" class="progress-bar-fill"></div>
            </div>
            
            <div class="flex gap-3 mt-4 w-full">
                <button type="button" class="btn-ghost flex-1 !py-2 text-sm text-[#008069] font-bold" onclick="document.getElementById('profile-photo-input').click()">Change Photo</button>
                @if(auth()->user()->profile_photo)
                    <button type="button" id="remove-avatar-btn" class="btn-ghost flex-1 !py-2 text-sm text-red-500 font-bold border-red-200" onclick="removeAvatar()">Remove</button>
                @else
                    <button type="button" id="remove-avatar-btn" class="btn-ghost flex-1 !py-2 text-sm text-red-500 font-bold border-red-200" style="display: none;" onclick="removeAvatar()">Remove</button>
                @endif
            </div>
        </div>
    </div>

    <!-- Account Details Form -->
    <div class="glass-strong rounded-[2rem] p-6">
        <h3 class="font-display text-2xl font-bold mb-4">Edit Profile Details</h3>
        
        <form method="POST" action="{{ route('profile.update') }}" class="grid gap-4 sm:grid-cols-2">
            @csrf
            <div>
                <label class="field-label">Company Name</label>
                <input class="w-full" name="company_name" value="{{ old('company_name', $profile->company_name) }}" placeholder="e.g. Dream Weddings Inc.">
            </div>
            <div>
                <label class="field-label">Phone Number</label>
                <input class="w-full" name="phone" value="{{ old('phone', $profile->phone ?? auth()->user()->phone) }}" placeholder="+91 98765 43210">
            </div>
            <div>
                <label class="field-label">Location / City</label>
                <input class="w-full" name="location" value="{{ old('location', $profile->location) }}" placeholder="e.g. Mumbai, Maharashtra">
            </div>
            <div>
                <label class="field-label">Website URL</label>
                <input class="w-full" name="website" value="{{ old('website', $profile->website) }}" placeholder="https://example.com">
            </div>
            <div class="sm:col-span-2">
                <label class="field-label">Bio / Profile Summary</label>
                <textarea class="w-full" rows="5" name="bio" placeholder="Tell us about yourself or your business... (Max 600 characters)">{{ old('bio', $profile->bio) }}</textarea>
            </div>
            <div class="sm:col-span-2 mt-2">
                <button class="btn-primary !px-6 !py-3">Save Profile Details</button>
            </div>
        </form>
    </div>
</div>

<script>
    const dropZone = document.getElementById('avatar-drop-zone');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // Drag and drop events
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
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

    /**
     * AJAX Upload Profile Photo.
     */
    function uploadProfilePhoto(file) {
        if (!file.type.match('image.*')) {
            alert('Please select a valid image file (JPG, PNG, or WEBP).');
            return;
        }

        const formData = new FormData();
        formData.append('profile_photo', file);

        const progressContainer = document.getElementById('upload-progress-container');
        const progressBarFill = document.getElementById('upload-progress-fill');
        
        progressContainer.style.display = 'block';
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
            progressContainer.style.display = 'none';
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // Update previews reactive to changes
                    document.getElementById('avatar-preview-element').src = response.url;
                    
                    const sidebarAvatar = document.getElementById('sidebar-avatar');
                    if (sidebarAvatar) sidebarAvatar.src = response.url;
                    
                    const headerAvatar = document.getElementById('header-avatar');
                    if (headerAvatar) headerAvatar.src = response.url;

                    // Show remove button
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
            progressContainer.style.display = 'none';
            alert('An error occurred during upload.');
        };

        xhr.send(formData);
    }

    /**
     * AJAX Remove Profile Photo.
     */
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

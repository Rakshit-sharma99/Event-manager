@extends('layouts.guest', ['title' => 'Complete Onboarding - Eventra'])
@section('hide-nav', '1')

@section('content')
<section class="auth-stage">
    <a href="{{ route('landing') }}" class="auth-brand">Eventra</a>

    <form method="POST" action="{{ route('google.register.complete') }}" class="auth-card">
        @csrf
        <div style="text-align: center; margin-bottom: 24px;">
            @if(!empty($googleUser['profile_photo']))
                <img src="{{ $googleUser['profile_photo'] }}" alt="{{ $googleUser['name'] }}" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #cbd5e1; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            @endif
            <h1 style="margin: 12px 0 4px;">Welcome, {{ explode(' ', trim($googleUser['name'] ?? 'User'))[0] }}!</h1>
            <p class="plain-muted" style="font-size: 0.95rem; margin: 0;">Please customize your account settings to complete your registration.</p>
        </div>

        <div class="auth-grid">
            <div style="grid-column: 1 / -1;">
                <label>I am registering as an...</label>
                <select name="role" id="reg-role" onchange="toggleVendorFields()" required style="padding: 10px; font-size: 1rem;">
                    <option value="guest" {{ old('role') === 'guest' ? 'selected' : '' }}>Guest (Attend events & RSVP)</option>
                    <option value="planner" {{ old('role') === 'planner' ? 'selected' : '' }}>Event Planner (Organize events & budgets)</option>
                    <option value="vendor" {{ old('role') === 'vendor' ? 'selected' : '' }}>Service Vendor (Offer business services)</option>
                </select>
                @error('role') <small class="otp-error">{{ $message }}</small> @enderror
            </div>

            <div id="residence-field">
                <label>Residence / City</label>
                <input name="residence" value="{{ old('residence') }}" placeholder="e.g. Mumbai">
                @error('residence') <small class="otp-error">{{ $message }}</small> @enderror
            </div>

            <div id="phone-field">
                <label>Phone Number</label>
                <input name="phone_number" value="{{ old('phone_number') }}" placeholder="+91 98765 43210">
                @error('phone_number') <small class="otp-error">{{ $message }}</small> @enderror
            </div>

            <div id="vendor-category-field" style="display: none; grid-column: 1 / -1;">
                <label>Service Category *</label>
                <select name="vendor_category" id="reg-vendor-category" style="padding: 10px;">
                    <option value="">-- Select your category --</option>
                    @foreach(config('smart_budget.service_vendor_category_map', []) as $smartCat => $vendorCats)
                        @php $catLabel = config("smart_budget.services.{$smartCat}.label", ucfirst(str_replace('_', ' ', $smartCat))); @endphp
                        <option value="{{ $smartCat }}" {{ old('vendor_category') === $smartCat ? 'selected' : '' }}>{{ $catLabel }}</option>
                    @endforeach
                </select>
                <small style="color: #666; font-size: 0.8rem; display: block; margin-top: 4px;">Select the service category your business falls under</small>
                @error('vendor_category') <small class="otp-error">{{ $message }}</small> @enderror
            </div>
        </div>

        <button class="auth-submit" type="submit" style="margin-top: 24px; width: 100%; padding: 12px; font-size: 1rem;">Complete Registration</button>
        <p style="text-align: center; margin-top: 16px;"><a href="{{ route('landing') }}" style="color: #666; text-decoration: none;">Cancel onboarding</a></p>
    </form>

    <script>
    function toggleVendorFields() {
        const role = document.getElementById('reg-role').value;
        const categoryField = document.getElementById('vendor-category-field');
        const categorySelect = document.getElementById('reg-vendor-category');
        const residenceInput = document.getElementsByName('residence')[0];
        
        if (role === 'vendor') {
            categoryField.style.display = 'block';
            categorySelect.required = true;
            if (residenceInput) {
                residenceInput.required = true;
                const label = residenceInput.previousElementSibling;
                if (label && !label.innerHTML.includes('*')) {
                    label.innerHTML = 'Residence / City *';
                }
            }
        } else {
            categoryField.style.display = 'none';
            categorySelect.required = false;
            categorySelect.value = '';
            if (residenceInput) {
                residenceInput.required = false;
                const label = residenceInput.previousElementSibling;
                if (label) {
                    label.innerHTML = 'Residence / City';
                }
            }
        }
    }
    document.addEventListener('DOMContentLoaded', toggleVendorFields);
    toggleVendorFields();
    </script>
</section>
@endsection

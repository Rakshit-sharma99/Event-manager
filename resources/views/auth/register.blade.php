@extends('layouts.guest', ['title' => 'Register - Eventra'])
@section('hide-nav', '1')

@section('content')
<section class="auth-stage">
    <a href="{{ route('landing') }}" class="auth-brand">Eventra</a>

    <form method="POST" action="{{ route('register.store') }}" class="auth-card">
        @csrf
        <h1>Create Account</h1>

        <div class="auth-grid">
            <div>
                <label>Full Name</label>
                <input name="name" value="{{ old('name') }}" required placeholder="John Doe">
                @error('name') <small class="otp-error">{{ $message }}</small> @enderror
            </div>
            <div>
                <label>Email</label>
                <input name="email" type="email" value="{{ old('email', request('email')) }}" required placeholder="you@example.com">
                @error('email') <small class="otp-error">{{ $message }}</small> @enderror
            </div>
            <div>
                <label>Residence / City</label>
                <input name="residence" value="{{ old('residence') }}" placeholder="e.g. Mumbai">
                @error('residence') <small class="otp-error">{{ $message }}</small> @enderror
            </div>
            <div>
                <label>Phone Number</label>
                <input name="phone_number" value="{{ old('phone_number') }}" placeholder="+91 98765 43210">
                @error('phone_number') <small class="otp-error">{{ $message }}</small> @enderror
            </div>
            <div>
                <label>I am a...</label>
                <select name="role" id="reg-role" onchange="toggleVendorCategory()">
                    <option value="planner" {{ old('role', request('role')) === 'planner' ? 'selected' : '' }}>Event Planner</option>
                    <option value="vendor" {{ old('role', request('role')) === 'vendor' ? 'selected' : '' }}>Vendor</option>
                    <option value="guest" {{ old('role', request('role')) === 'guest' ? 'selected' : '' }}>Guest</option>
                </select>
                @error('role') <small class="otp-error">{{ $message }}</small> @enderror
            </div>
            <div id="vendor-category-field" style="display: none;">
                <label>Service Category *</label>
                <select name="vendor_category" id="reg-vendor-category">
                    <option value="">-- Select your category --</option>
                    @foreach(config('smart_budget.service_vendor_category_map', []) as $smartCat => $vendorCats)
                        @php $catLabel = config("smart_budget.services.{$smartCat}.label", ucfirst(str_replace('_', ' ', $smartCat))); @endphp
                        <option value="{{ $smartCat }}" {{ old('vendor_category') === $smartCat ? 'selected' : '' }}>{{ $catLabel }}</option>
                    @endforeach
                </select>
                <small style="color: #666; font-size: 0.8rem;">Select the service category your business falls under</small>
                @error('vendor_category') <small class="otp-error">{{ $message }}</small> @enderror
            </div>
            <div>
                <label>Password</label>
                <input name="password" type="password" required placeholder="Min 8 characters">
                @error('password') <small class="otp-error">{{ $message }}</small> @enderror
            </div>
            <div style="grid-column: 1 / -1;">
                <label>Confirm Password</label>
                <input name="password_confirmation" type="password" required placeholder="Re-enter password">
            </div>
        </div>

        <button class="auth-submit" type="submit">Sign up</button>

        <div class="auth-divider">
            <span>OR</span>
        </div>

        <a href="{{ route('google.login') }}" class="btn-google">
            <svg class="google-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
            </svg>
            <span>Continue with Google</span>
        </a>

        <p>Already registered? <a href="{{ route('login') }}">Login</a></p>
    </form>

    <script>
    function toggleVendorCategory() {
        const role = document.getElementById('reg-role').value;
        const field = document.getElementById('vendor-category-field');
        const select = document.getElementById('reg-vendor-category');
        if (role === 'vendor') {
            field.style.display = '';
            select.required = true;
        } else {
            field.style.display = 'none';
            select.required = false;
            select.value = '';
        }
    }
    // Run on page load for old() state or direct URL parameter
    document.addEventListener('DOMContentLoaded', toggleVendorCategory);
    toggleVendorCategory();
    </script>
</section>
@endsection

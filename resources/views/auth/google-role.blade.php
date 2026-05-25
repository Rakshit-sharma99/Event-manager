@extends('layouts.guest', ['title' => 'Complete Onboarding — Eventra'])
@section('hide-nav', '1')

@section('content')
<div class="min-h-screen flex">
    {{-- Left — Decorative --}}
    <div class="hidden lg:flex flex-1 bg-brand-gradient relative overflow-hidden items-center justify-center p-12">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIxIiBmaWxsPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDUpIi8+PC9zdmc+')] opacity-60"></div>
        <div class="absolute top-20 left-20 w-40 h-40 rounded-full bg-white/5 blur-2xl"></div>
        <div class="absolute bottom-20 right-20 w-60 h-60 rounded-full bg-white/5 blur-3xl"></div>

        <div class="relative z-10 max-w-md text-white text-center">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-h2 font-extrabold mb-8">
                <span class="text-3xl">✦</span> Eventra
            </a>
            <h2 class="text-h2 font-extrabold mb-4 leading-tight">Welcome to the family</h2>
            <p class="text-body-lg opacity-80 leading-relaxed">Let's personalize your workspace settings so we can tailor Eventra's smart planning tools for you.</p>
            <div class="mt-12 flex justify-center gap-3">
                <div class="w-2 h-2 rounded-full bg-white/40"></div>
                <div class="w-2 h-2 rounded-full bg-white/40"></div>
                <div class="w-8 h-2 rounded-full bg-white"></div>
            </div>
        </div>
    </div>

    {{-- Right — Onboarding Form --}}
    <div class="flex-1 flex items-center justify-center p-6 lg:p-12 bg-white">
        <div class="w-full max-w-md" data-animate="fade-up" x-data="{ role: '{{ old('role', 'guest') }}' }">
            {{-- Mobile logo --}}
            <a href="{{ route('landing') }}" class="lg:hidden flex items-center gap-2 text-h3 font-extrabold text-primary-500 mb-8">
                <span class="text-2xl">✦</span> Eventra
            </a>

            {{-- Welcome User Info --}}
            <div class="flex flex-col items-center text-center mb-8">
                @if(!empty($googleUser['profile_photo']))
                    <img src="{{ $googleUser['profile_photo'] }}" alt="{{ $googleUser['name'] }}" class="w-20 h-20 rounded-full object-cover border-4 border-surface-100 shadow-md">
                @endif
                <h1 class="text-h2 font-extrabold text-neutral-dark mt-4 mb-2">Welcome, {{ explode(' ', trim($googleUser['name'] ?? 'User'))[0] }}!</h1>
                <p class="text-body text-surface-500">Please customize your account settings to complete your registration.</p>
            </div>

            <form method="POST" action="{{ route('google.register.complete') }}" class="space-y-5">
                @csrf

                {{-- Role Selection Radio Cards --}}
                <div class="space-y-1.5">
                    <label class="block text-body font-medium text-surface-700">I am registering as an... <span class="text-danger">*</span></label>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach(['guest' => '🎟️ Guest', 'planner' => '📋 Planner', 'vendor' => '🏪 Vendor'] as $value => $label)
                            <label @click="role = '{{ $value }}'" :class="role === '{{ $value }}' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-surface-200 text-surface-600 hover:border-surface-300'"
                                   class="flex flex-col items-center gap-1 p-3 rounded-lg border-2 cursor-pointer transition-all text-center">
                                <input type="radio" name="role" value="{{ $value }}" x-model="role" class="sr-only">
                                <span class="text-lg">{{ explode(' ', $label)[0] }}</span>
                                <span class="text-caption font-semibold">{{ explode(' ', $label, 2)[1] }}</span>
                            </label>
                        @endforeach
                    </div>
                    @if($errors->first('role'))
                        <p class="text-caption text-danger animate-shake">{{ $errors->first('role') }}</p>
                    @endif
                </div>

                {{-- Residence / City Input --}}
                <div class="space-y-1.5">
                    <label for="residence" class="block text-body font-medium text-surface-700">
                        Residence / City <span x-show="role === 'vendor'" class="text-danger" x-cloak>*</span>
                    </label>
                    <input 
                        type="text" 
                        id="residence" 
                        name="residence" 
                        placeholder="e.g. Mumbai" 
                        :required="role === 'vendor'"
                        value="{{ old('residence') }}"
                        class="input {{ $errors->first('residence') ? 'border-danger' : '' }}"
                    >
                    @if($errors->first('residence'))
                        <p class="text-caption text-danger animate-shake">{{ $errors->first('residence') }}</p>
                    @endif
                </div>

                {{-- Phone Number Input --}}
                <x-input 
                    label="Phone Number" 
                    name="phone_number" 
                    type="text" 
                    placeholder="+91 98765 43210" 
                    :value="old('phone_number')"
                    :error="$errors->first('phone_number')" 
                />

                {{-- Vendor Category field --}}
                <div x-show="role === 'vendor'" x-transition class="space-y-1.5" x-cloak>
                    <label for="vendor_category" class="block text-body font-medium text-surface-700">Service Category <span class="text-danger">*</span></label>
                    <select 
                        name="vendor_category" 
                        id="vendor_category" 
                        :required="role === 'vendor'"
                        class="input"
                    >
                        <option value="">-- Select your category --</option>
                        @foreach(config('smart_budget.service_vendor_category_map', []) as $smartCat => $vendorCats)
                            @php $catLabel = config("smart_budget.services.{$smartCat}.label", ucfirst(str_replace('_', ' ', $smartCat))); @endphp
                            <option value="{{ $smartCat }}" {{ old('vendor_category') === $smartCat ? 'selected' : '' }}>{{ $catLabel }}</option>
                        @endforeach
                    </select>
                    <p class="text-caption text-surface-400">Select the service category your business falls under.</p>
                    @if($errors->first('vendor_category'))
                        <p class="text-caption text-danger animate-shake">{{ $errors->first('vendor_category') }}</p>
                    @endif
                </div>

                <button type="submit" class="btn-primary w-full py-3">Complete Registration</button>
            </form>

            <p class="mt-6 text-center text-body text-surface-500">
                <a href="{{ route('landing') }}" class="font-semibold text-surface-500 hover:text-primary-500 transition-colors">Cancel onboarding</a>
            </p>
        </div>
    </div>
</div>
@endsection

@extends('layouts.guest', ['title' => 'Verify Code — Eventra'])
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
            <h2 class="text-h2 font-extrabold mb-4 leading-tight">One step closer</h2>
            <p class="text-body-lg opacity-80 leading-relaxed">We sent a secure validation key to your inbox to protect your identity. Let's make sure it's you.</p>
            <div class="mt-12 flex justify-center gap-3">
                <div class="w-2 h-2 rounded-full bg-white/40"></div>
                <div class="w-8 h-2 rounded-full bg-white"></div>
                <div class="w-2 h-2 rounded-full bg-white/40"></div>
            </div>
        </div>
    </div>

    {{-- Right — Verification Form --}}
    <div class="flex-1 flex items-center justify-center p-6 lg:p-12 bg-white">
        <div class="w-full max-w-md" data-animate="fade-up">
            {{-- Mobile logo --}}
            <a href="{{ route('landing') }}" class="lg:hidden flex items-center gap-2 text-h3 font-extrabold text-primary-500 mb-8">
                <span class="text-2xl">✦</span> Eventra
            </a>

            <h1 class="text-h2 font-extrabold text-neutral-dark mb-2">Verify Your Email</h1>
            <p class="text-body text-surface-500 mb-8">Enter the 6-digit code sent to <strong class="text-neutral-dark">{{ session('reset_email') }}</strong></p>

            <form method="POST" action="{{ route('password.verify-otp.submit') }}" id="otp-form" class="space-y-6">
                @csrf

                <div class="flex gap-2 sm:gap-3 justify-center mb-6" id="otp-container">
                    <input type="text" name="otp[]" maxlength="1" class="otp-digit w-12 h-12 text-center text-xl font-bold rounded border border-surface-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/15 focus:outline-none transition-all duration-200" inputmode="numeric" pattern="[0-9]" autocomplete="one-time-code" autofocus>
                    <input type="text" name="otp[]" maxlength="1" class="otp-digit w-12 h-12 text-center text-xl font-bold rounded border border-surface-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/15 focus:outline-none transition-all duration-200" inputmode="numeric" pattern="[0-9]">
                    <input type="text" name="otp[]" maxlength="1" class="otp-digit w-12 h-12 text-center text-xl font-bold rounded border border-surface-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/15 focus:outline-none transition-all duration-200" inputmode="numeric" pattern="[0-9]">
                    <input type="text" name="otp[]" maxlength="1" class="otp-digit w-12 h-12 text-center text-xl font-bold rounded border border-surface-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/15 focus:outline-none transition-all duration-200" inputmode="numeric" pattern="[0-9]">
                    <input type="text" name="otp[]" maxlength="1" class="otp-digit w-12 h-12 text-center text-xl font-bold rounded border border-surface-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/15 focus:outline-none transition-all duration-200" inputmode="numeric" pattern="[0-9]">
                    <input type="text" name="otp[]" maxlength="1" class="otp-digit w-12 h-12 text-center text-xl font-bold rounded border border-surface-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/15 focus:outline-none transition-all duration-200" inputmode="numeric" pattern="[0-9]">
                </div>

                <input type="hidden" name="otp_code" id="otp-code-hidden">

                @if($errors->has('otp_code'))
                    <p class="text-caption font-semibold text-danger text-center animate-shake">{{ $errors->first('otp_code') }}</p>
                @endif

                <div class="flex justify-center text-body text-surface-500">
                    Code expires in: <span id="otp-timer" class="font-bold text-primary-500 ml-1">15:00</span>
                </div>

                <button type="submit" class="btn-primary w-full py-3" id="otp-submit-btn">Verify Code</button>
            </form>

            <div class="mt-6 flex justify-center text-body">
                <form method="POST" action="{{ route('password.resend-otp') }}" id="resend-form">
                    @csrf
                    <span class="text-surface-500">Didn't receive the code?</span>
                    <button type="submit" class="font-semibold text-primary-500 hover:text-primary-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors ml-1" id="resend-btn" disabled>
                        Resend Code <span id="resend-cooldown"></span>
                    </button>
                </form>
            </div>

            <p class="mt-8 text-center text-body text-surface-500">
                <a href="{{ route('password.reset') }}" class="font-semibold text-primary-500 hover:text-primary-600 transition-colors">Use a different email</a>
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.otp-digit');
    const hiddenField = document.getElementById('otp-code-hidden');
    const form = document.getElementById('otp-form');
    const timerEl = document.getElementById('otp-timer');
    const resendBtn = document.getElementById('resend-btn');
    const cooldownSpan = document.getElementById('resend-cooldown');

    inputs.forEach((input, idx) => {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value && idx < inputs.length - 1) {
                inputs[idx + 1].focus();
            }
            updateHidden();
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && !this.value && idx > 0) {
                inputs[idx - 1].focus();
            }
        });
    });

    inputs[0].addEventListener('paste', function (e) {
        e.preventDefault();
        const digits = (e.clipboardData || window.clipboardData).getData('text').trim().replace(/[^0-9]/g, '').slice(0, 6);
        for (let i = 0; i < digits.length && i < inputs.length; i++) {
            inputs[i].value = digits[i];
        }
        if (digits.length > 0) inputs[Math.min(digits.length, inputs.length) - 1].focus();
        updateHidden();
    });

    function updateHidden() {
        hiddenField.value = Array.from(inputs).map(i => i.value).join('');
    }

    form.addEventListener('submit', function () { updateHidden(); });

    // Countdown
    let remaining = @json($resetUser && $resetUser->otp_expires_at ? max(0, $resetUser->otp_expires_at->diffInSeconds(now())) : 900);
    function updateTimer() {
        if (remaining <= 0) {
            timerEl.textContent = 'Code expired';
            timerEl.classList.add('text-danger');
            return;
        }
        const m = Math.floor(remaining / 60);
        const s = remaining % 60;
        timerEl.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
        remaining--;
        setTimeout(updateTimer, 1000);
    }
    updateTimer();

    // Resend cooldown
    let resendCooldown = 60;
    @if($resetUser && $resetUser->otp_last_resent_at)
        resendCooldown = Math.max(0, 60 - @json(now()->diffInSeconds($resetUser->otp_last_resent_at)));
    @endif
    function updateResendBtn() {
        if (resendCooldown <= 0) {
            resendBtn.disabled = false;
            cooldownSpan.textContent = '';
            return;
        }
        resendBtn.disabled = true;
        cooldownSpan.textContent = '(' + resendCooldown + 's)';
        resendCooldown--;
        setTimeout(updateResendBtn, 1000);
    }
    updateResendBtn();
});
</script>
@endsection

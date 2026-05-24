@extends('layouts.guest', ['title' => 'Verify Code - Reset Password - Eventra'])
@section('hide-nav', '1')

@section('content')
<section class="auth-stage">
    <a href="{{ route('landing') }}" class="auth-brand">Eventra</a>

    <div class="auth-card">
        <h1>Verify Your Email</h1>
        <p class="plain-muted">Enter the 6-digit code sent to <strong>{{ session('reset_email') }}</strong></p>

        <form method="POST" action="{{ route('password.verify-otp.submit') }}" id="otp-form">
            @csrf

            <div class="otp-inputs" id="otp-container">
                <input type="text" name="otp[]" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]" autocomplete="one-time-code" autofocus>
                <input type="text" name="otp[]" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]">
                <input type="text" name="otp[]" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]">
                <input type="text" name="otp[]" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]">
                <input type="text" name="otp[]" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]">
                <input type="text" name="otp[]" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]">
            </div>

            <input type="hidden" name="otp_code" id="otp-code-hidden">

            @if($errors->has('otp_code'))
                <p class="otp-error" style="text-align:center;">{{ $errors->first('otp_code') }}</p>
            @endif

            <div class="otp-timer-row">
                <span id="otp-timer" class="otp-timer">15:00</span>
            </div>

            <button class="auth-submit" type="submit">Verify Code</button>
        </form>

        <div class="otp-resend-row">
            <form method="POST" action="{{ route('password.resend-otp') }}">
                @csrf
                <button type="submit" class="otp-resend-btn" id="resend-btn" disabled>
                    Resend Code <span id="resend-cooldown"></span>
                </button>
            </form>
        </div>

        <p style="margin-top: 12px; text-align: center;"><a href="{{ route('password.reset') }}">Use a different email</a></p>
    </div>
</section>

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
            timerEl.classList.add('otp-timer-expired');
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

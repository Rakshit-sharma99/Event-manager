@if(session('success') || session('invite_link') || session('timeline_link') || $errors->any())
    <div class="mx-auto mb-5 max-w-7xl px-5">
        <div class="glass-strong rounded-3xl p-4" data-reveal>
            @if(session('success'))<p class="font-semibold text-eventra-cyan">{{ session('success') }}</p>@endif
            @if(session('invite_link'))<p class="mt-2 break-all text-sm text-white/70">Invite link: <a class="text-eventra-cyan" href="{{ session('invite_link') }}">{{ session('invite_link') }}</a></p>@endif
            @if(session('timeline_link'))<p class="mt-2 break-all text-sm text-white/70">Timeline link: <a class="text-eventra-cyan" href="{{ session('timeline_link') }}">{{ session('timeline_link') }}</a></p>@endif
            @if(session('verification_link'))<p class="mt-2 break-all text-sm text-white/70">Dev verification link: <a class="text-eventra-cyan" href="{{ session('verification_link') }}">{{ session('verification_link') }}</a></p>@endif
            @if($errors->any())<p class="font-semibold text-rose-300">{{ $errors->first() }}</p>@endif
        </div>
    </div>
@endif

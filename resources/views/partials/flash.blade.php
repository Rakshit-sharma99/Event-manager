{{-- ══════════════════════════════════════════════════════════════
     Flash Messages — Uses <x-alert> component
     ══════════════════════════════════════════════════════════════ --}}

@if(session('success') || session('error') || session('warning') || session('info') || $errors->any())
    <div class="fixed top-4 right-4 z-[110] flex flex-col gap-3 w-96 max-w-[calc(100%-2rem)]">
        @if(session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif

        @if(session('error'))
            <x-alert type="error" :message="session('error')" />
        @endif

        @if(session('warning'))
            <x-alert type="warning" :message="session('warning')" />
        @endif

        @if(session('info'))
            <x-alert type="info" :message="session('info')" />
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                <x-alert type="error" :message="$error" />
            @endforeach
        @endif
    </div>
@endif

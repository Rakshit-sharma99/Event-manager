@extends('layouts.app', ['title' => 'Guest Dashboard — Eventra'])

@section('content')
<div class="space-y-6">
    {{-- Greeting Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-brand-gradient rounded-md p-6 text-white shadow-lg relative overflow-hidden" data-animate="fade-up">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIxIiBmaWxsPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDUpIi8+PC9zdmc+')] opacity-40"></div>
        <div class="relative z-10">
            <h1 class="text-h2 font-extrabold mb-1">Hello, {{ $user->name }}! 👋</h1>
            <p class="text-body-lg opacity-90">Welcome to your guest lounge. Here you can track your invitations, manage RSVPs, and chat with hosts.</p>
        </div>
        <div class="relative z-10 flex gap-2">
            <x-btn href="{{ route('chat.index') }}" variant="ghost" class="!text-white !bg-white/10 hover:!bg-white/20 border-transparent">
                💬 Messages
            </x-btn>
            <x-btn href="{{ route('profile.edit') }}" variant="ghost" class="!text-white !bg-white/10 hover:!bg-white/20 border-transparent">
                👤 Profile Settings
            </x-btn>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4" data-animate="stagger">
        <x-stat-card 
            label="Total Invitations" 
            :value="$events->count()" 
            sub="Events you're invited to" 
            icon="🎟️" 
        />
        <x-stat-card 
            label="Attending" 
            :value="$guestRecords->where('rsvp_status', 'yes')->count() + $guestRecords->where('status', 'yes')->count()" 
            sub="Confirmed celebrations" 
            icon="✅" 
            :highlight="true"
        />
        <x-stat-card 
            label="Pending Responses" 
            :value="$guestRecords->whereIn('rsvp_status', ['pending', ''])->count() + $guestRecords->whereIn('status', ['pending', ''])->count()" 
            sub="Awaiting your reply" 
            icon="⏳" 
        />
    </div>

    {{-- Invitations List --}}
    <div class="space-y-4">
        <h2 class="text-h3 font-bold text-neutral-dark">Your Invitations</h2>

        @if($events->isEmpty())
            <x-card class="py-12" data-animate="fade-up">
                <x-empty-state 
                    title="No invitations yet" 
                    description="You haven't been invited to any events yet. When a host invites you, their celebration will appear here." 
                    icon="✉️"
                />
            </x-card>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" data-animate="stagger">
                @foreach($events as $event)
                    @php
                        $guestRecord = $guestRecords->where('event_id', (string) $event->getKey())->first();
                        $status = $guestRecord->rsvp_status ?? $guestRecord->status ?? 'pending';
                        
                        $badgeVariant = match($status) {
                            'yes', 'attending', 'confirmed' => 'success',
                            'no', 'declined' => 'danger',
                            'maybe' => 'info',
                            default => 'warning',
                        };

                        $statusLabel = match($status) {
                            'yes', 'attending', 'confirmed' => 'Attending',
                            'no', 'declined' => 'Declined',
                            'maybe' => 'Maybe',
                            default => 'Pending Reply',
                        };
                    @endphp

                    <x-card class="hover:-translate-y-1 hover:shadow-glow flex flex-col justify-between h-full group transition-all duration-300">
                        <div>
                            {{-- Header --}}
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <h3 class="text-h4 font-bold text-neutral-dark group-hover:text-primary-500 transition-colors leading-tight">
                                    {{ $event->event_name ?? $event->title }}
                                </h3>
                                <x-badge variant="{{ $badgeVariant }}">
                                    {{ $statusLabel }}
                                </x-badge>
                            </div>

                            {{-- Event details --}}
                            <div class="space-y-2 text-body text-surface-500 mb-6">
                                <div class="flex items-center gap-2">
                                    <span class="text-neutral-dark flex-shrink-0">📅</span>
                                    <span>{{ optional($event->event_date)->format('F d, Y') ?? 'Date: TBD' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-neutral-dark flex-shrink-0">📍</span>
                                    <span class="truncate">{{ $event->location ?? 'Location: TBD' }}</span>
                                </div>
                                @if($event->planner)
                                    <div class="flex items-center gap-2">
                                        <span class="text-neutral-dark flex-shrink-0">👤</span>
                                        <span>Hosted by: {{ $event->planner->name }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="pt-4 border-t border-surface-100 flex gap-2">
                            @if(!empty($guestRecord->invite_token))
                                <x-btn href="{{ route('rsvp.show', $guestRecord->invite_token) }}" variant="primary" size="sm" class="flex-1">
                                    Update RSVP
                                </x-btn>
                            @else
                                <x-btn href="#" variant="primary" size="sm" class="flex-1 disabled opacity-50 cursor-not-allowed">
                                    No RSVP Token
                                </x-btn>
                            @endif

                            <x-btn href="{{ route('chat.index') }}" variant="outline" size="sm" class="px-3" title="Chat with Host">
                                💬
                            </x-btn>
                        </div>
                    </x-card>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
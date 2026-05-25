@extends('layouts.app', ['title' => 'Guests — Eventra'])

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="badge bg-primary-50 text-primary-600 font-semibold mb-2">{{ $event->event_name }}</span>
            <h1 class="text-h2 font-extrabold text-neutral-dark">Guest List & RSVP</h1>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-btn href="{{ route('guests.create', $event) }}" variant="primary" size="sm" icon="plus">
                Add Guest
            </x-btn>
            <x-btn href="{{ route('guests.bulk', $event) }}" variant="outline" size="sm" icon="info">
                CSV Import
            </x-btn>
            <x-btn href="{{ route('guests.export', $event) }}" variant="ghost" size="sm" icon="arrow-right">
                Export CSV
            </x-btn>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4" data-animate="stagger">
        <x-stat-card 
            label="Attending" 
            :value="$stats['yes'] ?? 0" 
            sub="Confirmed RSVPs" 
            icon="✅" 
            :highlight="true"
        />
        <x-stat-card 
            label="Declined" 
            :value="$stats['no'] ?? 0" 
            sub="Regrets received" 
            icon="❌" 
        />
        <x-stat-card 
            label="Maybe" 
            :value="$stats['maybe'] ?? 0" 
            sub="Undecided guests" 
            icon="❓" 
        />
        <x-stat-card 
            label="Pending" 
            :value="$stats['pending'] ?? 0" 
            sub="Awaiting reply" 
            icon="⏳" 
        />
    </div>

    {{-- Search & Filter --}}
    <x-card class="!p-4" data-animate="fade-up">
        <form method="GET" action="{{ route('guests.index', $event) }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Search by name or email..." 
                    class="input"
                >
            </div>
            <div class="w-full sm:w-48">
                <select name="rsvp" class="input">
                    <option value="">All RSVP Status</option>
                    @foreach(['pending' => 'Pending', 'yes' => 'Attending', 'no' => 'Declined', 'maybe' => 'Maybe'] as $status => $label)
                        <option value="{{ $status }}" @selected(request('rsvp') === $status)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary py-2.5 px-6 flex-1 sm:flex-none">
                    Filter
                </button>
                @if(request()->filled('q') || request()->filled('rsvp'))
                    <a href="{{ route('guests.index', $event) }}" class="btn-ghost py-2.5 px-4 text-center">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </x-card>

    {{-- Table / Guest List --}}
    @if($guests->isEmpty())
        <x-card class="py-16">
            <x-empty-state 
                title="No guests found" 
                description="No guests match your filters, or you haven't added guests to this event yet." 
                icon="👥"
                action="Add Guest"
                :actionUrl="route('guests.create', $event)"
            />
        </x-card>
    @else
        <x-card class="!p-0 overflow-hidden border border-surface-200 shadow-2xs" data-animate="fade-up">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-50 border-b border-surface-200">
                            <th class="px-6 py-4 text-caption font-bold text-surface-600 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-4 text-caption font-bold text-surface-600 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-caption font-bold text-surface-600 uppercase tracking-wider">Invitation</th>
                            <th class="px-6 py-4 text-caption font-bold text-surface-600 uppercase tracking-wider">RSVP</th>
                            <th class="px-6 py-4 text-caption font-bold text-surface-600 uppercase tracking-wider">Dietary</th>
                            <th class="px-6 py-4 text-caption font-bold text-surface-600 uppercase tracking-wider">Seat</th>
                            <th class="px-6 py-4 text-caption font-bold text-surface-600 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach($guests as $guest)
                            <tr class="hover:bg-surface-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img 
                                            src="{{ $guest->avatar_url }}" 
                                            alt="{{ $guest->name }}" 
                                            class="w-8 h-8 rounded-full object-cover border border-surface-200 flex-shrink-0"
                                        >
                                        <span class="text-body font-semibold text-neutral-dark">{{ $guest->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-body text-surface-500">
                                    {{ $guest->email }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($guest->invite_sent_at)
                                        @php
                                            $sentDate = $guest->invite_sent_at instanceof \Carbon\Carbon 
                                                ? $guest->invite_sent_at 
                                                : \Carbon\Carbon::parse($guest->invite_sent_at);
                                        @endphp
                                        <x-badge variant="success">
                                            Sent ({{ $sentDate->format('M d') }})
                                        </x-badge>
                                    @else
                                        <x-badge variant="gray">
                                            Not Sent
                                        </x-badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $rsvpStatus = $guest->rsvp_status ?? 'pending';
                                        $badgeVar = match($rsvpStatus) {
                                            'yes' => 'success',
                                            'no' => 'danger',
                                            'maybe' => 'info',
                                            default => 'warning',
                                        };
                                        $badgeLbl = match($rsvpStatus) {
                                            'yes' => 'Attending',
                                            'no' => 'Declined',
                                            'maybe' => 'Maybe',
                                            default => 'Pending',
                                        };
                                    @endphp
                                    <x-badge variant="{{ $badgeVar }}">
                                        {{ $badgeLbl }}
                                    </x-badge>
                                </td>
                                <td class="px-6 py-4 text-body text-surface-500 capitalize">
                                    {{ $guest->dietary_preference ?? 'Not specified' }}
                                </td>
                                <td class="px-6 py-4 text-body text-surface-500 font-semibold">
                                    {{ $guest->seat ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2.5">
                                        @if($guest->rsvp_status === 'pending' || !$guest->rsvp_status)
                                            <form method="POST" action="{{ route('guests.invite', [$event, $guest]) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-caption font-bold text-primary-500 hover:text-primary-600 transition-colors uppercase tracking-wider">
                                                    Invite
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('guests.edit', [$event, $guest]) }}" class="text-caption font-bold text-surface-600 hover:text-primary-500 transition-colors uppercase tracking-wider">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('guests.destroy', [$event, $guest]) }}" class="inline" onsubmit="return confirm('Are you sure you want to remove this guest?')">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="text-caption font-bold text-danger hover:text-danger-dark transition-colors uppercase tracking-wider">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $guests->links() }}
        </div>
    @endif
</div>
@endsection

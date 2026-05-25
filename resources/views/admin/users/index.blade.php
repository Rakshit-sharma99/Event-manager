@extends('layouts.admin')
@section('page-title', 'User Management')

@section('content')
<div class="space-y-6 pb-12" data-animate="fade-up">
    {{-- Tabs & Search --}}
    <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
        <div class="flex gap-1 p-1 bg-surface-100 rounded-lg border border-surface-200">
            <a href="{{ route('admin.users') }}" 
               class="px-4 py-2 rounded-md font-medium text-caption transition-all
                      {{ !request('role') ? 'bg-primary-500 text-white shadow-sm' : 'text-surface-600 hover:text-neutral-dark hover:bg-surface-200/50' }}">
                All <span class="opacity-70">({{ $roleCounts['all'] }})</span>
            </a>
            @foreach(['planner' => 'Planners', 'vendor' => 'Vendors', 'guest' => 'Guests', 'admin' => 'Admins'] as $rKey => $rLabel)
                <a href="{{ route('admin.users', ['role' => $rKey]) }}" 
                   class="px-4 py-2 rounded-md font-medium text-caption transition-all
                          {{ request('role') === $rKey ? 'bg-primary-500 text-white shadow-sm' : 'text-surface-600 hover:text-neutral-dark hover:bg-surface-200/50' }}">
                    {{ $rLabel }} <span class="opacity-70">({{ $roleCounts[$rKey] }})</span>
                </a>
            @endforeach
        </div>

        <form method="GET" class="flex items-center gap-2 px-4 py-2 bg-white border border-surface-200 rounded-lg min-w-[280px] focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-500/10 transition-all">
            @if(request('role'))
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            <svg class="w-4 h-4 text-surface-400" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="1.5"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            <input type="text" name="q" placeholder="Search name, email, phone..." value="{{ request('q') }}" class="bg-transparent border-none outline-none text-body text-neutral-dark placeholder:text-surface-400 p-0 flex-1">
        </form>
    </div>

    {{-- Users Table --}}
    <x-card padding="p-0" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-surface-50 border-b border-surface-150">
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Verified</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Registered</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100">
                    @forelse($users as $u)
                        <tr class="hover:bg-surface-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $u->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover border border-surface-200 flex-shrink-0">
                                    <span class="text-body font-bold text-neutral-dark">{{ $u->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-body text-surface-600">
                                {{ $u->email }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $roleVar = match($u->role) {
                                        'admin' => 'success',
                                        'vendor' => 'info',
                                        'planner' => 'primary',
                                        default => 'gray',
                                    };
                                @endphp
                                <x-badge :variant="$roleVar" class="text-[9px] uppercase tracking-wider">{{ $u->role }}</x-badge>
                            </td>
                            <td class="px-6 py-4 text-body text-surface-600">
                                {{ $u->phone_number ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($u->email_verified_at)
                                    <x-badge variant="success" :dot="true" class="text-[9px] uppercase">Verified</x-badge>
                                @else
                                    <x-badge variant="warning" :dot="true" class="text-[9px] uppercase">Unverified</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($u->is_banned)
                                    <x-badge variant="danger" class="uppercase text-[9px] tracking-wider">Banned</x-badge>
                                @elseif($u->is_suspended)
                                    <x-badge variant="warning" class="uppercase text-[9px] tracking-wider">Suspended</x-badge>
                                @else
                                    <x-badge variant="success" class="uppercase text-[9px] tracking-wider">Active</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-caption text-surface-500">
                                {{ $u->created_at?->format('M d, Y') ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($u->role !== 'admin')
                                    <div class="flex gap-2">
                                        @if(!$u->is_suspended && !$u->is_banned)
                                            <form method="POST" action="{{ route('admin.users.suspend', $u) }}" class="inline">
                                                @csrf
                                                <x-btn type="submit" variant="outline" size="sm" class="!border-warning !text-warning hover:!bg-warning hover:!text-white" onclick="return confirm('Suspend {{ $u->name }}?')">Suspend</x-btn>
                                            </form>
                                            <form method="POST" action="{{ route('admin.users.ban', $u) }}" class="inline">
                                                @csrf
                                                <x-btn type="submit" variant="danger" size="sm" onclick="return confirm('Ban {{ $u->name }}? This is a serious action.')">Ban</x-btn>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.activate', $u) }}" class="inline">
                                                @csrf
                                                <x-btn type="submit" size="sm">Activate</x-btn>
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-caption text-surface-400 font-medium">Protected</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-body text-surface-400 bg-white">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="flex justify-center mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection

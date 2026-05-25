@extends('layouts.admin')
@section('page-title', 'User Management')

@section('content')
    {{-- Tabs & Search --}}
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
        <div class="admin-tabs">
            <a href="{{ route('admin.users') }}" class="admin-tab {{ !request('role') ? 'active' : '' }}">
                All <span style="opacity: 0.7;">({{ $roleCounts['all'] }})</span>
            </a>
            <a href="{{ route('admin.users', ['role' => 'planner']) }}" class="admin-tab {{ request('role') === 'planner' ? 'active' : '' }}">
                Planners <span style="opacity: 0.7;">({{ $roleCounts['planner'] }})</span>
            </a>
            <a href="{{ route('admin.users', ['role' => 'vendor']) }}" class="admin-tab {{ request('role') === 'vendor' ? 'active' : '' }}">
                Vendors <span style="opacity: 0.7;">({{ $roleCounts['vendor'] }})</span>
            </a>
            <a href="{{ route('admin.users', ['role' => 'guest']) }}" class="admin-tab {{ request('role') === 'guest' ? 'active' : '' }}">
                Guests <span style="opacity: 0.7;">({{ $roleCounts['guest'] }})</span>
            </a>
            <a href="{{ route('admin.users', ['role' => 'admin']) }}" class="admin-tab {{ request('role') === 'admin' ? 'active' : '' }}">
                Admins <span style="opacity: 0.7;">({{ $roleCounts['admin'] }})</span>
            </a>
        </div>

        <form method="GET" class="admin-search" style="min-width: 280px;">
            @if(request('role'))
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            <span>🔍</span>
            <input type="text" name="q" placeholder="Search name, email, phone..." value="{{ request('q') }}">
        </form>
    </div>

    {{-- Users Table --}}
    <div class="admin-card" style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Verified</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <img src="{{ $u->avatar_url }}" alt="" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover; border: 1px solid var(--admin-border);">
                                <strong style="font-size: 0.88rem;">{{ $u->name }}</strong>
                            </div>
                        </td>
                        <td><span style="font-size: 0.82rem; color: var(--admin-text-muted);">{{ $u->email }}</span></td>
                        <td>
                            <span class="status-badge {{ $u->role === 'admin' ? 'approved' : ($u->role === 'vendor' ? 'under_review' : 'active') }}">
                                {{ ucfirst($u->role) }}
                            </span>
                        </td>
                        <td><span style="font-size: 0.82rem;">{{ $u->phone_number ?? '—' }}</span></td>
                        <td>
                            @if($u->email_verified_at)
                                <span style="color: #10b981; font-size: 0.82rem;">✓ Verified</span>
                            @else
                                <span style="color: #f59e0b; font-size: 0.82rem;">Unverified</span>
                            @endif
                        </td>
                        <td>
                            @if($u->is_banned)
                                <span class="status-badge rejected">Banned</span>
                            @elseif($u->is_suspended)
                                <span class="status-badge pending">Suspended</span>
                            @else
                                <span class="status-badge approved">Active</span>
                            @endif
                        </td>
                        <td><span style="font-size: 0.78rem; color: var(--admin-text-muted);">{{ $u->created_at?->format('M d, Y') ?? '—' }}</span></td>
                        <td>
                            @if($u->role !== 'admin')
                                <div style="display: flex; gap: 4px;">
                                    @if(!$u->is_suspended && !$u->is_banned)
                                        <form method="POST" action="{{ route('admin.users.suspend', $u) }}">
                                            @csrf
                                            <button type="submit" class="admin-btn admin-btn-warning admin-btn-sm" onclick="return confirm('Suspend {{ $u->name }}?')">Suspend</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.users.ban', $u) }}">
                                            @csrf
                                            <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm" onclick="return confirm('Ban {{ $u->name }}? This is a serious action.')">Ban</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.activate', $u) }}">
                                            @csrf
                                            <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm">Activate</button>
                                        </form>
                                    @endif
                                </div>
                            @else
                                <span style="font-size: 0.78rem; color: var(--admin-text-muted);">Protected</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 32px; color: var(--admin-text-muted);">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination">{{ $users->links() }}</div>
@endsection

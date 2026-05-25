<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Eventra Admin' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --admin-primary: #0f172a;
            --admin-secondary: #1e293b;
            --admin-accent: #10b981;
            --admin-accent-hover: #059669;
            --admin-border: #334155;
            --admin-text: #f1f5f9;
            --admin-text-muted: #94a3b8;
            --admin-bg: #0f172a;
            --admin-surface: #1e293b;
            --admin-card: rgba(30, 41, 59, 0.8);
        }

        .admin-shell {
            display: flex;
            min-height: 100vh;
            background: var(--admin-bg);
            color: var(--admin-text);
            font-family: 'Inter', -apple-system, sans-serif;
        }

        /* ── Admin Sidebar ── */
        .admin-sidebar {
            width: 260px;
            background: var(--admin-secondary);
            border-right: 1px solid var(--admin-border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            overflow-y: auto;
        }

        .admin-sidebar .sidebar-brand {
            padding: 24px 20px 16px;
            border-bottom: 1px solid var(--admin-border);
        }

        .admin-sidebar .sidebar-brand h2 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .admin-sidebar .sidebar-brand h2 a {
            color: var(--admin-text);
            text-decoration: none;
        }

        .admin-sidebar .sidebar-brand h2 a span {
            color: var(--admin-accent);
        }

        .admin-sidebar .sidebar-brand .badge {
            display: inline-block;
            background: var(--admin-accent);
            color: #fff;
            font-size: 0.6rem;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 700;
            text-transform: uppercase;
            margin-left: 6px;
            letter-spacing: 0.5px;
        }

        .admin-sidebar .admin-user-block {
            padding: 16px 20px;
            border-bottom: 1px solid var(--admin-border);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-sidebar .admin-user-block img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--admin-accent);
        }

        .admin-sidebar .admin-user-block .info strong {
            display: block;
            font-size: 0.85rem;
            color: var(--admin-text);
        }

        .admin-sidebar .admin-user-block .info span {
            font-size: 0.7rem;
            color: var(--admin-accent);
            font-weight: 600;
            text-transform: uppercase;
        }

        .admin-sidebar nav {
            padding: 12px 0;
            flex: 1;
        }

        .admin-sidebar .nav-section {
            padding: 8px 20px 4px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--admin-text-muted);
        }

        .admin-sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: var(--admin-text-muted);
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
        }

        .admin-sidebar .nav-link:hover {
            background: rgba(255,255,255,0.04);
            color: var(--admin-text);
        }

        .admin-sidebar .nav-link.active {
            background: rgba(16, 185, 129, 0.08);
            color: var(--admin-accent);
            border-left-color: var(--admin-accent);
            font-weight: 600;
        }

        .admin-sidebar .nav-link .icon {
            width: 20px;
            text-align: center;
            font-size: 1rem;
        }

        .admin-sidebar .nav-link .badge-count {
            margin-left: auto;
            background: #ef4444;
            color: #fff;
            font-size: 0.65rem;
            padding: 2px 7px;
            border-radius: 10px;
            font-weight: 700;
            min-width: 20px;
            text-align: center;
        }

        .admin-sidebar .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid var(--admin-border);
        }

        .admin-sidebar .sidebar-footer button {
            width: 100%;
            padding: 8px 12px;
            background: transparent;
            color: var(--admin-text-muted);
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .admin-sidebar .sidebar-footer button:hover {
            background: rgba(239, 68, 68, 0.1);
            border-color: #ef4444;
            color: #ef4444;
        }

        /* ── Admin Main Panel ── */
        .admin-main {
            flex: 1;
            margin-left: 260px;
            min-height: 100vh;
        }

        .admin-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px 32px;
            background: var(--admin-secondary);
            border-bottom: 1px solid var(--admin-border);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .admin-topbar h1 {
            margin: 0;
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--admin-text);
            letter-spacing: -0.3px;
        }

        .admin-topbar .topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-content {
            padding: 28px 32px;
        }

        /* ── Admin Cards ── */
        .admin-card {
            background: var(--admin-card);
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            padding: 24px;
            backdrop-filter: blur(12px);
        }

        .admin-card .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .admin-card .card-header h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: var(--admin-text);
        }

        /* ── Admin Table ── */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th {
            padding: 10px 12px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--admin-text-muted);
            border-bottom: 1px solid var(--admin-border);
            text-align: left;
        }

        .admin-table td {
            padding: 12px;
            font-size: 0.88rem;
            border-bottom: 1px solid rgba(51, 65, 85, 0.5);
            color: var(--admin-text);
        }

        .admin-table tr:hover td {
            background: rgba(255,255,255,0.02);
        }

        /* ── Status Badges ── */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .status-badge.pending { background: rgba(245,158,11,0.15); color: #f59e0b; }
        .status-badge.under_review { background: rgba(59,130,246,0.15); color: #3b82f6; }
        .status-badge.approved { background: rgba(16,185,129,0.15); color: #10b981; }
        .status-badge.rejected { background: rgba(239,68,68,0.15); color: #ef4444; }
        .status-badge.confirmed { background: rgba(16,185,129,0.15); color: #10b981; }
        .status-badge.negotiating { background: rgba(168,85,247,0.15); color: #a855f7; }
        .status-badge.cancelled { background: rgba(239,68,68,0.15); color: #ef4444; }
        .status-badge.suspended { background: rgba(239,68,68,0.15); color: #ef4444; }
        .status-badge.active { background: rgba(16,185,129,0.15); color: #10b981; }

        /* ── Admin Buttons ── */
        .admin-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            text-decoration: none;
        }

        .admin-btn-primary {
            background: var(--admin-accent);
            color: #fff;
        }

        .admin-btn-primary:hover {
            background: var(--admin-accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .admin-btn-danger {
            background: rgba(239,68,68,0.15);
            color: #ef4444;
            border: 1px solid rgba(239,68,68,0.3);
        }

        .admin-btn-danger:hover {
            background: rgba(239,68,68,0.25);
        }

        .admin-btn-warning {
            background: rgba(245,158,11,0.15);
            color: #f59e0b;
            border: 1px solid rgba(245,158,11,0.3);
        }

        .admin-btn-warning:hover {
            background: rgba(245,158,11,0.25);
        }

        .admin-btn-secondary {
            background: rgba(148,163,184,0.1);
            color: var(--admin-text-muted);
            border: 1px solid var(--admin-border);
        }

        .admin-btn-secondary:hover {
            background: rgba(148,163,184,0.2);
            color: var(--admin-text);
        }

        .admin-btn-sm {
            padding: 5px 10px;
            font-size: 0.75rem;
        }

        /* ── Admin Search ── */
        .admin-search {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--admin-primary);
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            padding: 8px 14px;
        }

        .admin-search input {
            background: transparent;
            border: none;
            outline: none;
            color: var(--admin-text);
            font-size: 0.88rem;
            width: 100%;
        }

        .admin-search input::placeholder {
            color: var(--admin-text-muted);
        }

        /* ── Filter Tabs ── */
        .admin-tabs {
            display: flex;
            gap: 4px;
            padding: 4px;
            background: var(--admin-primary);
            border-radius: 10px;
            border: 1px solid var(--admin-border);
        }

        .admin-tab {
            padding: 8px 16px;
            border-radius: 7px;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--admin-text-muted);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .admin-tab:hover {
            background: rgba(255,255,255,0.04);
            color: var(--admin-text);
        }

        .admin-tab.active {
            background: var(--admin-accent);
            color: #fff;
        }

        /* ── Stat Cards Grid ── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
        }

        .stat-card {
            background: var(--admin-card);
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .stat-card.emerald::before { background: linear-gradient(90deg, #10b981, #34d399); }
        .stat-card.blue::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
        .stat-card.amber::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
        .stat-card.purple::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
        .stat-card.rose::before { background: linear-gradient(90deg, #f43f5e, #fb7185); }
        .stat-card.cyan::before { background: linear-gradient(90deg, #06b6d4, #22d3ee); }

        .stat-card .stat-icon {
            font-size: 1.5rem;
            margin-bottom: 8px;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -1px;
            color: var(--admin-text);
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-card .stat-label {
            font-size: 0.78rem;
            color: var(--admin-text-muted);
            font-weight: 500;
        }

        /* ── Charts ── */
        .chart-container {
            position: relative;
            width: 100%;
        }

        .chart-container canvas {
            width: 100% !important;
        }

        /* ── Admin Grid ── */
        .admin-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .admin-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 1024px) {
            .admin-grid-2,
            .admin-grid-3 {
                grid-template-columns: 1fr;
            }
        }

        /* ── Activity Feed ── */
        .activity-item {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid rgba(51, 65, 85, 0.4);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-top: 6px;
            flex-shrink: 0;
        }

        .activity-dot.green { background: #10b981; }
        .activity-dot.red { background: #ef4444; }
        .activity-dot.blue { background: #3b82f6; }
        .activity-dot.amber { background: #f59e0b; }

        .activity-text {
            font-size: 0.85rem;
            color: var(--admin-text);
            line-height: 1.4;
        }

        .activity-time {
            font-size: 0.72rem;
            color: var(--admin-text-muted);
            margin-top: 2px;
        }

        /* ── Pipeline Bar ── */
        .pipeline-bar {
            display: flex;
            border-radius: 8px;
            overflow: hidden;
            height: 12px;
            background: var(--admin-primary);
        }

        .pipeline-segment {
            height: 100%;
            transition: width 0.6s ease;
        }

        .pipeline-segment.pending { background: #f59e0b; }
        .pipeline-segment.under_review { background: #3b82f6; }
        .pipeline-segment.approved { background: #10b981; }
        .pipeline-segment.rejected { background: #ef4444; }

        .pipeline-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-top: 12px;
        }

        .pipeline-legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            color: var(--admin-text-muted);
        }

        .pipeline-legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 2px;
        }

        /* ── Modal ── */
        .admin-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(4px);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }

        .admin-modal-overlay.active {
            display: flex;
        }

        .admin-modal {
            background: var(--admin-secondary);
            border: 1px solid var(--admin-border);
            border-radius: 16px;
            padding: 28px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        }

        .admin-modal h3 {
            margin: 0 0 16px;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .admin-modal textarea,
        .admin-modal input[type="text"] {
            width: 100%;
            background: var(--admin-primary);
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            padding: 10px 14px;
            color: var(--admin-text);
            font-size: 0.88rem;
            resize: vertical;
            margin-bottom: 16px;
        }

        .admin-modal textarea:focus,
        .admin-modal input[type="text"]:focus {
            outline: none;
            border-color: var(--admin-accent);
        }

        .admin-modal .modal-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        /* ── Admin Pagination ── */
        .admin-pagination {
            display: flex;
            justify-content: center;
            gap: 4px;
            margin-top: 20px;
        }

        .admin-pagination a,
        .admin-pagination span {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.82rem;
            color: var(--admin-text-muted);
            text-decoration: none;
            border: 1px solid var(--admin-border);
        }

        .admin-pagination a:hover {
            background: rgba(255,255,255,0.04);
            color: var(--admin-text);
        }

        .admin-pagination .active span {
            background: var(--admin-accent);
            color: #fff;
            border-color: var(--admin-accent);
        }

        /* ── Flash Messages ── */
        .admin-flash {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.88rem;
            font-weight: 500;
        }

        .admin-flash.success {
            background: rgba(16,185,129,0.1);
            border: 1px solid rgba(16,185,129,0.3);
            color: #10b981;
        }

        .admin-flash.error {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            color: #ef4444;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .admin-sidebar {
                display: none;
            }
            .admin-main {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    @php $user = auth()->user(); @endphp

    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <h2><a href="{{ route('admin.dashboard') }}">Eventra<span>.</span></a> <span class="badge">Admin</span></h2>
            </div>

            <div class="admin-user-block">
                <img src="{{ $user?->avatar_url }}" alt="Avatar">
                <div class="info">
                    <strong>{{ $user?->name }}</strong>
                    <span>Administrator</span>
                </div>
            </div>

            <nav>
                <div class="nav-section">Main</div>
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <span class="icon">📊</span> Dashboard
                </a>

                <div class="nav-section">Verification</div>
                <a class="nav-link {{ request()->routeIs('admin.vendor-verifications') ? 'active' : '' }}"
                    href="{{ route('admin.vendor-verifications') }}">
                    <span class="icon">✅</span> Vendor Verification
                    @php $pendingCount = \App\Models\Vendor::whereIn('verification_status', ['pending', 'under_review'])->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="badge-count">{{ $pendingCount }}</span>
                    @endif
                </a>

                <div class="nav-section">Management</div>
                <a class="nav-link {{ request()->routeIs('admin.vendors', 'admin.vendors.*') ? 'active' : '' }}"
                    href="{{ route('admin.vendors') }}">
                    <span class="icon">🏪</span> All Vendors
                </a>
                <a class="nav-link {{ request()->routeIs('admin.events', 'admin.events.*') ? 'active' : '' }}"
                    href="{{ route('admin.events') }}">
                    <span class="icon">📅</span> All Events
                </a>
                <a class="nav-link {{ request()->routeIs('admin.bookings') ? 'active' : '' }}"
                    href="{{ route('admin.bookings') }}">
                    <span class="icon">📋</span> Bookings
                </a>
                <a class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}"
                    href="{{ route('admin.users') }}">
                    <span class="icon">👥</span> Users
                </a>
            </nav>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">🚪 Sign Out</button>
                </form>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-topbar">
                <div>
                    <h1>@yield('page-title', 'Admin Dashboard')</h1>
                </div>
                <div class="topbar-actions">
                    @yield('topbar-actions')
                </div>
            </header>

            <div class="admin-content">
                @if(session('success'))
                    <div class="admin-flash success">✓ {{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="admin-flash error">✕ {{ session('error') }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>

@extends('layouts.admin')
@section('page-title', 'Dashboard')

@section('content')
    {{-- ── Stat Cards ── --}}
    <div class="stat-grid" style="margin-bottom: 28px;">
        <div class="stat-card emerald">
            <div class="stat-icon">📅</div>
            <div class="stat-value">{{ number_format($stats['total_events']) }}</div>
            <div class="stat-label">Total Events</div>
        </div>
        <div class="stat-card blue">
            <div class="stat-icon">🏪</div>
            <div class="stat-value">{{ number_format($stats['total_vendors']) }}</div>
            <div class="stat-label">Registered Vendors</div>
        </div>
        <div class="stat-card amber">
            <div class="stat-icon">⏳</div>
            <div class="stat-value">{{ number_format($stats['pending_verifications']) }}</div>
            <div class="stat-label">Pending Verifications</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-icon">📋</div>
            <div class="stat-value">{{ number_format($stats['active_bookings']) }}</div>
            <div class="stat-label">Active Bookings</div>
        </div>
        <div class="stat-card rose">
            <div class="stat-icon">🎯</div>
            <div class="stat-value">{{ number_format($stats['total_planners']) }}</div>
            <div class="stat-label">Event Planners</div>
        </div>
        <div class="stat-card cyan">
            <div class="stat-icon">👥</div>
            <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
            <div class="stat-label">Total Users</div>
        </div>
    </div>

    {{-- ── Vendor Verification Pipeline ── --}}
    <div class="admin-card" style="margin-bottom: 28px;">
        <div class="card-header">
            <h3>Vendor Verification Pipeline</h3>
            <a href="{{ route('admin.vendor-verifications') }}" class="admin-btn admin-btn-secondary admin-btn-sm">View All →</a>
        </div>

        @php
            $totalPipeline = array_sum($verificationPipeline);
        @endphp

        @if($totalPipeline > 0)
            <div class="pipeline-bar">
                <div class="pipeline-segment pending" style="width: {{ ($verificationPipeline['pending'] / $totalPipeline) * 100 }}%"></div>
                <div class="pipeline-segment under_review" style="width: {{ ($verificationPipeline['under_review'] / $totalPipeline) * 100 }}%"></div>
                <div class="pipeline-segment approved" style="width: {{ ($verificationPipeline['approved'] / $totalPipeline) * 100 }}%"></div>
                <div class="pipeline-segment rejected" style="width: {{ ($verificationPipeline['rejected'] / $totalPipeline) * 100 }}%"></div>
            </div>
        @else
            <div class="pipeline-bar">
                <div class="pipeline-segment" style="width: 100%; background: var(--admin-border);"></div>
            </div>
        @endif

        <div class="pipeline-legend">
            <div class="pipeline-legend-item">
                <div class="pipeline-legend-dot" style="background: #f59e0b;"></div>
                Pending ({{ $verificationPipeline['pending'] }})
            </div>
            <div class="pipeline-legend-item">
                <div class="pipeline-legend-dot" style="background: #3b82f6;"></div>
                Under Review ({{ $verificationPipeline['under_review'] }})
            </div>
            <div class="pipeline-legend-item">
                <div class="pipeline-legend-dot" style="background: #10b981;"></div>
                Approved ({{ $verificationPipeline['approved'] }})
            </div>
            <div class="pipeline-legend-item">
                <div class="pipeline-legend-dot" style="background: #ef4444;"></div>
                Rejected ({{ $verificationPipeline['rejected'] }})
            </div>
        </div>
    </div>

    {{-- ── Charts Row ── --}}
    <div class="admin-grid-2" style="margin-bottom: 28px;">
        {{-- Monthly Registrations Chart --}}
        <div class="admin-card">
            <div class="card-header">
                <h3>Monthly User Registrations</h3>
            </div>
            <div class="chart-container">
                <canvas id="registrationsChart" height="200"></canvas>
            </div>
        </div>

        {{-- Monthly Bookings Chart --}}
        <div class="admin-card">
            <div class="card-header">
                <h3>Monthly Bookings</h3>
            </div>
            <div class="chart-container">
                <canvas id="bookingsChart" height="200"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Bottom Row: Recent Activity + Upcoming Events + Pending Vendors ── --}}
    <div class="admin-grid-3">
        {{-- Recent Activity --}}
        <div class="admin-card">
            <div class="card-header">
                <h3>Recent Activity</h3>
            </div>
            @forelse($recentActivity as $log)
                <div class="activity-item">
                    <div class="activity-dot {{ str_contains($log->action, 'approved') || str_contains($log->action, 'activated') ? 'green' : (str_contains($log->action, 'rejected') || str_contains($log->action, 'banned') || str_contains($log->action, 'suspended') ? 'red' : 'blue') }}"></div>
                    <div>
                        <div class="activity-text">
                            {{ str_replace('_', ' ', ucfirst($log->action)) }}
                            @if($log->details && isset($log->details['business_name']))
                                — {{ $log->details['business_name'] }}
                            @elseif($log->details && isset($log->details['name']))
                                — {{ $log->details['name'] }}
                            @endif
                        </div>
                        <div class="activity-time">{{ $log->created_at?->diffForHumans() ?? 'just now' }}</div>
                    </div>
                </div>
            @empty
                <p style="color: var(--admin-text-muted); font-size: 0.85rem; text-align: center; padding: 20px 0;">No activity logged yet.</p>
            @endforelse
        </div>

        {{-- Upcoming Events --}}
        <div class="admin-card">
            <div class="card-header">
                <h3>Upcoming Events</h3>
            </div>
            @forelse($upcomingEvents as $event)
                <div class="activity-item">
                    <div class="activity-dot blue"></div>
                    <div>
                        <div class="activity-text">{{ $event->event_name }}</div>
                        <div class="activity-time">
                            {{ $event->event_date?->format('M d, Y') ?? 'TBA' }}
                            · {{ $event->category ?? 'General' }}
                        </div>
                    </div>
                </div>
            @empty
                <p style="color: var(--admin-text-muted); font-size: 0.85rem; text-align: center; padding: 20px 0;">No upcoming events.</p>
            @endforelse
        </div>

        {{-- Pending Vendor Applications --}}
        <div class="admin-card">
            <div class="card-header">
                <h3>Pending Applications</h3>
                <a href="{{ route('admin.vendor-verifications') }}" class="admin-btn admin-btn-secondary admin-btn-sm">View All</a>
            </div>
            @forelse($recentVendorApps as $vendor)
                <div class="activity-item">
                    <div class="activity-dot amber"></div>
                    <div style="flex: 1; min-width: 0;">
                        <div class="activity-text" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $vendor->business_name ?: $vendor->name ?: 'Unnamed' }}
                        </div>
                        <div class="activity-time">
                            {{ $vendor->category ?? 'No category' }} · <span class="status-badge {{ $vendor->verification_status }}">{{ str_replace('_', ' ', $vendor->verification_status) }}</span>
                        </div>
                    </div>
                    <a href="{{ route('admin.vendors.show', $vendor) }}" class="admin-btn admin-btn-secondary admin-btn-sm" style="flex-shrink: 0;">Review</a>
                </div>
            @empty
                <p style="color: var(--admin-text-muted); font-size: 0.85rem; text-align: center; padding: 20px 0;">No pending applications.</p>
            @endforelse
        </div>
    </div>

    {{-- ── Charts JS (lightweight, no CDN dependency) ── --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const regLabels = @json($monthlyRegistrations->keys()->values());
            const regValues = @json($monthlyRegistrations->values());
            const bookLabels = @json($monthlyBookings->keys()->values());
            const bookValues = @json($monthlyBookings->values());

            function drawBarChart(canvasId, labels, data, color) {
                const canvas = document.getElementById(canvasId);
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                const dpr = window.devicePixelRatio || 1;
                const rect = canvas.parentElement.getBoundingClientRect();
                canvas.width = rect.width * dpr;
                canvas.height = 200 * dpr;
                canvas.style.width = rect.width + 'px';
                canvas.style.height = '200px';
                ctx.scale(dpr, dpr);

                const W = rect.width;
                const H = 200;
                const pad = { top: 20, right: 20, bottom: 40, left: 50 };
                const chartW = W - pad.left - pad.right;
                const chartH = H - pad.top - pad.bottom;
                const maxVal = Math.max(...data, 1);

                ctx.clearRect(0, 0, W, H);

                // Y-axis gridlines
                ctx.strokeStyle = 'rgba(51,65,85,0.4)';
                ctx.lineWidth = 0.5;
                for (let i = 0; i <= 4; i++) {
                    const y = pad.top + (chartH / 4) * i;
                    ctx.beginPath();
                    ctx.moveTo(pad.left, y);
                    ctx.lineTo(W - pad.right, y);
                    ctx.stroke();
                    ctx.fillStyle = '#94a3b8';
                    ctx.font = '11px Inter, sans-serif';
                    ctx.textAlign = 'right';
                    ctx.fillText(Math.round(maxVal - (maxVal / 4) * i), pad.left - 8, y + 4);
                }

                // Bars
                const barW = Math.min(40, chartW / labels.length - 10);
                const gap = (chartW - barW * labels.length) / (labels.length + 1);

                data.forEach((val, i) => {
                    const x = pad.left + gap * (i + 1) + barW * i;
                    const barH = (val / maxVal) * chartH;
                    const y = pad.top + chartH - barH;

                    // Bar gradient
                    const grad = ctx.createLinearGradient(x, y, x, y + barH);
                    grad.addColorStop(0, color);
                    grad.addColorStop(1, color + '60');
                    ctx.fillStyle = grad;
                    ctx.beginPath();
                    ctx.roundRect(x, y, barW, barH, [4, 4, 0, 0]);
                    ctx.fill();

                    // Label
                    ctx.fillStyle = '#94a3b8';
                    ctx.font = '11px Inter, sans-serif';
                    ctx.textAlign = 'center';
                    ctx.fillText(labels[i], x + barW / 2, H - pad.bottom + 18);

                    // Value on top
                    if (val > 0) {
                        ctx.fillStyle = '#f1f5f9';
                        ctx.font = 'bold 11px Inter, sans-serif';
                        ctx.fillText(val, x + barW / 2, y - 6);
                    }
                });
            }

            drawBarChart('registrationsChart', regLabels, regValues, '#10b981');
            drawBarChart('bookingsChart', bookLabels, bookValues, '#3b82f6');
        });
    </script>
@endsection

@extends('layouts.admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-8 pb-12">
    {{-- ── Stat Cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4" data-animate="stagger">
        <x-stat-card label="Total Events" value="{{ number_format($stats['total_events']) }}" icon="📅" />
        <x-stat-card label="Registered Vendors" value="{{ number_format($stats['total_vendors']) }}" icon="🏪" />
        <x-stat-card label="Pending Verifications" value="{{ number_format($stats['pending_verifications']) }}" icon="⏳" />
        <x-stat-card label="Active Bookings" value="{{ number_format($stats['active_bookings']) }}" icon="📋" />
        <x-stat-card label="Event Planners" value="{{ number_format($stats['total_planners']) }}" icon="🎯" />
        <x-stat-card label="Total Users" value="{{ number_format($stats['total_users']) }}" icon="👥" />
    </div>

    {{-- ── Vendor Verification Pipeline ── --}}
    <x-card data-animate="fade-up">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-h3 font-bold text-neutral-dark">Vendor Verification Pipeline</h3>
                <p class="text-caption text-surface-500 mt-1">Real-time status of all business verification requests.</p>
            </div>
            <x-btn variant="outline" size="sm" href="{{ route('admin.vendor-verifications') }}">View All &rarr;</x-btn>
        </div>

        @php
            $totalPipeline = array_sum($verificationPipeline);
        @endphp

        <div class="w-full bg-surface-100 rounded-full h-3 flex overflow-hidden">
            @if($totalPipeline > 0)
                <div class="bg-amber-500 transition-all duration-300" style="width: {{ ($verificationPipeline['pending'] / $totalPipeline) * 100 }}%" title="Pending: {{ $verificationPipeline['pending'] }}"></div>
                <div class="bg-blue-500 transition-all duration-300" style="width: {{ ($verificationPipeline['under_review'] / $totalPipeline) * 100 }}%" title="Under Review: {{ $verificationPipeline['under_review'] }}"></div>
                <div class="bg-success transition-all duration-300" style="width: {{ ($verificationPipeline['approved'] / $totalPipeline) * 100 }}%" title="Approved: {{ $verificationPipeline['approved'] }}"></div>
                <div class="bg-danger transition-all duration-300" style="width: {{ ($verificationPipeline['rejected'] / $totalPipeline) * 100 }}%" title="Rejected: {{ $verificationPipeline['rejected'] }}"></div>
            @else
                <div class="bg-surface-200 w-full"></div>
            @endif
        </div>

        <div class="flex flex-wrap gap-6 mt-4 text-caption font-medium text-surface-600">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-xs bg-amber-500"></span>
                <span>Pending ({{ $verificationPipeline['pending'] }})</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-xs bg-blue-500"></span>
                <span>Under Review ({{ $verificationPipeline['under_review'] }})</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-xs bg-success"></span>
                <span>Approved ({{ $verificationPipeline['approved'] }})</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-xs bg-danger"></span>
                <span>Rejected ({{ $verificationPipeline['rejected'] }})</span>
            </div>
        </div>
    </x-card>

    {{-- ── Charts Row ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Monthly Registrations Chart --}}
        <x-card data-animate="fade-up">
            <h3 class="text-h3 font-bold text-neutral-dark mb-4">Monthly User Registrations</h3>
            <div class="relative w-full h-[220px]">
                <canvas id="registrationsChart"></canvas>
            </div>
        </x-card>

        {{-- Monthly Bookings Chart --}}
        <x-card data-animate="fade-up">
            <h3 class="text-h3 font-bold text-neutral-dark mb-4">Monthly Bookings</h3>
            <div class="relative w-full h-[220px]">
                <canvas id="bookingsChart"></canvas>
            </div>
        </x-card>
    </div>

    {{-- ── Bottom Row: Recent Activity + Upcoming Events + Pending Vendors ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Recent Activity --}}
        <x-card data-animate="fade-up" class="flex flex-col justify-between">
            <div>
                <h3 class="text-h3 font-bold text-neutral-dark mb-4 border-b border-surface-100 pb-2">Recent Activity</h3>
                <div class="divide-y divide-surface-100">
                    @forelse($recentActivity as $log)
                        @php
                            $isGood = str_contains($log->action, 'approved') || str_contains($log->action, 'activated');
                            $isBad = str_contains($log->action, 'rejected') || str_contains($log->action, 'banned') || str_contains($log->action, 'suspended');
                            $dotColor = $isGood ? 'bg-success' : ($isBad ? 'bg-danger' : 'bg-primary-500');
                        @endphp
                        <div class="py-3 flex gap-3 items-start">
                            <span class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 {{ $dotColor }}"></span>
                            <div class="min-w-0 flex-1">
                                <p class="text-body font-medium text-neutral-dark leading-snug">
                                    {{ str_replace('_', ' ', ucfirst($log->action)) }}
                                    @if($log->details && isset($log->details['business_name']))
                                        <span class="text-primary-500 font-semibold">— {{ $log->details['business_name'] }}</span>
                                    @elseif($log->details && isset($log->details['name']))
                                        <span class="text-primary-500 font-semibold">— {{ $log->details['name'] }}</span>
                                    @endif
                                </p>
                                <span class="text-[10px] text-surface-400 block mt-0.5">{{ $log->created_at?->diffForHumans() ?? 'just now' }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-caption text-surface-400 text-center py-6">No activity logged yet.</p>
                    @endforelse
                </div>
            </div>
        </x-card>

        {{-- Upcoming Events --}}
        <x-card data-animate="fade-up" class="flex flex-col justify-between">
            <div>
                <h3 class="text-h3 font-bold text-neutral-dark mb-4 border-b border-surface-100 pb-2">Upcoming Events</h3>
                <div class="divide-y divide-surface-100">
                    @forelse($upcomingEvents as $event)
                        <div class="py-3 flex gap-3 items-start">
                            <span class="w-2 h-2 rounded-full mt-1.5 bg-blue-500 flex-shrink-0"></span>
                            <div class="min-w-0 flex-1">
                                <p class="text-body font-bold text-neutral-dark truncate">{{ $event->event_name }}</p>
                                <p class="text-caption text-surface-500 mt-0.5 truncate">
                                    {{ $event->event_date?->format('M d, Y') ?? 'TBA' }} &middot; {{ ucfirst($event->category ?? 'General') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-caption text-surface-400 text-center py-6">No upcoming events.</p>
                    @endforelse
                </div>
            </div>
        </x-card>

        {{-- Pending Vendor Applications --}}
        <x-card data-animate="fade-up" class="flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center mb-4 border-b border-surface-100 pb-2">
                    <h3 class="text-h3 font-bold text-neutral-dark">Pending Applications</h3>
                    <x-btn variant="ghost" size="sm" class="!p-0" href="{{ route('admin.vendor-verifications') }}">View All</x-btn>
                </div>
                <div class="divide-y divide-surface-100">
                    @forelse($recentVendorApps as $appBiz)
                        <div class="py-3 flex justify-between items-center gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="text-body font-bold text-neutral-dark truncate">{{ $appBiz->business_name ?: $appBiz->name ?: 'Unnamed' }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-caption text-surface-500 truncate">{{ ucfirst($appBiz->category ?? 'Service') }}</span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-surface-300"></span>
                                    @php
                                        $badgeVar = match($appBiz->verification_status) {
                                            'pending' => 'warning',
                                            'under_review' => 'info',
                                            'approved' => 'success',
                                            default => 'gray',
                                        };
                                    @endphp
                                    <x-badge :variant="$badgeVar" class="text-[9px] uppercase tracking-wider py-0 px-1.5">{{ str_replace('_', ' ', $appBiz->verification_status) }}</x-badge>
                                </div>
                            </div>
                            <x-btn variant="outline" size="sm" href="{{ route('admin.vendors.show', $appBiz) }}" class="flex-shrink-0">Review</x-btn>
                        </div>
                    @empty
                        <p class="text-caption text-surface-400 text-center py-6">No pending applications.</p>
                    @endforelse
                </div>
            </div>
        </x-card>
    </div>
</div>

{{-- ── Charts JS ── --}}
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
            canvas.height = 220 * dpr;
            canvas.style.width = rect.width + 'px';
            canvas.style.height = '220px';
            ctx.scale(dpr, dpr);

            const W = rect.width;
            const H = 220;
            const pad = { top: 30, right: 20, bottom: 40, left: 40 };
            const chartW = W - pad.left - pad.right;
            const chartH = H - pad.top - pad.bottom;
            const maxVal = Math.max(...data, 1);

            ctx.clearRect(0, 0, W, H);

            // Y-axis gridlines
            ctx.strokeStyle = 'rgba(229,231,235,0.8)';
            ctx.lineWidth = 1;
            for (let i = 0; i <= 4; i++) {
                const y = pad.top + (chartH / 4) * i;
                ctx.beginPath();
                ctx.moveTo(pad.left, y);
                ctx.lineTo(W - pad.right, y);
                ctx.stroke();
                
                ctx.fillStyle = '#737373';
                ctx.font = '10px Sequel Sans, system-ui, sans-serif';
                ctx.textAlign = 'right';
                ctx.fillText(Math.round(maxVal - (maxVal / 4) * i), pad.left - 8, y + 3);
            }

            // Bars
            const barW = Math.min(32, chartW / labels.length - 12);
            const gap = (chartW - barW * labels.length) / (labels.length + 1);

            data.forEach((val, i) => {
                const x = pad.left + gap * (i + 1) + barW * i;
                const barH = (val / maxVal) * chartH;
                const y = pad.top + chartH - barH;

                // Bar gradient
                const grad = ctx.createLinearGradient(x, y, x, y + barH);
                grad.addColorStop(0, color);
                grad.addColorStop(1, color + '50');
                ctx.fillStyle = grad;
                ctx.beginPath();
                
                // Draw rounded rectangles if supported, otherwise normal rect
                if (ctx.roundRect) {
                    ctx.roundRect(x, y, barW, barH, [4, 4, 0, 0]);
                } else {
                    ctx.rect(x, y, barW, barH);
                }
                ctx.fill();

                // Label
                ctx.fillStyle = '#737373';
                ctx.font = '10px Sequel Sans, system-ui, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText(labels[i], x + barW / 2, H - pad.bottom + 16);

                // Value on top
                if (val > 0) {
                    ctx.fillStyle = '#404040';
                    ctx.font = 'bold 10px Sequel Sans, system-ui, sans-serif';
                    ctx.fillText(val, x + barW / 2, y - 6);
                }
            });
        }

        // Delay slightly for accurate bounding client rect calculations
        setTimeout(() => {
            drawBarChart('registrationsChart', regLabels, regValues, '#6C5CE7');
            drawBarChart('bookingsChart', bookLabels, bookValues, '#A855F7');
        }, 150);

        window.addEventListener('resize', () => {
            drawBarChart('registrationsChart', regLabels, regValues, '#6C5CE7');
            drawBarChart('bookingsChart', bookLabels, bookValues, '#A855F7');
        });
    });
</script>
@endsection

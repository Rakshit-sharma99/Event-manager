@extends('layouts.app', ['title' => 'Smart Budget Planner - Eventra'])
@section('page-title', 'Smart Budget Planner')

@section('content')
<style>
    .sb-container { max-width: 100%; }

    /* Header */
    .sb-hero { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff; border-radius: 16px; padding: 28px 32px; margin-bottom: 24px; }
    .sb-hero h2 { margin: 0 0 4px; font-size: 1.5rem; font-weight: 800; }
    .sb-hero .sb-meta { color: #94a3b8; font-size: 0.9rem; }
    .sb-hero-stats { display: flex; gap: 24px; margin-top: 16px; flex-wrap: wrap; }
    .sb-hero-stat { background: rgba(255,255,255,0.07); border-radius: 12px; padding: 12px 20px; min-width: 140px; }
    .sb-hero-stat small { display: block; color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; }
    .sb-hero-stat strong { font-size: 1.25rem; color: #fff; }

    /* Confidence meter */
    .confidence-ring { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 1.3rem; color: #fff; position: relative; flex-shrink: 0; }

    /* Config panel */
    .sb-config-panel { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; margin-bottom: 24px; }
    .sb-config-panel h3 { margin: 0 0 16px; font-size: 1.15rem; font-weight: 800; color: #0f172a; }

    /* Luxury selector */
    .luxury-grid { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
    .luxury-card { padding: 12px 20px; border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer; text-align: center; transition: all 0.2s; min-width: 110px; }
    .luxury-card:hover { border-color: #3b82f6; background: #eff6ff; }
    .luxury-card.active { border-color: #2563eb; background: #dbeafe; }
    .luxury-card .lux-label { font-weight: 700; font-size: 0.95rem; color: #0f172a; }
    .luxury-card .lux-desc { font-size: 0.75rem; color: #64748b; margin-top: 2px; }

    /* Service grid */
    .service-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(145px, 1fr)); gap: 10px; margin-bottom: 20px; }
    .service-card { border: 2px solid #e2e8f0; border-radius: 12px; padding: 14px 10px; cursor: pointer; text-align: center; transition: all 0.2s; position: relative; }
    .service-card:hover { border-color: #3b82f6; transform: translateY(-2px); }
    .service-card.selected { border-color: #10b981; background: #ecfdf5; }
    .service-card .svc-icon { font-size: 1.6rem; display: block; margin-bottom: 4px; }
    .service-card .svc-name { font-weight: 700; font-size: 0.82rem; color: #0f172a; }
    .service-card .svc-check { position: absolute; top: 6px; right: 8px; font-size: 0.85rem; display: none; }
    .service-card.selected .svc-check { display: block; }

    /* Generate button */
    .sb-generate-btn { background: linear-gradient(135deg, #2563eb, #7c3aed); color: #fff; border: none; padding: 14px 32px; border-radius: 12px; font-weight: 800; font-size: 1rem; cursor: pointer; transition: all 0.3s; }
    .sb-generate-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(37,99,235,0.3); }
    .sb-generate-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; box-shadow: none; }

    /* Allocations panel */
    .sb-alloc-panel { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; margin-bottom: 24px; }
    .alloc-row { display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f1f5f9; flex-wrap: wrap; }
    .alloc-row:last-child { border-bottom: none; }
    .alloc-cat-name { font-weight: 700; font-size: 0.9rem; color: #0f172a; min-width: 130px; }
    .alloc-amount { font-weight: 800; font-size: 0.95rem; color: #047857; min-width: 110px; }
    .alloc-bar-wrap { flex: 1; min-width: 150px; height: 10px; background: #e5e7eb; border-radius: 5px; overflow: hidden; }
    .alloc-bar-fill { height: 100%; border-radius: 5px; transition: width 0.5s ease; }
    .alloc-priority-select { padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.8rem; font-weight: 600; }
    .alloc-lock-btn { background: none; border: 1px solid #d1d5db; border-radius: 6px; padding: 4px 8px; cursor: pointer; font-size: 0.85rem; transition: all 0.15s; }
    .alloc-lock-btn.locked { background: #fef3c7; border-color: #f59e0b; }
    .alloc-percent { font-size: 0.8rem; color: #64748b; font-weight: 600; min-width: 40px; text-align: right; }

    /* Warnings */
    .sb-warnings { margin-bottom: 24px; }
    .sb-warning-item { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 10px; margin-bottom: 8px; font-size: 0.88rem; font-weight: 600; }
    .sb-warning-item.critical { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .sb-warning-item.warning { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }

    /* Savings panel */
    .sb-savings-panel { background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%); border: 1px solid #a7f3d0; border-radius: 16px; padding: 20px 24px; margin-bottom: 24px; }
    .sb-savings-total { font-size: 1.3rem; font-weight: 900; color: #047857; }
    .sb-saving-item { padding: 8px 0; font-size: 0.88rem; color: #065f46; border-bottom: 1px solid #d1fae5; }
    .sb-saving-item:last-child { border-bottom: none; }

    /* Vendor recommendations */
    .sb-reco-section { margin-bottom: 24px; }
    .sb-reco-cat-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px; }
    .sb-reco-cat-title { font-weight: 800; font-size: 1.05rem; color: #0f172a; }
    .sb-reco-filters { display: flex; gap: 6px; }
    .sb-reco-filter-btn { padding: 5px 12px; border: 1px solid #d1d5db; border-radius: 20px; background: #fff; cursor: pointer; font-size: 0.78rem; font-weight: 700; color: #64748b; transition: all 0.15s; }
    .sb-reco-filter-btn:hover, .sb-reco-filter-btn.active { background: #2563eb; color: #fff; border-color: #2563eb; }
    .sb-reco-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 14px; }
    .sb-vendor-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px; transition: all 0.2s; position: relative; }
    .sb-vendor-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.06); transform: translateY(-2px); }
    .sb-vendor-label { position: absolute; top: 10px; right: 10px; padding: 3px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
    .sb-vendor-label.best-value { background: #dbeafe; color: #1d4ed8; }
    .sb-vendor-label.premium { background: #fae8ff; color: #7e22ce; }
    .sb-vendor-label.budget-friendly { background: #dcfce7; color: #166534; }
    .sb-vendor-name { font-weight: 800; font-size: 1rem; color: #0f172a; margin: 0 0 4px; }
    .sb-vendor-rating { color: #f59e0b; font-weight: 700; font-size: 0.85rem; }
    .sb-vendor-price { font-weight: 800; color: #047857; font-size: 0.95rem; margin: 6px 0; }
    .sb-vendor-match { display: inline-block; background: linear-gradient(135deg, #2563eb, #7c3aed); color: #fff; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; margin-bottom: 8px; }
    .sb-vendor-reasons { margin: 8px 0; }
    .sb-vendor-reason { font-size: 0.78rem; color: #475569; padding: 2px 0; }
    .sb-vendor-reason::before { content: '✓ '; color: #10b981; font-weight: 700; }
    .sb-vendor-actions { display: flex; gap: 8px; margin-top: 10px; }
    .sb-vendor-actions a { flex: 1; text-align: center; padding: 8px 0; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 0.85rem; transition: all 0.15s; }
    .sb-btn-view { background: #f1f5f9; color: #334155; }
    .sb-btn-view:hover { background: #e2e8f0; }
    .sb-btn-book { background: #2563eb; color: #fff; }
    .sb-btn-book:hover { background: #1d4ed8; }

    /* Not available badge */
    .sb-unavailable { opacity: 0.6; }
    .sb-unavailable-badge { background: #fef2f2; color: #dc2626; font-size: 0.72rem; font-weight: 700; padding: 2px 8px; border-radius: 4px; display: inline-block; margin-top: 4px; }

    /* Loading spinner */
    .sb-loading { text-align: center; padding: 32px; color: #64748b; }
    .sb-spinner { display: inline-block; width: 28px; height: 28px; border: 3px solid #e2e8f0; border-top-color: #2563eb; border-radius: 50%; animation: sbSpin 0.7s linear infinite; }
    @keyframes sbSpin { to { transform: rotate(360deg); } }

    @media (max-width: 640px) {
        .sb-hero-stats { gap: 10px; }
        .sb-hero-stat { min-width: 100px; padding: 10px 14px; }
        .service-grid { grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); }
        .alloc-row { flex-direction: column; align-items: flex-start; }
    }
</style>

<div class="sb-container">

    <!-- Hero Header -->
    <div class="sb-hero">
        <h2>🧠 Smart Budget Planner</h2>
        <p class="sb-meta">{{ $event->event_name }} · {{ $event->category }} · {{ optional($event->event_date)->format('M d, Y') }}</p>
        <div class="sb-hero-stats">
            <div class="sb-hero-stat"><small>Total Budget</small><strong>₹{{ number_format($event->total_budget) }}</strong></div>
            <div class="sb-hero-stat"><small>Expected Guests</small><strong>{{ $event->guest_count_expected ?? 150 }}</strong></div>
            <div class="sb-hero-stat"><small>Location</small><strong>{{ $event->location ?? 'N/A' }}</strong></div>
            @if($hasAllocations && $confidence)
                <div class="sb-hero-stat" style="display: flex; align-items: center; gap: 14px;">
                    <?php
                        $confScore = $confidence['overall'] ?? 0;
                        $confColor = $confScore >= 75 ? '#10b981' : ($confScore >= 50 ? '#f59e0b' : '#ef4444');
                    ?>
                    <div class="confidence-ring" style="background: conic-gradient({{ $confColor }} {{ $confScore * 3.6 }}deg, rgba(255,255,255,0.1) 0deg);">
                        {{ $confScore }}%
                    </div>
                    <div><small>Confidence</small><strong style="font-size: 0.9rem;">Budget Plan Score</strong></div>
                </div>
            @endif
        </div>
    </div>

    <!-- Back to event -->
    <p style="margin-bottom: 16px;"><a href="{{ route('events.show', $event) }}">← Back to event</a></p>

    <!-- CONFIGURATION PANEL -->
    <div class="sb-config-panel" id="config-panel">
        <h3>1. Configure Your Budget Plan</h3>

        <!-- Luxury Level -->
        <label style="font-weight: 700; margin-bottom: 8px; display: block; color: #475569; font-size: 0.85rem;">LUXURY LEVEL</label>
        <div class="luxury-grid">
            @foreach($luxuryLevels as $key => $lux)
                <div class="luxury-card {{ ($event->luxury_level ?? 'balanced') === $key ? 'active' : '' }}"
                     data-level="{{ $key }}" onclick="selectLuxury('{{ $key }}')">
                    <div class="lux-label">{{ $lux['label'] }}</div>
                    <div class="lux-desc">{{ $lux['multiplier'] }}x premium</div>
                </div>
            @endforeach
        </div>

        <!-- Services -->
        <label style="font-weight: 700; margin-bottom: 8px; display: block; color: #475569; font-size: 0.85rem;">SELECT REQUIRED SERVICES</label>
        <div class="service-grid">
            @foreach($services as $key => $svc)
                <?php $isSelected = $selections->contains('service_name', $key); ?>
                <div class="service-card {{ $isSelected ? 'selected' : '' }}"
                     data-service="{{ $key }}" onclick="toggleService('{{ $key }}')">
                    <span class="svc-check">✅</span>
                    <span class="svc-icon">{{ $svc['icon'] }}</span>
                    <span class="svc-name">{{ $svc['label'] }}</span>
                </div>
            @endforeach
        </div>

        <button class="sb-generate-btn" id="generate-btn" onclick="generateAllocations()">
            {{ $hasAllocations ? '🔄 Regenerate Smart Budget' : '⚡ Generate Smart Budget' }}
        </button>
    </div>

    <!-- WARNINGS -->
    <div class="sb-warnings" id="warnings-container">
        @if(!empty($warnings))
            @foreach($warnings as $w)
                <div class="sb-warning-item {{ $w['severity'] }}">
                    ⚠️ {{ $w['message'] }}
                </div>
            @endforeach
        @endif
    </div>

    <!-- ALLOCATIONS DASHBOARD -->
    <div class="sb-alloc-panel" id="alloc-panel" style="{{ $hasAllocations ? '' : 'display:none;' }}">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 16px;">
            <h3 style="margin: 0;">2. Budget Allocations</h3>
            <button onclick="saveRebalance()" style="background: #0f172a; color: #fff; border: none; padding: 8px 18px; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">Rebalance</button>
        </div>
        <div id="alloc-list">
            @if($hasAllocations)
                @foreach($allocations as $alloc)
                    <?php
                        $pct = $event->total_budget > 0 ? round($alloc->allocated_amount / $event->total_budget * 100) : 0;
                        $barColor = $pct > 35 ? '#f59e0b' : ($pct > 20 ? '#3b82f6' : '#10b981');
                        $icon = config("smart_budget.services.{$alloc->category}.icon", '📋');
                        $label = config("smart_budget.services.{$alloc->category}.label", ucfirst($alloc->category));
                    ?>
                    <div class="alloc-row" data-category="{{ $alloc->category }}">
                        <span style="font-size: 1.2rem;">{{ $icon }}</span>
                        <span class="alloc-cat-name">{{ $label }}</span>
                        <span class="alloc-amount">₹{{ number_format($alloc->allocated_amount) }}</span>
                        <span class="alloc-percent">{{ $pct }}%</span>
                        <div class="alloc-bar-wrap"><div class="alloc-bar-fill" style="width: {{ $pct }}%; background: {{ $barColor }};"></div></div>
                        <select class="alloc-priority-select" data-cat="{{ $alloc->category }}" onchange="markDirty()">
                            <option value="low" {{ $alloc->priority_level === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ $alloc->priority_level === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ $alloc->priority_level === 'high' ? 'selected' : '' }}>High</option>
                        </select>
                        <button class="alloc-lock-btn {{ $alloc->is_locked ? 'locked' : '' }}" data-cat="{{ $alloc->category }}" onclick="toggleLock('{{ $alloc->category }}')">
                            {{ $alloc->is_locked ? '🔒' : '🔓' }}
                        </button>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- SAVINGS SUGGESTIONS -->
    @if($savings && ($savings['total_savings'] ?? 0) > 0)
        <div class="sb-savings-panel" id="savings-panel">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div>
                    <h3 style="margin: 0; color: #065f46;">💡 Potential Savings</h3>
                    <span class="sb-savings-total">₹{{ number_format($savings['total_savings']) }}</span>
                </div>
            </div>
            @foreach($savings['suggestions'] as $s)
                <div class="sb-saving-item">💰 {{ $s['message'] }}</div>
            @endforeach
        </div>
    @endif

    <!-- VENDOR RECOMMENDATIONS -->
    <div id="reco-container" style="{{ $hasAllocations ? '' : 'display:none;' }}">
        <h3 style="margin-bottom: 16px;">3. Vendor Recommendations</h3>
        @if($hasAllocations)
            @foreach($allocations as $alloc)
                <div class="sb-reco-section" id="reco-{{ $alloc->category }}">
                    <div class="sb-reco-cat-header">
                        <span class="sb-reco-cat-title">
                            {{ config("smart_budget.services.{$alloc->category}.icon", '📋') }}
                            {{ config("smart_budget.services.{$alloc->category}.label", ucfirst($alloc->category)) }}
                            <span style="font-weight: 400; color: #64748b; font-size: 0.85rem;">— Budget: ₹{{ number_format($alloc->allocated_amount) }}</span>
                        </span>
                        <div class="sb-reco-filters">
                            <button class="sb-reco-filter-btn active" onclick="loadRecommendations('{{ $alloc->category }}', 'best_match', this)">Best Match</button>
                            <button class="sb-reco-filter-btn" onclick="loadRecommendations('{{ $alloc->category }}', 'cheaper', this)">Cheaper</button>
                            <button class="sb-reco-filter-btn" onclick="loadRecommendations('{{ $alloc->category }}', 'premium', this)">Premium</button>
                        </div>
                    </div>
                    <div class="sb-reco-grid" id="reco-grid-{{ $alloc->category }}">
                        <div class="sb-loading"><div class="sb-spinner"></div><br>Finding vendors...</div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- REAL-TIME TRACKING -->
    <div id="tracking-panel" style="{{ $hasAllocations ? '' : 'display:none;' }}; margin-top: 24px;">
        <h3>4. Budget Tracking</h3>
        <div id="tracking-content">
            <div class="sb-loading"><div class="sb-spinner"></div><br>Loading tracking data...</div>
        </div>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
const eventId = '{{ $event->getKey() }}';
let selectedLuxury = '{{ $event->luxury_level ?? 'balanced' }}';
let selectedServices = @json($selections->pluck('service_name')->all());

function selectLuxury(level) {
    selectedLuxury = level;
    document.querySelectorAll('.luxury-card').forEach(c => {
        c.classList.toggle('active', c.dataset.level === level);
    });
}

function toggleService(svc) {
    const idx = selectedServices.indexOf(svc);
    if (idx > -1) { selectedServices.splice(idx, 1); } else { selectedServices.push(svc); }
    document.querySelectorAll('.service-card').forEach(c => {
        c.classList.toggle('selected', selectedServices.includes(c.dataset.service));
    });
}

function markDirty() {}

function toggleLock(cat) {
    const btn = document.querySelector(`.alloc-lock-btn[data-cat="${cat}"]`);
    if (btn.classList.contains('locked')) {
        btn.classList.remove('locked');
        btn.textContent = '🔓';
    } else {
        btn.classList.add('locked');
        btn.textContent = '🔒';
    }
}

function generateAllocations() {
    if (selectedServices.length === 0) { alert('Please select at least one service.'); return; }

    const btn = document.getElementById('generate-btn');
    btn.disabled = true;
    btn.textContent = '⏳ Generating...';

    // Gather priorities from existing selects (if any)
    const priorities = {};
    document.querySelectorAll('.alloc-priority-select').forEach(sel => {
        priorities[sel.dataset.cat] = sel.value;
    });

    fetch(`/events/${eventId}/smart-budget/generate`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({
            luxury_level: selectedLuxury,
            selected_services: selectedServices,
            priorities: priorities,
        })
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.textContent = '🔄 Regenerate Smart Budget';

        if (data.ok) {
            renderAllocations(data.allocations);
            renderWarnings(data.warnings || []);
            renderConfidence(data.confidence);

            document.getElementById('alloc-panel').style.display = '';
            document.getElementById('reco-container').style.display = '';
            document.getElementById('tracking-panel').style.display = '';

            // Load recommendations for each category
            data.allocations.forEach(a => {
                ensureRecoSection(a.category, a.allocated_amount);
                loadRecommendations(a.category, 'best_match');
            });

            loadTracking();
            loadSavings();
        }
    })
    .catch(() => { btn.disabled = false; btn.textContent = '⚡ Generate Smart Budget'; });
}

function renderAllocations(allocations) {
    const container = document.getElementById('alloc-list');
    const total = {{ $event->total_budget }};
    const services = @json(config('smart_budget.services'));

    container.innerHTML = allocations.map(a => {
        const pct = total > 0 ? Math.round(a.allocated_amount / total * 100) : 0;
        const barColor = pct > 35 ? '#f59e0b' : (pct > 20 ? '#3b82f6' : '#10b981');
        const svc = services[a.category] || { icon: '📋', label: a.category };
        const isLocked = a.is_locked ? 'locked' : '';

        return `<div class="alloc-row" data-category="${esc(a.category)}">
            <span style="font-size: 1.2rem;">${svc.icon}</span>
            <span class="alloc-cat-name">${esc(svc.label)}</span>
            <span class="alloc-amount">₹${Number(a.allocated_amount).toLocaleString('en-IN')}</span>
            <span class="alloc-percent">${pct}%</span>
            <div class="alloc-bar-wrap"><div class="alloc-bar-fill" style="width: ${pct}%; background: ${barColor};"></div></div>
            <select class="alloc-priority-select" data-cat="${esc(a.category)}" onchange="markDirty()">
                <option value="low" ${a.priority_level === 'low' ? 'selected' : ''}>Low</option>
                <option value="medium" ${a.priority_level === 'medium' ? 'selected' : ''}>Medium</option>
                <option value="high" ${a.priority_level === 'high' ? 'selected' : ''}>High</option>
            </select>
            <button class="alloc-lock-btn ${isLocked}" data-cat="${esc(a.category)}" onclick="toggleLock('${esc(a.category)}')">
                ${a.is_locked ? '🔒' : '🔓'}
            </button>
        </div>`;
    }).join('');
}

function renderWarnings(warnings) {
    const container = document.getElementById('warnings-container');
    if (!warnings.length) { container.innerHTML = ''; return; }
    container.innerHTML = warnings.map(w =>
        `<div class="sb-warning-item ${esc(w.severity)}">⚠️ ${esc(w.message)}</div>`
    ).join('');
}

function renderConfidence(conf) {
    if (!conf) return;
    // Update the hero stat if it exists
    const heroStats = document.querySelector('.sb-hero-stats');
    const existing = document.getElementById('conf-hero-stat');
    if (existing) existing.remove();

    const color = conf.overall >= 75 ? '#10b981' : (conf.overall >= 50 ? '#f59e0b' : '#ef4444');
    const html = `<div class="sb-hero-stat" id="conf-hero-stat" style="display: flex; align-items: center; gap: 14px;">
        <div class="confidence-ring" style="background: conic-gradient(${color} ${conf.overall * 3.6}deg, rgba(255,255,255,0.1) 0deg);">
            ${conf.overall}%
        </div>
        <div><small>Confidence</small><strong style="font-size: 0.9rem;">Budget Plan Score</strong></div>
    </div>`;
    heroStats.insertAdjacentHTML('beforeend', html);
}

function saveRebalance() {
    const priorities = {};
    const locked = {};

    document.querySelectorAll('.alloc-priority-select').forEach(sel => {
        priorities[sel.dataset.cat] = sel.value;
    });
    document.querySelectorAll('.alloc-lock-btn').forEach(btn => {
        locked[btn.dataset.cat] = btn.classList.contains('locked');
    });

    fetch(`/events/${eventId}/smart-budget/priorities`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ priorities, locked })
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            renderAllocations(data.allocations);
            renderWarnings(data.warnings || []);
            renderConfidence(data.confidence);
            // Reload recommendations
            data.allocations.forEach(a => {
                loadRecommendations(a.category, 'best_match');
            });
            loadSavings();
        }
    });
}

function ensureRecoSection(category, budget) {
    if (document.getElementById('reco-' + category)) return;
    const container = document.getElementById('reco-container');
    const services = @json(config('smart_budget.services'));
    const svc = services[category] || { icon: '📋', label: category };

    const html = `<div class="sb-reco-section" id="reco-${esc(category)}">
        <div class="sb-reco-cat-header">
            <span class="sb-reco-cat-title">${svc.icon} ${esc(svc.label)} <span style="font-weight: 400; color: #64748b; font-size: 0.85rem;">— Budget: ₹${Number(budget).toLocaleString('en-IN')}</span></span>
            <div class="sb-reco-filters">
                <button class="sb-reco-filter-btn active" onclick="loadRecommendations('${esc(category)}', 'best_match', this)">Best Match</button>
                <button class="sb-reco-filter-btn" onclick="loadRecommendations('${esc(category)}', 'cheaper', this)">Cheaper</button>
                <button class="sb-reco-filter-btn" onclick="loadRecommendations('${esc(category)}', 'premium', this)">Premium</button>
            </div>
        </div>
        <div class="sb-reco-grid" id="reco-grid-${esc(category)}">
            <div class="sb-loading"><div class="sb-spinner"></div><br>Finding vendors...</div>
        </div>
    </div>`;
    container.insertAdjacentHTML('beforeend', html);
}

function loadRecommendations(category, filter, clickedBtn) {
    const grid = document.getElementById('reco-grid-' + category);
    if (!grid) return;

    // Update filter button states
    if (clickedBtn) {
        const section = clickedBtn.closest('.sb-reco-section');
        section.querySelectorAll('.sb-reco-filter-btn').forEach(b => b.classList.remove('active'));
        clickedBtn.classList.add('active');
    }

    grid.innerHTML = '<div class="sb-loading"><div class="sb-spinner"></div><br>Finding vendors...</div>';

    fetch(`/events/${eventId}/smart-budget/recommendations?category=${encodeURIComponent(category)}&filter=${filter}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.vendors || data.vendors.length === 0) {
            grid.innerHTML = '<p style="color: #94a3b8; padding: 16px; text-align: center;">No vendors found for this category and filter.</p>';
            return;
        }

        grid.innerHTML = data.vendors.map(v => {
            const labelClass = v.label === 'Best Value' ? 'best-value' : (v.label.includes('Premium') ? 'premium' : 'budget-friendly');
            const unavailClass = !v.is_available ? 'sb-unavailable' : '';

            return `<div class="sb-vendor-card ${unavailClass}">
                <span class="sb-vendor-label ${labelClass}">${esc(v.label)}</span>
                <h4 class="sb-vendor-name">${esc(v.business_name)}</h4>
                <span class="sb-vendor-rating">★ ${Number(v.rating).toFixed(1)} <span style="color: #94a3b8; font-weight: 400;">(${v.total_reviews} reviews)</span></span>
                <div class="sb-vendor-price">₹${Number(v.price_min).toLocaleString('en-IN')}${v.price_max > v.price_min ? ' – ₹' + Number(v.price_max).toLocaleString('en-IN') : ''}+</div>
                <span class="sb-vendor-match">${v.match_score}% Match</span>
                ${!v.is_available ? '<span class="sb-unavailable-badge">May not be available</span>' : ''}
                <div class="sb-vendor-reasons">
                    ${v.reasons.map(r => `<div class="sb-vendor-reason">${esc(r)}</div>`).join('')}
                </div>
                <div class="sb-vendor-actions">
                    <a href="/vendors/${v.vendor_id}" class="sb-btn-view">View Details</a>
                    <a href="/events/${eventId}/bookings/create?vendor=${v.vendor_id}" class="sb-btn-book">Book Vendor</a>
                </div>
            </div>`;
        }).join('');
    })
    .catch(() => {
        grid.innerHTML = '<p style="color: #ef4444; padding: 16px; text-align: center;">Failed to load recommendations.</p>';
    });
}

function loadTracking() {
    const container = document.getElementById('tracking-content');
    fetch(`/events/${eventId}/smart-budget/tracking`, { headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(data => {
        const services = @json(config('smart_budget.services'));
        let html = '';

        if (data.alerts && data.alerts.length > 0) {
            html += data.alerts.map(a =>
                `<div class="sb-warning-item critical">🚨 ${esc(a.message)}</div>`
            ).join('');
        }

        html += '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 12px; margin-top: 12px;">';
        data.tracking.forEach(t => {
            const svc = services[t.category] || { icon: '📋', label: t.category };
            const barColor = t.percent_used > 90 ? '#ef4444' : (t.percent_used > 70 ? '#f59e0b' : '#10b981');
            html += `<div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <span style="font-weight: 700; font-size: 0.88rem;">${svc.icon} ${esc(svc.label)}</span>
                    <span style="font-size: 0.78rem; color: #64748b;">${t.percent_used}% used</span>
                </div>
                <div style="height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden; margin-bottom: 6px;">
                    <div style="width: ${t.percent_used}%; height: 100%; background: ${barColor}; transition: width 0.4s;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.78rem; color: #475569;">
                    <span>Used: ₹${Number(t.used_amount).toLocaleString('en-IN')}</span>
                    <span>Remaining: ₹${Number(t.remaining).toLocaleString('en-IN')}</span>
                </div>
                ${t.is_locked ? '<span style="font-size: 0.7rem; color: #f59e0b; font-weight: 700;">🔒 Locked</span>' : ''}
            </div>`;
        });
        html += '</div>';

        html += `<div style="margin-top: 16px; display: flex; gap: 16px; flex-wrap: wrap;">
            <div style="background: #f0fdf4; border: 1px solid #a7f3d0; border-radius: 10px; padding: 12px 20px;">
                <small style="color: #065f46; font-weight: 700;">Total Budget</small>
                <strong style="display: block; color: #047857; font-size: 1.1rem;">₹${Number(data.total_budget).toLocaleString('en-IN')}</strong>
            </div>
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 12px 20px;">
                <small style="color: #1e40af; font-weight: 700;">Allocated</small>
                <strong style="display: block; color: #2563eb; font-size: 1.1rem;">₹${Number(data.total_allocated).toLocaleString('en-IN')}</strong>
            </div>
            <div style="background: #fef3c7; border: 1px solid #fde68a; border-radius: 10px; padding: 12px 20px;">
                <small style="color: #92400e; font-weight: 700;">Used</small>
                <strong style="display: block; color: #d97706; font-size: 1.1rem;">₹${Number(data.total_used).toLocaleString('en-IN')}</strong>
            </div>
        </div>`;

        container.innerHTML = html;
    })
    .catch(() => { container.innerHTML = '<p style="color: #ef4444;">Failed to load tracking data.</p>'; });
}

function loadSavings() {
    fetch(`/events/${eventId}/smart-budget/savings`, { headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(data => {
        let panel = document.getElementById('savings-panel');
        if (data.total_savings <= 0) {
            if (panel) panel.style.display = 'none';
            return;
        }
        if (!panel) {
            const ref = document.getElementById('reco-container');
            const html = `<div class="sb-savings-panel" id="savings-panel"></div>`;
            ref.insertAdjacentHTML('beforebegin', html);
            panel = document.getElementById('savings-panel');
        }
        panel.style.display = '';
        panel.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div>
                    <h3 style="margin: 0; color: #065f46;">💡 Potential Savings</h3>
                    <span class="sb-savings-total">₹${Number(data.total_savings).toLocaleString('en-IN')}</span>
                </div>
            </div>
            ${data.suggestions.map(s => `<div class="sb-saving-item">💰 ${esc(s.message)}</div>`).join('')}
        `;
    });
}

function esc(s) {
    if (!s) return '';
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
}

// On page load: if allocations exist, load recommendations + tracking
document.addEventListener('DOMContentLoaded', function() {
    @if($hasAllocations)
        @foreach($allocations as $alloc)
            loadRecommendations('{{ $alloc->category }}', 'best_match');
        @endforeach
        loadTracking();
    @endif
});
</script>
@endsection

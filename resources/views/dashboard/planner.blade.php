@extends('layouts.app', ['title' => 'Dashboard - Eventra'])
@section('page-title','Dashboard')

@section('content')
<section class="plain-section">
    <h2>Hello, {{ $user->name }}</h2>
    <p class="plain-muted">Plain dashboard for backend-first development.</p>
</section>

<section class="mobile-safe-grid plain-section">
    @foreach([
        ['Total Events', $stats['events'], 'Workspace event records'],
        ['Total Guests', $stats['guests'], 'Guest records across events'],
        ['Total Spent', 'Rs. '.number_format($stats['spent']), 'Recorded expenses'],
        ['Pending Tasks', $stats['tasks'], 'Tasks still open'],
    ] as [$label, $value, $copy])
        <article class="stat-card plain-stat">
            <p>{{ $label }}</p>
            <strong>{{ $value }}</strong>
            <small class="plain-muted">{{ $copy }}</small>
        </article>
    @endforeach
</section>

<section class="plain-section grid-list">
    <article class="panel">
        <h3>Upcoming Events</h3>
        @forelse($upcoming as $event)
            <p>
                <a href="{{ auth()->user()->role === 'planner' ? route('events.show', $event) : '#' }}">
                    {{ $event->event_name }}
                </a>
                <br>
                <span class="plain-muted">
                    {{ optional($event->event_date)->format('M d, Y') }} -
                    {{ $event->location }} -
                    {{ $event->guest_count_expected }} guests
                </span>
            </p>
        @empty
            <p>No events yet.</p>
        @endforelse
    </article>

    <article class="panel">
        <h3>Quick Actions</h3>
        <div class="plain-actions">
            @if(auth()->user()->role === 'planner')
                <a class="btn-primary" href="{{ route('events.create') }}">New Event</a>
            @endif
            <a class="btn-ghost" href="{{ route('vendors.index') }}">Vendors</a>
            <a class="btn-ghost" href="{{ route('profile.edit') }}">Profile</a>
        </div>
    </article>
</section>

<!-- ── Find Vendors Section ── -->
<section class="plain-section" id="find-vendors">
    <div class="panel" style="padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 16px;">
            <div>
                <h3 style="margin: 0;">Find Vendors</h3>
                <p class="plain-muted" style="margin: 4px 0 0;">Discover registered vendors by category or search by name.</p>
            </div>
            <a href="{{ route('vendors.index') }}" style="font-weight: 600; color: #2563eb; text-decoration: none; font-size: 0.95rem;">View Full Directory →</a>
        </div>

        <!-- Filters -->
        <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px;">
            <select id="vendor-category-filter" onchange="filterVendors()" style="padding: 10px 14px; border: 1px solid #ccc; border-radius: 8px; min-width: 180px; font-size: 0.95rem;">
                <option value="">All Categories</option>
                @foreach($vendorCategories as $cat)
                    <option value="{{ $cat }}">{{ str($cat)->headline() }}</option>
                @endforeach
            </select>
            <input id="vendor-search-input" type="text" placeholder="Search by vendor name..." oninput="debounceSearch()" style="flex: 1; min-width: 200px; padding: 10px 14px; border: 1px solid #ccc; border-radius: 8px; font-size: 0.95rem;">
        </div>

        <!-- Vendor Results Grid -->
        <div id="vendor-results" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
            <p class="plain-muted" style="text-align: center; grid-column: 1 / -1; padding: 24px;">Select a category or start typing to search vendors.</p>
        </div>
    </div>
</section>

<script>
let vendorSearchTimer = null;

function debounceSearch() {
    clearTimeout(vendorSearchTimer);
    vendorSearchTimer = setTimeout(() => filterVendors(), 350);
}

function filterVendors() {
    const category = document.getElementById('vendor-category-filter').value;
    const q = document.getElementById('vendor-search-input').value.trim();
    const container = document.getElementById('vendor-results');

    if (!category && !q) {
        container.innerHTML = '<p class="plain-muted" style="text-align: center; grid-column: 1 / -1; padding: 24px;">Select a category or start typing to search vendors.</p>';
        return;
    }

    container.innerHTML = '<p class="plain-muted" style="text-align: center; grid-column: 1 / -1; padding: 24px;">Loading vendors...</p>';

    const params = new URLSearchParams();
    if (category) params.set('category', category);
    if (q) params.set('q', q);

    fetch('/api/vendors/filter?' + params.toString(), {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(vendors => {
        if (!vendors.length) {
            container.innerHTML = '<p class="plain-muted" style="text-align: center; grid-column: 1 / -1; padding: 24px;">No vendors found matching your criteria.</p>';
            return;
        }
        container.innerHTML = vendors.map(v => {
            const cat = v.category ? v.category.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) : 'Vendor';
            const rating = v.rating ? Number(v.rating).toFixed(1) : '0.0';
            const priceMin = v.price_min ? '₹' + Number(v.price_min).toLocaleString('en-IN') : '₹0';
            const loc = v.location || v.base_location || '';
            const desc = v.description ? (v.description.length > 80 ? v.description.substring(0, 80) + '...' : v.description) : '';
            const vendorId = v._id || v.id;

            return `<div style="border: 1px solid #d8d8d8; border-radius: 12px; padding: 16px; background: #fafafa; transition: box-shadow 0.2s;" onmouseenter="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.08)'" onmouseleave="this.style.boxShadow='none'">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="background: rgba(37,99,235,0.1); color: #2563eb; padding: 3px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">${escVendorHtml(cat)}</span>
                    <span style="color: #d97706; font-weight: 700; font-size: 0.9rem;">★ ${rating}</span>
                </div>
                <h4 style="margin: 0 0 4px; font-size: 1.1rem; font-weight: 700;">${escVendorHtml(v.business_name || v.name || 'Vendor')}</h4>
                <p style="margin: 0 0 8px; color: #888; font-size: 0.9rem; line-height: 1.4;">${escVendorHtml(desc)}</p>
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; color: #666;">
                    <span>📍 ${escVendorHtml(loc)}</span>
                    <strong>${priceMin}+</strong>
                </div>
                <div style="display: flex; gap: 8px; margin-top: 12px;">
                    <a href="/vendors/${vendorId}" style="flex: 1; text-align: center; background: #2563eb; color: #fff; padding: 8px 0; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 0.9rem;">View Details</a>
                    <form method="POST" action="/vendors/${vendorId}/favorite" style="margin: 0;">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]')?.content || ''}">
                        <button type="submit" style="background: #f5f5f5; border: 1px solid #ddd; border-radius: 6px; padding: 8px 12px; cursor: pointer; font-size: 0.9rem;" title="Add to favorites">❤️</button>
                    </form>
                </div>
            </div>`;
        }).join('');
    })
    .catch(() => {
        container.innerHTML = '<p style="text-align: center; grid-column: 1 / -1; padding: 24px; color: #dc2626;">Error loading vendors. Please try again.</p>';
    });
}

function escVendorHtml(s) {
    if (!s) return '';
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
}

// Auto-load all vendors on page load
document.addEventListener('DOMContentLoaded', function() {
    filterVendors();
});
</script>
@endsection

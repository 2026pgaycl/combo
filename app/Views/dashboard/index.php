<h1>Welcome back, <?= htmlspecialchars($user['name']) ?></h1>
<p class="text-muted">Here's what's happening across your portfolio.</p>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-value"><?= (int) $stats['buildings'] ?></div>
        <div class="stat-label">Buildings</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $occupancyRate ?>%</div>
        <div class="stat-label">Occupancy rate</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= (int) $stats['active_leases'] ?></div>
        <div class="stat-label">Active leases</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= (int) $stats['open_tickets'] ?></div>
        <div class="stat-label">Open maintenance tickets</div>
    </div>
    <div class="stat-card stat-card-alert">
        <div class="stat-value"><?= (int) $stats['leases_expiring_soon'] ?></div>
        <div class="stat-label">Leases expiring in 90 days</div>
    </div>
</div>

<div class="quick-links">
    <a href="/buildings/create" class="btn btn-primary">+ Add Building</a>
    <a href="/tenants/create" class="btn btn-secondary">+ Add Tenant</a>
    <a href="/leases/create" class="btn btn-secondary">+ Create Lease</a>
</div>

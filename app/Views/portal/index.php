<h1>Welcome, <?= htmlspecialchars($tenantName) ?></h1>
<p class="text-muted">Here's a summary of your account.</p>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-value"><?= count($leases) ?></div>
        <div class="stat-label">Leases</div>
    </div>
    <div class="stat-card <?= count($openInvoices) ? 'stat-card-alert' : '' ?>">
        <div class="stat-value"><?= count($openInvoices) ?></div>
        <div class="stat-label">Unpaid invoices</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= count($openTickets) ?></div>
        <div class="stat-label">Open maintenance tickets</div>
    </div>
</div>

<h2>Your Leases</h2>
<table class="data-table">
    <thead><tr><th>Unit</th><th>Start</th><th>End</th><th>Rent</th><th>Status</th></tr></thead>
    <tbody>
        <?php if (empty($leases)): ?>
        <tr><td colspan="5" class="text-muted">No leases on file.</td></tr>
        <?php endif; ?>
        <?php foreach ($leases as $l): ?>
        <tr>
            <td><?= htmlspecialchars($l['building_name'] . ' — ' . $l['unit_number']) ?></td>
            <td><?= htmlspecialchars($l['start_date']) ?></td>
            <td><?= htmlspecialchars($l['end_date']) ?></td>
            <td>RM <?= number_format($l['monthly_rent'], 2) ?></td>
            <td><span class="badge badge-<?= htmlspecialchars($l['status']) ?>"><?= htmlspecialchars($l['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="quick-links">
    <a href="/portal/invoices" class="btn btn-secondary">View Invoices</a>
    <a href="/portal/maintenance/create" class="btn btn-primary">+ Report Issue</a>
</div>

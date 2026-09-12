<div class="page-header">
    <h1>Maintenance Tickets</h1>
    <a href="/maintenance/create" class="btn btn-primary">+ Report Issue</a>
</div>

<?php \App\Core\View::partial('partials.csv_toolbar', [
    'exportUrl' => '/maintenance/export',
    'importUrl' => '/maintenance/import',
]); ?>

<form method="GET" action="/maintenance" class="filter-bar">
    <label>Status
        <select name="status" onchange="this.form.submit()">
            <option value="">All statuses</option>
            <?php foreach (['open', 'in_progress', 'resolved', 'closed'] as $s): ?>
            <option value="<?= $s ?>" <?= $selectedStatus === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $s)) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
</form>

<table class="data-table">
    <thead>
        <tr><th>#</th><th>Category</th><th>Unit</th><th>Tenant</th><th>Assigned</th><th>Status</th><th>Reported</th></tr>
    </thead>
    <tbody>
        <?php if (empty($tickets)): ?>
        <tr><td colspan="7" class="text-muted">No tickets yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($tickets as $t): ?>
        <tr class="<?= $t['status'] === 'open' ? 'row-warning' : '' ?>">
            <td><a href="/maintenance/<?= $t['id'] ?>">#<?= $t['id'] ?></a></td>
            <td><?= htmlspecialchars(ucfirst($t['category'])) ?></td>
            <td><?= htmlspecialchars($t['building_name'] . ' — ' . $t['unit_number']) ?></td>
            <td><?= htmlspecialchars($t['tenant_name'] ?? '—') ?></td>
            <td><?= htmlspecialchars($t['assignee_name'] ?? 'Unassigned') ?></td>
            <td><span class="badge badge-<?= htmlspecialchars($t['status']) ?>"><?= htmlspecialchars(str_replace('_', ' ', $t['status'])) ?></span></td>
            <td><?= htmlspecialchars($t['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

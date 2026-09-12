<div class="page-header">
    <h1>My Maintenance Tickets</h1>
    <a href="/portal/maintenance/create" class="btn btn-primary">+ Report Issue</a>
</div>

<table class="data-table">
    <thead>
        <tr><th>#</th><th>Category</th><th>Unit</th><th>Assigned</th><th>Status</th><th>Reported</th></tr>
    </thead>
    <tbody>
        <?php if (empty($tickets)): ?>
        <tr><td colspan="6" class="text-muted">No tickets yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($tickets as $t): ?>
        <tr class="<?= $t['status'] === 'open' ? 'row-warning' : '' ?>">
            <td><a href="/portal/maintenance/<?= $t['id'] ?>">#<?= $t['id'] ?></a></td>
            <td><?= htmlspecialchars(ucfirst($t['category'])) ?></td>
            <td><?= htmlspecialchars($t['building_name'] . ' — ' . $t['unit_number']) ?></td>
            <td><?= htmlspecialchars($t['assignee_name'] ?? 'Unassigned') ?></td>
            <td><span class="badge badge-<?= htmlspecialchars($t['status']) ?>"><?= htmlspecialchars(str_replace('_', ' ', $t['status'])) ?></span></td>
            <td><?= htmlspecialchars($t['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

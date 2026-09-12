<div class="page-header">
    <h1>Leases</h1>
    <a href="/leases/create" class="btn btn-primary">+ Create Lease</a>
</div>

<table class="data-table">
    <thead>
        <tr><th>Tenant</th><th>Unit</th><th>Start</th><th>End</th><th>Rent</th><th>Status</th></tr>
    </thead>
    <tbody>
        <?php if (empty($leases)): ?>
        <tr><td colspan="6" class="text-muted">No leases yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($leases as $l): ?>
        <tr class="<?= ($l['status'] === 'active' && $l['days_to_expiry'] <= 90) ? 'row-warning' : '' ?>">
            <td><a href="/leases/<?= $l['id'] ?>"><?= htmlspecialchars($l['company_name']) ?></a></td>
            <td><?= htmlspecialchars($l['building_name'] . ' — ' . $l['unit_number']) ?></td>
            <td><?= htmlspecialchars($l['start_date']) ?></td>
            <td><?= htmlspecialchars($l['end_date']) ?>
                <?php if ($l['status'] === 'active' && $l['days_to_expiry'] <= 90): ?>
                    <span class="tag-alert"><?= (int) $l['days_to_expiry'] ?>d left</span>
                <?php endif; ?>
            </td>
            <td>RM <?= number_format($l['monthly_rent'], 2) ?></td>
            <td><span class="badge badge-<?= htmlspecialchars($l['status']) ?>"><?= htmlspecialchars($l['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

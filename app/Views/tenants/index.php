<div class="page-header">
    <h1>Tenants</h1>
    <a href="/tenants/create" class="btn btn-primary">+ Add Tenant</a>
</div>

<table class="data-table">
    <thead>
        <tr><th>Company</th><th>Contact</th><th>Unit</th><th>Lease Ends</th><th></th></tr>
    </thead>
    <tbody>
        <?php if (empty($tenants)): ?>
        <tr><td colspan="5" class="text-muted">No tenants yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($tenants as $t): ?>
        <tr>
            <td><a href="/tenants/<?= $t['id'] ?>"><?= htmlspecialchars($t['company_name']) ?></a></td>
            <td><?= htmlspecialchars($t['contact_name']) ?></td>
            <td><?= $t['unit_number'] ? htmlspecialchars($t['building_name'] . ' — ' . $t['unit_number']) : '—' ?></td>
            <td><?= htmlspecialchars($t['end_date'] ?? '—') ?></td>
            <td><a href="/tenants/<?= $t['id'] ?>/edit">Edit</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

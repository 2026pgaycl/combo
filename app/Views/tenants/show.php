<div class="page-header">
    <h1><?= htmlspecialchars($tenant['company_name']) ?></h1>
    <a href="/tenants/<?= $tenant['id'] ?>/edit" class="btn btn-secondary">Edit</a>
</div>

<dl class="detail-list">
    <dt>Contact</dt><dd><?= htmlspecialchars($tenant['contact_name']) ?></dd>
    <dt>Email</dt><dd><?= htmlspecialchars($tenant['contact_email'] ?? '—') ?></dd>
    <dt>Phone</dt><dd><?= htmlspecialchars($tenant['contact_phone'] ?? '—') ?></dd>
    <dt>Reg. No.</dt><dd><?= htmlspecialchars($tenant['reg_no'] ?? '—') ?></dd>
</dl>

<h2>Leases</h2>
<table class="data-table">
    <thead><tr><th>Unit</th><th>Start</th><th>End</th><th>Rent</th><th>Status</th></tr></thead>
    <tbody>
        <?php if (empty($leases)): ?>
        <tr><td colspan="5" class="text-muted">No leases on file yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($leases as $l): ?>
        <tr>
            <td><a href="/leases/<?= $l['id'] ?>"><?= htmlspecialchars($l['building_name'] . ' — ' . $l['unit_number']) ?></a></td>
            <td><?= htmlspecialchars($l['start_date']) ?></td>
            <td><?= htmlspecialchars($l['end_date']) ?></td>
            <td>RM <?= number_format($l['monthly_rent'], 2) ?></td>
            <td><span class="badge badge-<?= htmlspecialchars($l['status']) ?>"><?= htmlspecialchars($l['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php \App\Core\View::partial('partials.documents', [
    'documents' => $documents,
    'relatedType' => 'tenant',
    'relatedId' => $tenant['id'],
]); ?>

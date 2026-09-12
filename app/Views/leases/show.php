<div class="page-header">
    <h1>Lease — <?= htmlspecialchars($lease['company_name']) ?></h1>
    <div class="header-actions">
        <?php if ($lease['status'] === 'active'): ?>
        <a href="/invoices/create?lease_id=<?= $lease['id'] ?>" class="btn btn-secondary">+ Create Invoice</a>
        <?php endif; ?>
        <span class="badge badge-<?= htmlspecialchars($lease['status']) ?>"><?= htmlspecialchars($lease['status']) ?></span>
    </div>
</div>

<dl class="detail-list">
    <dt>Unit</dt><dd><?= htmlspecialchars($lease['building_name'] . ' — ' . $lease['unit_number']) ?></dd>
    <dt>Tenant contact</dt><dd><?= htmlspecialchars($lease['contact_name']) ?> (<?= htmlspecialchars($lease['contact_email'] ?? '—') ?>)</dd>
    <dt>Start date</dt><dd><?= htmlspecialchars($lease['start_date']) ?></dd>
    <dt>End date</dt><dd><?= htmlspecialchars($lease['end_date']) ?></dd>
    <dt>Monthly rent</dt><dd>RM <?= number_format($lease['monthly_rent'], 2) ?></dd>
    <dt>Deposit</dt><dd>RM <?= number_format($lease['deposit'], 2) ?></dd>
    <?php if (!empty($lease['escalation_clause'])): ?>
    <dt>Escalation clause</dt><dd><?= nl2br(htmlspecialchars($lease['escalation_clause'])) ?></dd>
    <?php endif; ?>
</dl>

<?php if ($lease['status'] === 'active'): ?>
<form method="POST" action="/leases/<?= $lease['id'] ?>/terminate" class="danger-zone" onsubmit="return confirm('Terminate this lease and mark the unit vacant?');">
    <?= \App\Core\Csrf::field() ?>
    <button type="submit" class="btn btn-danger">Terminate Lease</button>
</form>
<?php endif; ?>

<h2>Lease Documents</h2>
<table class="data-table">
    <thead><tr><th>Type</th><th>Expiry</th><th>Uploaded</th><th></th></tr></thead>
    <tbody>
        <?php if (empty($leaseDocuments)): ?>
        <tr><td colspan="4" class="text-muted">No lease documents yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($leaseDocuments as $d): ?>
        <tr class="<?= (!empty($d['expiry_date']) && $d['expiry_date'] < date('Y-m-d')) ? 'row-warning' : '' ?>">
            <td><a href="/<?= htmlspecialchars($d['file_path']) ?>" target="_blank"><?= htmlspecialchars($d['doc_type'] === 'kyc' ? 'KYC' : ucfirst($d['doc_type'])) ?></a></td>
            <td><?= htmlspecialchars($d['expiry_date'] ?? '—') ?></td>
            <td><?= htmlspecialchars($d['uploaded_at']) ?></td>
            <td>
                <form method="POST" action="/leases/documents/<?= $d['id'] ?>" onsubmit="return confirm('Delete this document?');">
                    <?= \App\Core\Csrf::field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="text-link-danger">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<form method="POST" action="/leases/<?= $lease['id'] ?>/documents" enctype="multipart/form-data" class="form-card form-card-inline">
    <?= \App\Core\Csrf::field() ?>
    <label>Type
        <select name="doc_type">
            <?php foreach ($docTypes as $type): ?>
            <option value="<?= $type ?>"><?= $type === 'kyc' ? 'KYC' : ucfirst($type) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Expiry date (optional)
        <input type="date" name="expiry_date">
    </label>
    <label>File
        <input type="file" name="document" required>
    </label>
    <button type="submit" class="btn btn-primary">Upload</button>
</form>

<?php \App\Core\View::partial('partials.documents', [
    'documents' => $documents,
    'relatedType' => 'lease',
    'relatedId' => $lease['id'],
]); ?>

<div class="page-header">
    <h1>Invoices</h1>
    <a href="/invoices/create" class="btn btn-primary">+ Create Invoice</a>
</div>

<?php \App\Core\View::partial('partials.csv_toolbar', [
    'exportUrl' => '/invoices/export',
    'importUrl' => '/invoices/import',
]); ?>

<table class="data-table">
    <thead>
        <tr><th>Invoice #</th><th>Tenant</th><th>Unit</th><th>Due</th><th>Total</th><th>Paid</th><th>Status</th></tr>
    </thead>
    <tbody>
        <?php if (empty($invoices)): ?>
        <tr><td colspan="7" class="text-muted">No invoices yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($invoices as $inv): ?>
        <tr class="<?= ($inv['status'] !== 'paid' && $inv['due_date'] < date('Y-m-d')) ? 'row-warning' : '' ?>">
            <td><a href="/invoices/<?= $inv['id'] ?>"><?= htmlspecialchars($inv['invoice_no']) ?></a></td>
            <td><?= htmlspecialchars($inv['company_name']) ?></td>
            <td><?= htmlspecialchars($inv['building_name'] . ' — ' . $inv['unit_number']) ?></td>
            <td><?= htmlspecialchars($inv['due_date']) ?></td>
            <td>RM <?= number_format($inv['total'], 2) ?></td>
            <td>RM <?= number_format($inv['amount_paid'], 2) ?></td>
            <td><span class="badge badge-<?= htmlspecialchars($inv['status']) ?>"><?= htmlspecialchars(str_replace('_', ' ', $inv['status'])) ?></span></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

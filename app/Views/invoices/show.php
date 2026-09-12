<div class="page-header">
    <h1>Invoice <?= htmlspecialchars($invoice['invoice_no']) ?></h1>
    <span class="badge badge-<?= htmlspecialchars($invoice['status']) ?>"><?= htmlspecialchars(str_replace('_', ' ', $invoice['status'])) ?></span>
</div>

<dl class="detail-list">
    <dt>Tenant</dt><dd><?= htmlspecialchars($invoice['company_name']) ?></dd>
    <dt>Unit</dt><dd><?= htmlspecialchars($invoice['building_name'] . ' — ' . $invoice['unit_number']) ?></dd>
    <dt>Period</dt><dd><?= htmlspecialchars($invoice['period_start']) ?> to <?= htmlspecialchars($invoice['period_end']) ?></dd>
    <dt>Due date</dt><dd><?= htmlspecialchars($invoice['due_date']) ?></dd>
    <dt>Subtotal</dt><dd>RM <?= number_format($invoice['subtotal'], 2) ?></dd>
    <dt>Tax</dt><dd>RM <?= number_format($invoice['tax'], 2) ?></dd>
    <dt>Total</dt><dd>RM <?= number_format($invoice['total'], 2) ?></dd>
    <dt>Paid so far</dt><dd>RM <?= number_format($amountPaid, 2) ?></dd>
    <dt>Balance</dt><dd>RM <?= number_format($invoice['total'] - $amountPaid, 2) ?></dd>
</dl>

<h2>Line items</h2>
<table class="data-table">
    <thead><tr><th>Description</th><th>Amount</th></tr></thead>
    <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['description']) ?></td>
            <td>RM <?= number_format($item['amount'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Payments</h2>
<table class="data-table">
    <thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Reference</th></tr></thead>
    <tbody>
        <?php if (empty($payments)): ?>
        <tr><td colspan="4" class="text-muted">No payments recorded yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($payments as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['paid_at']) ?></td>
            <td>RM <?= number_format($p['amount'], 2) ?></td>
            <td><?= htmlspecialchars(str_replace('_', ' ', $p['method'])) ?></td>
            <td><?= htmlspecialchars($p['reference_no'] ?? '—') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if ($invoice['status'] !== 'paid'): ?>
<form method="POST" action="/invoices/<?= $invoice['id'] ?>/payments" class="form-card form-card-inline">
    <?= \App\Core\Csrf::field() ?>
    <label>Amount (RM)
        <input type="number" step="0.01" name="amount" required>
    </label>
    <label>Method
        <select name="method">
            <option value="bank_transfer">Bank transfer</option>
            <option value="cheque">Cheque</option>
            <option value="online_gateway">Online gateway</option>
            <option value="cash">Cash</option>
        </select>
    </label>
    <label>Reference no.
        <input type="text" name="reference_no">
    </label>
    <button type="submit" class="btn btn-primary">Record Payment</button>
</form>
<?php endif; ?>

<?php if (empty($payments)): ?>
<form method="POST" action="/invoices/<?= $invoice['id'] ?>" class="danger-zone" onsubmit="return confirm('Delete this invoice? This cannot be undone.');">
    <?= \App\Core\Csrf::field() ?>
    <input type="hidden" name="_method" value="DELETE">
    <button type="submit" class="btn btn-danger">Delete Invoice</button>
</form>
<?php endif; ?>

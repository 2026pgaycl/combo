<h1>Create Invoice</h1>

<form method="POST" action="/invoices" class="form-card">
    <?= \App\Core\Csrf::field() ?>

    <label>Lease
        <select name="lease_id" id="lease_id" required>
            <option value="">Select a lease</option>
            <?php foreach ($leases as $l): ?>
            <option value="<?= $l['id'] ?>" data-rent="<?= $l['monthly_rent'] ?>" <?= (int) $l['id'] === (int) $selectedLeaseId ? 'selected' : '' ?>>
                <?= htmlspecialchars($l['company_name'] . ' — ' . $l['building_name'] . ' ' . $l['unit_number']) ?>
                (RM <?= number_format($l['monthly_rent'], 2) ?>/mo)
            </option>
            <?php endforeach; ?>
        </select>
    </label>

    <div class="form-row">
        <label>Period start
            <input type="date" name="period_start" required>
        </label>
        <label>Period end
            <input type="date" name="period_end" required>
        </label>
        <label>Due date
            <input type="date" name="due_date" required>
        </label>
    </div>

    <h2>Line items</h2>
    <div class="form-row">
        <label>Description
            <input type="text" name="item_description[]" value="Monthly rent">
        </label>
        <label>Amount (RM)
            <input type="number" step="0.01" name="item_amount[]" id="rent_amount">
        </label>
    </div>
    <div class="form-row">
        <label>Description
            <input type="text" name="item_description[]" placeholder="e.g. Service charge">
        </label>
        <label>Amount (RM)
            <input type="number" step="0.01" name="item_amount[]">
        </label>
    </div>
    <div class="form-row">
        <label>Description
            <input type="text" name="item_description[]" placeholder="e.g. Utilities">
        </label>
        <label>Amount (RM)
            <input type="number" step="0.01" name="item_amount[]">
        </label>
    </div>

    <label>Tax (RM)
        <input type="number" step="0.01" name="tax" value="0">
    </label>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Create Invoice</button>
        <a href="/invoices" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<script>
document.getElementById('lease_id').addEventListener('change', function () {
    const rent = this.options[this.selectedIndex].dataset.rent;
    const rentInput = document.getElementById('rent_amount');
    if (rent && rentInput && !rentInput.value) {
        rentInput.value = rent;
    }
});
</script>

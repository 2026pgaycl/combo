<h1>Create Lease</h1>

<form method="POST" action="/leases" class="form-card">
    <?= \App\Core\Csrf::field() ?>

    <label>Vacant unit
        <select name="unit_id" required>
            <option value="">Select a unit</option>
            <?php foreach ($vacantUnits as $u): ?>
            <option value="<?= $u['id'] ?>">
                <?= htmlspecialchars($u['building_name'] . ' — ' . $u['unit_number']) ?>
                (RM <?= number_format($u['base_rent'], 2) ?>)
            </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Tenant
        <select name="tenant_id" required>
            <option value="">Select a tenant</option>
            <?php foreach ($tenants as $t): ?>
            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['company_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <div class="form-row">
        <label>Start date
            <input type="date" name="start_date" required>
        </label>
        <label>End date
            <input type="date" name="end_date" required>
        </label>
    </div>

    <div class="form-row">
        <label>Monthly rent (RM)
            <input type="number" step="0.01" name="monthly_rent" required>
        </label>
        <label>Deposit (RM)
            <input type="number" step="0.01" name="deposit" value="0">
        </label>
    </div>

    <label>Escalation clause (optional)
        <textarea name="escalation_clause" rows="3" placeholder="e.g. 5% increase every 2 years"></textarea>
    </label>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Create Lease</button>
        <a href="/leases" class="btn btn-secondary">Cancel</a>
    </div>
</form>

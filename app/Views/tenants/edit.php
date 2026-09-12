<h1>Edit Tenant</h1>

<form method="POST" action="/tenants/<?= $tenant['id'] ?>" class="form-card">
    <?= \App\Core\Csrf::field() ?>
    <input type="hidden" name="_method" value="PUT">
    <label>Company name
        <input type="text" name="company_name" value="<?= htmlspecialchars($tenant['company_name']) ?>" required>
    </label>
    <label>Business registration no. (SSM)
        <input type="text" name="reg_no" value="<?= htmlspecialchars($tenant['reg_no'] ?? '') ?>">
    </label>
    <label>Contact person
        <input type="text" name="contact_name" value="<?= htmlspecialchars($tenant['contact_name']) ?>" required>
    </label>
    <div class="form-row">
        <label>Contact email
            <input type="email" name="contact_email" value="<?= htmlspecialchars($tenant['contact_email'] ?? '') ?>">
        </label>
        <label>Contact phone
            <input type="text" name="contact_phone" value="<?= htmlspecialchars($tenant['contact_phone'] ?? '') ?>">
        </label>
    </div>
    <label>Billing address
        <textarea name="billing_address" rows="3"><?= htmlspecialchars($tenant['billing_address'] ?? '') ?></textarea>
    </label>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Update Tenant</button>
        <a href="/tenants/<?= $tenant['id'] ?>" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<form method="POST" action="/tenants/<?= $tenant['id'] ?>" class="danger-zone" onsubmit="return confirm('Delete this tenant? This cannot be undone.');">
    <?= \App\Core\Csrf::field() ?>
    <input type="hidden" name="_method" value="DELETE">
    <button type="submit" class="btn btn-danger">Delete Tenant</button>
</form>

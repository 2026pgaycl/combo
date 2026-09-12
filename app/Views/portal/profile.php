<h1>My Profile</h1>

<form method="POST" action="/portal/profile" class="form-card">
    <?= \App\Core\Csrf::field() ?>
    <input type="hidden" name="_method" value="PUT">

    <label>Company name
        <input type="text" value="<?= htmlspecialchars($tenant['company_name']) ?>" disabled>
    </label>
    <label>Contact name
        <input type="text" name="contact_name" value="<?= htmlspecialchars($tenant['contact_name']) ?>" required>
    </label>
    <label>Contact phone
        <input type="text" name="contact_phone" value="<?= htmlspecialchars($tenant['contact_phone'] ?? '') ?>">
    </label>
    <label>Billing address
        <input type="text" name="billing_address" value="<?= htmlspecialchars($tenant['billing_address'] ?? '') ?>">
    </label>
    <label>Contact email (contact property staff to change)
        <input type="email" value="<?= htmlspecialchars($tenant['contact_email'] ?? '') ?>" disabled>
    </label>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
</form>

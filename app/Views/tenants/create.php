<h1>Add Tenant</h1>

<form method="POST" action="/tenants" class="form-card">
    <?= \App\Core\Csrf::field() ?>
    <label>Company name
        <input type="text" name="company_name" required>
    </label>
    <label>Business registration no. (SSM)
        <input type="text" name="reg_no">
    </label>
    <label>Contact person
        <input type="text" name="contact_name" required>
    </label>
    <div class="form-row">
        <label>Contact email
            <input type="email" name="contact_email">
        </label>
        <label>Contact phone
            <input type="text" name="contact_phone">
        </label>
    </div>
    <label>Billing address
        <textarea name="billing_address" rows="3"></textarea>
    </label>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save Tenant</button>
        <a href="/tenants" class="btn btn-secondary">Cancel</a>
    </div>
</form>

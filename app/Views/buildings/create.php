<h1>Add Building</h1>

<form method="POST" action="/buildings" class="form-card">
    <?= \App\Core\Csrf::field() ?>
    <label>Building name
        <input type="text" name="name" required>
    </label>
    <label>Address line 1
        <input type="text" name="address_line1">
    </label>
    <label>Address line 2
        <input type="text" name="address_line2">
    </label>
    <div class="form-row">
        <label>City
            <input type="text" name="city">
        </label>
        <label>State
            <input type="text" name="state">
        </label>
        <label>Postcode
            <input type="text" name="postcode">
        </label>
    </div>
    <label>Country
        <input type="text" name="country" value="Malaysia">
    </label>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save Building</button>
        <a href="/buildings" class="btn btn-secondary">Cancel</a>
    </div>
</form>

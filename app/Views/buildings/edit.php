<h1>Edit Building</h1>

<form method="POST" action="/buildings/<?= $building['id'] ?>" class="form-card">
    <?= \App\Core\Csrf::field() ?>
    <input type="hidden" name="_method" value="PUT">
    <label>Building name
        <input type="text" name="name" value="<?= htmlspecialchars($building['name']) ?>" required>
    </label>
    <label>Address line 1
        <input type="text" name="address_line1" value="<?= htmlspecialchars($building['address_line1']) ?>">
    </label>
    <label>Address line 2
        <input type="text" name="address_line2" value="<?= htmlspecialchars($building['address_line2'] ?? '') ?>">
    </label>
    <div class="form-row">
        <label>City
            <input type="text" name="city" value="<?= htmlspecialchars($building['city']) ?>">
        </label>
        <label>State
            <input type="text" name="state" value="<?= htmlspecialchars($building['state'] ?? '') ?>">
        </label>
        <label>Postcode
            <input type="text" name="postcode" value="<?= htmlspecialchars($building['postcode'] ?? '') ?>">
        </label>
    </div>
    <label>Country
        <input type="text" name="country" value="<?= htmlspecialchars($building['country']) ?>">
    </label>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Update Building</button>
        <a href="/buildings/<?= $building['id'] ?>" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<form method="POST" action="/buildings/<?= $building['id'] ?>" class="danger-zone" onsubmit="return confirm('Delete this building? This cannot be undone.');">
    <?= \App\Core\Csrf::field() ?>
    <input type="hidden" name="_method" value="DELETE">
    <button type="submit" class="btn btn-danger">Delete Building</button>
</form>

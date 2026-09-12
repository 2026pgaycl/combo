<h1>Edit Unit <?= htmlspecialchars($unit['unit_number']) ?></h1>

<form method="POST" action="/units/<?= $unit['id'] ?>" class="form-card">
    <?= \App\Core\Csrf::field() ?>
    <input type="hidden" name="_method" value="PUT">

    <label>Floor
        <select name="floor_id" required>
            <?php foreach ($floors as $f): ?>
            <option value="<?= $f['id'] ?>" <?= $f['id'] == $unit['floor_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($f['name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Unit number
        <input type="text" name="unit_number" value="<?= htmlspecialchars($unit['unit_number']) ?>" required>
    </label>

    <label>Unit type
        <select name="unit_type">
            <?php foreach (['office', 'retail', 'storage'] as $type): ?>
            <option value="<?= $type ?>" <?= $unit['unit_type'] === $type ? 'selected' : '' ?>><?= ucfirst($type) ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <div class="form-row">
        <label>Size (sqft)
            <input type="number" step="0.01" name="size_sqft" value="<?= htmlspecialchars($unit['size_sqft']) ?>">
        </label>
        <label>Base rent (RM)
            <input type="number" step="0.01" name="base_rent" value="<?= htmlspecialchars($unit['base_rent']) ?>">
        </label>
    </div>

    <label>Status
        <select name="status">
            <?php foreach (['vacant', 'occupied', 'reserved', 'maintenance'] as $status): ?>
            <option value="<?= $status ?>" <?= $unit['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Update Unit</button>
        <a href="/units/<?= $unit['id'] ?>" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<form method="POST" action="/units/<?= $unit['id'] ?>" class="danger-zone" onsubmit="return confirm('Delete this unit? This cannot be undone.');">
    <?= \App\Core\Csrf::field() ?>
    <input type="hidden" name="_method" value="DELETE">
    <button type="submit" class="btn btn-danger">Delete Unit</button>
</form>

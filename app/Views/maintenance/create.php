<h1>Report Maintenance Issue</h1>

<form method="POST" action="/maintenance" class="form-card">
    <?= \App\Core\Csrf::field() ?>

    <label>Unit
        <select name="unit_id" required>
            <option value="">Select a unit</option>
            <?php foreach ($units as $u): ?>
            <option value="<?= $u['id'] ?>" <?= (int) $u['id'] === (int) $selectedUnitId ? 'selected' : '' ?>>
                <?= htmlspecialchars($u['building_name'] . ' — ' . $u['unit_number']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Category
        <select name="category">
            <option value="general">General</option>
            <option value="aircond">Aircond</option>
            <option value="plumbing">Plumbing</option>
            <option value="electrical">Electrical</option>
        </select>
    </label>

    <label>Description
        <textarea name="description" rows="4" required></textarea>
    </label>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Submit Ticket</button>
        <a href="/maintenance" class="btn btn-secondary">Cancel</a>
    </div>
</form>

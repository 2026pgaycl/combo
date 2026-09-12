<h1>Add Unit</h1>

<form method="POST" action="/units" class="form-card">
    <?= \App\Core\Csrf::field() ?>

    <label>Building
        <select id="building_id" required>
            <option value="">Select a building</option>
            <?php foreach ($buildings as $b): ?>
            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Floor
        <select name="floor_id" id="floor_id" required>
            <option value="">Select a building first</option>
        </select>
    </label>

    <label>Unit number
        <input type="text" name="unit_number" placeholder="e.g. 12-03" required>
    </label>

    <label>Unit type
        <select name="unit_type">
            <option value="office">Office</option>
            <option value="retail">Retail</option>
            <option value="storage">Storage</option>
        </select>
    </label>

    <div class="form-row">
        <label>Size (sqft)
            <input type="number" step="0.01" name="size_sqft" value="0">
        </label>
        <label>Base rent (RM)
            <input type="number" step="0.01" name="base_rent" value="0">
        </label>
    </div>

    <label>Status
        <select name="status">
            <option value="vacant">Vacant</option>
            <option value="reserved">Reserved</option>
            <option value="maintenance">Under maintenance</option>
        </select>
    </label>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save Unit</button>
        <a href="/units" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<script>
document.getElementById('building_id').addEventListener('change', function () {
    const buildingId = this.value;
    const floorSelect = document.getElementById('floor_id');
    floorSelect.innerHTML = '<option value="">Loading…</option>';
    if (!buildingId) {
        floorSelect.innerHTML = '<option value="">Select a building first</option>';
        return;
    }
    fetch('/api/buildings/' + buildingId + '/floors')
        .then(res => res.json())
        .then(floors => {
            if (!floors.length) {
                floorSelect.innerHTML = '<option value="">No floors yet — add one on the building page</option>';
                return;
            }
            floorSelect.innerHTML = floors.map(f => `<option value="${f.id}">${f.name}</option>`).join('');
        });
});
</script>

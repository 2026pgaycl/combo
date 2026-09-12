<div class="page-header">
    <h1>Units</h1>
    <a href="/units/create" class="btn btn-primary">+ Add Unit</a>
</div>

<form method="GET" action="/units" class="filter-bar">
    <label>Filter by building
        <select name="building_id" onchange="this.form.submit()">
            <option value="">All buildings</option>
            <?php foreach ($buildings as $b): ?>
            <option value="<?= $b['id'] ?>" <?= ($selectedBuildingId == $b['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($b['name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label>
</form>

<table class="data-table">
    <thead>
        <tr>
            <th>Unit</th><th>Building</th><th>Floor</th><th>Type</th>
            <th>Size (sqft)</th><th>Base Rent</th><th>Status</th><th>Tenant</th><th></th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($units)): ?>
        <tr><td colspan="9" class="text-muted">No units found.</td></tr>
        <?php endif; ?>
        <?php foreach ($units as $u): ?>
        <tr>
            <td><a href="/units/<?= $u['id'] ?>"><?= htmlspecialchars($u['unit_number']) ?></a></td>
            <td><?= htmlspecialchars($u['building_name']) ?></td>
            <td><?= htmlspecialchars($u['floor_name']) ?></td>
            <td><?= htmlspecialchars($u['unit_type']) ?></td>
            <td><?= number_format($u['size_sqft'], 0) ?></td>
            <td>RM <?= number_format($u['base_rent'], 2) ?></td>
            <td><span class="badge badge-<?= htmlspecialchars($u['status']) ?>"><?= htmlspecialchars($u['status']) ?></span></td>
            <td><?= htmlspecialchars($u['tenant_name'] ?? '—') ?></td>
            <td><a href="/units/<?= $u['id'] ?>/edit">Edit</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

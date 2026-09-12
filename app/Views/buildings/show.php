<div class="page-header">
    <h1><?= htmlspecialchars($building['name']) ?></h1>
    <a href="/buildings/<?= $building['id'] ?>/edit" class="btn btn-secondary">Edit</a>
</div>
<p class="text-muted">
    <?= htmlspecialchars($building['address_line1']) ?>,
    <?= htmlspecialchars($building['city']) ?>
    <?= htmlspecialchars($building['country']) ?>
</p>

<h2>Floors</h2>
<table class="data-table">
    <thead><tr><th>Floor</th><th>Order</th></tr></thead>
    <tbody>
        <?php if (empty($floors)): ?>
        <tr><td colspan="2" class="text-muted">No floors added yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($floors as $f): ?>
        <tr><td><?= htmlspecialchars($f['name']) ?></td><td><?= (int) $f['level_order'] ?></td></tr>
        <?php endforeach; ?>
    </tbody>
</table>

<form method="POST" action="/buildings/<?= $building['id'] ?>/floors" class="form-card form-card-inline">
    <?= \App\Core\Csrf::field() ?>
    <label>Floor name
        <input type="text" name="name" placeholder="e.g. Level 12" required>
    </label>
    <label>Sort order
        <input type="number" name="level_order" value="0">
    </label>
    <button type="submit" class="btn btn-primary">Add Floor</button>
</form>

<?php \App\Core\View::partial('partials.documents', [
    'documents' => $documents,
    'relatedType' => 'building',
    'relatedId' => $building['id'],
]); ?>

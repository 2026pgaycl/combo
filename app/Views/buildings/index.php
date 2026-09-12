<div class="page-header">
    <h1>Buildings</h1>
    <a href="/buildings/create" class="btn btn-primary">+ Add Building</a>
</div>

<?php \App\Core\View::partial('partials.csv_toolbar', [
    'exportUrl' => '/buildings/export',
    'importUrl' => '/buildings/import',
]); ?>

<table class="data-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>City</th>
            <th>Units</th>
            <th>Occupied</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($buildings)): ?>
        <tr><td colspan="5" class="text-muted">No buildings yet. Add your first one to get started.</td></tr>
        <?php endif; ?>
        <?php foreach ($buildings as $b): ?>
        <tr>
            <td><a href="/buildings/<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></a></td>
            <td><?= htmlspecialchars($b['city']) ?></td>
            <td><?= (int) $b['unit_count'] ?></td>
            <td><?= (int) $b['occupied_count'] ?></td>
            <td><a href="/buildings/<?= $b['id'] ?>/edit">Edit</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="page-header">
    <h1>Unit <?= htmlspecialchars($unit['unit_number']) ?></h1>
    <div class="header-actions">
        <a href="/maintenance/create?unit_id=<?= $unit['id'] ?>" class="btn btn-secondary">+ Report Issue</a>
        <a href="/units/<?= $unit['id'] ?>/edit" class="btn btn-secondary">Edit</a>
    </div>
</div>

<dl class="detail-list">
    <dt>Building</dt><dd><?= htmlspecialchars($unit['building_name']) ?></dd>
    <dt>Floor</dt><dd><?= htmlspecialchars($unit['floor_name']) ?></dd>
    <dt>Type</dt><dd><?= htmlspecialchars($unit['unit_type']) ?></dd>
    <dt>Size</dt><dd><?= number_format($unit['size_sqft'], 0) ?> sqft</dd>
    <dt>Base rent</dt><dd>RM <?= number_format($unit['base_rent'], 2) ?></dd>
    <dt>Status</dt><dd><span class="badge badge-<?= htmlspecialchars($unit['status']) ?>"><?= htmlspecialchars($unit['status']) ?></span></dd>
</dl>

<h2>Photos</h2>
<div class="photo-grid">
    <?php if (empty($photos)): ?>
    <p class="text-muted">No photos yet.</p>
    <?php endif; ?>
    <?php foreach ($photos as $p): ?>
    <div class="photo-tile">
        <a href="/<?= htmlspecialchars($p['file_path']) ?>" target="_blank">
            <img src="/<?= htmlspecialchars($p['file_path']) ?>" alt="Photo of unit <?= htmlspecialchars($unit['unit_number']) ?>">
        </a>
        <form method="POST" action="/units/photos/<?= $p['id'] ?>" onsubmit="return confirm('Delete this photo?');">
            <?= \App\Core\Csrf::field() ?>
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="text-link-danger">Delete</button>
        </form>
    </div>
    <?php endforeach; ?>
</div>

<form method="POST" action="/units/<?= $unit['id'] ?>/photos" enctype="multipart/form-data" class="form-card form-card-inline">
    <?= \App\Core\Csrf::field() ?>
    <label>Photo
        <input type="file" name="photo" accept="image/*" required>
    </label>
    <button type="submit" class="btn btn-primary">Upload</button>
</form>

<?php \App\Core\View::partial('partials.documents', [
    'documents' => $documents,
    'relatedType' => 'unit',
    'relatedId' => $unit['id'],
]); ?>

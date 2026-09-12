<h2>Documents</h2>
<table class="data-table">
    <thead><tr><th>Title</th><th>Expiry</th><th>Uploaded</th><th></th></tr></thead>
    <tbody>
        <?php if (empty($documents)): ?>
        <tr><td colspan="4" class="text-muted">No documents yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($documents as $d): ?>
        <tr class="<?= (!empty($d['expiry_date']) && $d['expiry_date'] < date('Y-m-d')) ? 'row-warning' : '' ?>">
            <td><a href="/<?= htmlspecialchars($d['file_path']) ?>" target="_blank"><?= htmlspecialchars($d['title']) ?></a></td>
            <td><?= htmlspecialchars($d['expiry_date'] ?? '—') ?></td>
            <td><?= htmlspecialchars($d['created_at']) ?></td>
            <td>
                <form method="POST" action="/documents/<?= $d['id'] ?>" onsubmit="return confirm('Delete this document?');">
                    <?= \App\Core\Csrf::field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="text-link-danger">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<form method="POST" action="/documents" enctype="multipart/form-data" class="form-card form-card-inline">
    <?= \App\Core\Csrf::field() ?>
    <input type="hidden" name="related_type" value="<?= htmlspecialchars($relatedType) ?>">
    <input type="hidden" name="related_id" value="<?= (int) $relatedId ?>">
    <label>Title
        <input type="text" name="title" required>
    </label>
    <label>Expiry date (optional)
        <input type="date" name="expiry_date">
    </label>
    <label>File
        <input type="file" name="document" required>
    </label>
    <button type="submit" class="btn btn-primary">Upload</button>
</form>

<div class="page-header">
    <h1>Documents</h1>
</div>

<table class="data-table">
    <thead>
        <tr><th>Title</th><th>Attached to</th><th>Type</th><th>Expiry</th><th>Uploaded</th><th></th></tr>
    </thead>
    <tbody>
        <?php if (empty($documents)): ?>
        <tr><td colspan="6" class="text-muted">No documents yet. Add one from a building, tenant, lease, or unit page.</td></tr>
        <?php endif; ?>
        <?php foreach ($documents as $d): ?>
        <tr class="<?= (!empty($d['expiry_date']) && $d['expiry_date'] < date('Y-m-d')) ? 'row-warning' : '' ?>">
            <td><a href="/<?= htmlspecialchars($d['file_path']) ?>" target="_blank"><?= htmlspecialchars($d['title']) ?></a></td>
            <td><?= htmlspecialchars($d['related_label'] ?? '—') ?></td>
            <td><?= htmlspecialchars(ucfirst($d['related_type'])) ?></td>
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

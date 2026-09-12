<div class="page-header">
    <h1>My Documents</h1>
</div>

<h2>Tenant Documents</h2>
<table class="data-table">
    <thead><tr><th>Title</th><th>Expiry</th><th>Uploaded</th></tr></thead>
    <tbody>
        <?php if (empty($tenantDocuments)): ?>
        <tr><td colspan="3" class="text-muted">None on file.</td></tr>
        <?php endif; ?>
        <?php foreach ($tenantDocuments as $d): ?>
        <tr>
            <td><a href="/<?= htmlspecialchars($d['file_path']) ?>" target="_blank"><?= htmlspecialchars($d['title']) ?></a></td>
            <td><?= htmlspecialchars($d['expiry_date'] ?? '—') ?></td>
            <td><?= htmlspecialchars($d['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php foreach ($leaseSections as $section): ?>
<h2>Lease Documents — <?= htmlspecialchars($section['label']) ?></h2>
<table class="data-table">
    <thead><tr><th>Title / Type</th><th>Expiry</th><th>Uploaded</th></tr></thead>
    <tbody>
        <?php if (empty($section['documents'])): ?>
        <tr><td colspan="3" class="text-muted">None on file.</td></tr>
        <?php endif; ?>
        <?php foreach ($section['documents'] as $d): ?>
        <tr>
            <td><a href="/<?= htmlspecialchars($d['file_path']) ?>" target="_blank"><?= htmlspecialchars($d['label']) ?></a></td>
            <td><?= htmlspecialchars($d['expiry_date'] ?? '—') ?></td>
            <td><?= htmlspecialchars($d['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endforeach; ?>

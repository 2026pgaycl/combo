<div class="csv-toolbar">
    <a href="<?= htmlspecialchars($exportUrl) ?>" class="btn btn-secondary">Export CSV</a>
    <?php if (!empty($importUrl)): ?>
    <form method="POST" action="<?= htmlspecialchars($importUrl) ?>" enctype="multipart/form-data" class="csv-import-form">
        <?= \App\Core\Csrf::field() ?>
        <input type="file" name="csv" accept=".csv" required>
        <button type="submit" class="btn btn-secondary">Import CSV</button>
    </form>
    <?php endif; ?>
</div>

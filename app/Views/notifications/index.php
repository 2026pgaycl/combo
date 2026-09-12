<div class="page-header">
    <h1>Notifications</h1>
    <a href="/notifications/create" class="btn btn-primary">+ Queue Notification</a>
</div>

<form method="GET" action="/notifications" class="filter-bar">
    <label>Status
        <select name="status" onchange="this.form.submit()">
            <option value="">All statuses</option>
            <?php foreach (['queued', 'sent', 'failed'] as $s): ?>
            <option value="<?= $s ?>" <?= $selectedStatus === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
</form>

<table class="data-table">
    <thead>
        <tr><th>Recipient</th><th>Channel</th><th>Subject</th><th>Status</th><th>Created</th><th>Sent</th></tr>
    </thead>
    <tbody>
        <?php if (empty($notifications)): ?>
        <tr><td colspan="6" class="text-muted">No notifications yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($notifications as $n): ?>
        <tr class="<?= $n['status'] === 'failed' ? 'row-warning' : '' ?>">
            <td><a href="/notifications/<?= $n['id'] ?>"><?= htmlspecialchars($n['user_name'] ?? 'All / unspecified') ?></a></td>
            <td><?= htmlspecialchars(strtoupper($n['channel'])) ?></td>
            <td><?= htmlspecialchars($n['subject'] ?? '—') ?></td>
            <td><span class="badge badge-<?= htmlspecialchars($n['status']) ?>"><?= htmlspecialchars($n['status']) ?></span></td>
            <td><?= htmlspecialchars($n['created_at']) ?></td>
            <td><?= htmlspecialchars($n['sent_at'] ?? '—') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="page-header">
    <h1>Notification #<?= $notification['id'] ?></h1>
    <span class="badge badge-<?= htmlspecialchars($notification['status']) ?>"><?= htmlspecialchars($notification['status']) ?></span>
</div>

<dl class="detail-list">
    <dt>Recipient</dt><dd><?= htmlspecialchars($notification['user_name'] ?? 'All / unspecified') ?></dd>
    <dt>Channel</dt><dd><?= htmlspecialchars(strtoupper($notification['channel'])) ?></dd>
    <dt>Subject</dt><dd><?= htmlspecialchars($notification['subject'] ?? '—') ?></dd>
    <dt>Queued</dt><dd><?= htmlspecialchars($notification['created_at']) ?></dd>
    <dt>Sent</dt><dd><?= htmlspecialchars($notification['sent_at'] ?? '—') ?></dd>
</dl>

<h2>Message</h2>
<p><?= nl2br(htmlspecialchars($notification['body'] ?? '')) ?></p>

<h2>Update Status</h2>
<form method="POST" action="/notifications/<?= $notification['id'] ?>" class="form-card form-card-inline">
    <?= \App\Core\Csrf::field() ?>
    <input type="hidden" name="_method" value="PUT">
    <label>Status
        <select name="status">
            <?php foreach (['queued', 'sent', 'failed'] as $s): ?>
            <option value="<?= $s ?>" <?= $notification['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <button type="submit" class="btn btn-primary">Save</button>
</form>

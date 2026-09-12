<h1>Queue Notification</h1>

<p class="text-muted">This app doesn't send real email/SMS/push yet — queuing here just logs the message so you can track what needs to go out and mark it sent once you've delivered it through your own channel.</p>

<form method="POST" action="/notifications" class="form-card">
    <?= \App\Core\Csrf::field() ?>

    <label>Recipient
        <select name="user_id">
            <option value="">All / unspecified</option>
            <?php foreach ($users as $u): ?>
            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['email']) ?>)</option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Channel
        <select name="channel">
            <option value="email">Email</option>
            <option value="sms">SMS</option>
            <option value="push">Push</option>
        </select>
    </label>

    <label>Subject
        <input type="text" name="subject">
    </label>

    <label>Message
        <textarea name="body" rows="4"></textarea>
    </label>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Queue Notification</button>
        <a href="/notifications" class="btn btn-secondary">Cancel</a>
    </div>
</form>

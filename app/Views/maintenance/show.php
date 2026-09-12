<div class="page-header">
    <h1>Ticket #<?= $ticket['id'] ?> — <?= htmlspecialchars(ucfirst($ticket['category'])) ?></h1>
    <span class="badge badge-<?= htmlspecialchars($ticket['status']) ?>"><?= htmlspecialchars(str_replace('_', ' ', $ticket['status'])) ?></span>
</div>

<dl class="detail-list">
    <dt>Unit</dt><dd><?= htmlspecialchars($ticket['building_name'] . ' — ' . $ticket['unit_number']) ?></dd>
    <dt>Tenant</dt><dd><?= htmlspecialchars($ticket['tenant_name'] ?? '—') ?></dd>
    <dt>Reported</dt><dd><?= htmlspecialchars($ticket['created_at']) ?></dd>
    <dt>Assigned to</dt><dd><?= htmlspecialchars($ticket['assignee_name'] ?? 'Unassigned') ?></dd>
    <?php if ($ticket['cost'] !== null): ?>
    <dt>Cost</dt><dd>RM <?= number_format($ticket['cost'], 2) ?></dd>
    <?php endif; ?>
</dl>

<h2>Description</h2>
<p><?= nl2br(htmlspecialchars($ticket['description'])) ?></p>

<?php if (!empty($ticket['photo_path'])): ?>
<div class="photo-grid">
    <div class="photo-tile">
        <a href="/<?= htmlspecialchars($ticket['photo_path']) ?>" target="_blank">
            <img src="/<?= htmlspecialchars($ticket['photo_path']) ?>" alt="Photo for ticket #<?= $ticket['id'] ?>">
        </a>
    </div>
</div>
<?php endif; ?>

<h2>Update Ticket</h2>
<form method="POST" action="/maintenance/<?= $ticket['id'] ?>" enctype="multipart/form-data" class="form-card">
    <?= \App\Core\Csrf::field() ?>
    <input type="hidden" name="_method" value="PUT">

    <div class="form-row">
        <label>Status
            <select name="status">
                <?php foreach (['open', 'in_progress', 'resolved', 'closed'] as $s): ?>
                <option value="<?= $s ?>" <?= $ticket['status'] === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $s)) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Assigned to
            <select name="assigned_to">
                <option value="">Unassigned</option>
                <?php foreach ($staff as $s): ?>
                <option value="<?= $s['id'] ?>" <?= (int) $ticket['assigned_to'] === (int) $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Cost (RM)
            <input type="number" step="0.01" name="cost" value="<?= htmlspecialchars($ticket['cost'] ?? '') ?>">
        </label>
    </div>

    <label>Photo (<?= !empty($ticket['photo_path']) ? 'replace' : 'add' ?>, optional)
        <input type="file" name="photo" accept="image/*">
    </label>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>

<h2>Activity</h2>
<table class="data-table">
    <thead><tr><th>Date</th><th>By</th><th>Note</th></tr></thead>
    <tbody>
        <?php if (empty($updates)): ?>
        <tr><td colspan="3" class="text-muted">No notes yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($updates as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['created_at']) ?></td>
            <td><?= htmlspecialchars($u['user_name']) ?></td>
            <td><?= nl2br(htmlspecialchars($u['note'])) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<form method="POST" action="/maintenance/<?= $ticket['id'] ?>/updates" class="form-card form-card-inline">
    <?= \App\Core\Csrf::field() ?>
    <label>Add a note
        <input type="text" name="note" placeholder="e.g. Technician dispatched" required>
    </label>
    <button type="submit" class="btn btn-secondary">Add Note</button>
</form>

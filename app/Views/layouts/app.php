<?php
use App\Core\Auth;
$user = Auth::user();
$flashSuccess = $_SESSION['_flash']['success'] ?? null;
$flashError = $_SESSION['_flash']['error'] ?? null;
unset($_SESSION['_flash']);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$isActive = function (string $href) use ($path): string {
    return $href === '/' ? '' : (str_starts_with($path, $href) ? ' active' : '');
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Office Building Rental Management System</title>
<link rel="stylesheet" href="/assets/css/style.css?v=<?= @filemtime($_SERVER['DOCUMENT_ROOT'] . '/assets/css/style.css') ?: time() ?>">
</head>
<body>
<div class="app-shell" id="appShell">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <span>Office Building Rental Management System</span>
            <button type="button" class="sidebar-toggle" id="sidebarClose" aria-label="Close menu"><?= \App\Core\View::icon('x') ?></button>
        </div>
        <nav class="sidebar-nav">
            <?php if (($user['role_slug'] ?? null) === 'tenant'): ?>
            <a href="/portal" class="<?= $path === '/portal' ? 'active' : '' ?>"><?= \App\Core\View::icon('layout-dashboard') ?> Dashboard</a>
            <a href="/portal/invoices" class="<?= $isActive('/portal/invoices') ?>"><?= \App\Core\View::icon('receipt') ?> Invoices</a>
            <a href="/portal/maintenance" class="<?= $isActive('/portal/maintenance') ?>"><?= \App\Core\View::icon('wrench') ?> Maintenance</a>
            <a href="/portal/documents" class="<?= $isActive('/portal/documents') ?>"><?= \App\Core\View::icon('file-text') ?> Documents</a>
            <a href="/portal/profile" class="<?= $isActive('/portal/profile') ?>"><?= \App\Core\View::icon('user') ?> My Profile</a>
            <?php else: ?>
            <a href="/dashboard" class="<?= $isActive('/dashboard') ?>"><?= \App\Core\View::icon('layout-dashboard') ?> Dashboard</a>
            <a href="/buildings" class="<?= $isActive('/buildings') ?>"><?= \App\Core\View::icon('building') ?> Buildings</a>
            <a href="/units" class="<?= $isActive('/units') ?>"><?= \App\Core\View::icon('package') ?> Units</a>
            <a href="/tenants" class="<?= $isActive('/tenants') ?>"><?= \App\Core\View::icon('users') ?> Tenants</a>
            <a href="/leases" class="<?= $isActive('/leases') ?>"><?= \App\Core\View::icon('file-text') ?> Leases</a>
            <a href="/invoices" class="<?= $isActive('/invoices') ?>"><?= \App\Core\View::icon('receipt') ?> Invoices</a>
            <a href="/maintenance" class="<?= $isActive('/maintenance') ?>"><?= \App\Core\View::icon('wrench') ?> Maintenance</a>
            <a href="/documents" class="<?= $isActive('/documents') ?>"><?= \App\Core\View::icon('file-text') ?> Documents</a>
            <a href="/notifications" class="<?= $isActive('/notifications') ?>"><?= \App\Core\View::icon('bell') ?> Notifications</a>
            <a href="/help" class="<?= $isActive('/help') ?>"><?= \App\Core\View::icon('help-circle') ?> Help</a>
            <?php endif; ?>
        </nav>
        <?php if ($user): ?>
        <div class="sidebar-user">
            <div class="sidebar-user-name"><?= htmlspecialchars($user['name']) ?></div>
            <div class="sidebar-user-role"><?= htmlspecialchars($user['role_name']) ?></div>
            <form method="POST" action="/logout">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="link-button"><?= \App\Core\View::icon('log-out') ?> Log out</button>
            </form>
        </div>
        <?php endif; ?>
    </aside>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <main class="main-content">
        <button type="button" class="sidebar-toggle sidebar-toggle-open" id="sidebarOpen" aria-label="Open menu"><?= \App\Core\View::icon('menu') ?></button>

        <?php if ($flashSuccess): ?>
            <div class="alert alert-success"><?= htmlspecialchars($flashSuccess) ?></div>
        <?php endif; ?>
        <?php if ($flashError): ?>
            <div class="alert alert-error"><?= htmlspecialchars($flashError) ?></div>
        <?php endif; ?>

        <?= $content ?>
    </main>
</div>
<script>
(function () {
    var shell = document.getElementById('appShell');
    var open = document.getElementById('sidebarOpen');
    var close = document.getElementById('sidebarClose');
    var overlay = document.getElementById('sidebarOverlay');
    function setOpen(v) { shell.classList.toggle('sidebar-open', v); }
    if (open) open.addEventListener('click', function () { setOpen(true); });
    if (close) close.addEventListener('click', function () { setOpen(false); });
    if (overlay) overlay.addEventListener('click', function () { setOpen(false); });
})();
</script>
</body>
</html>

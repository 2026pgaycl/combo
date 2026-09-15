<?php
use App\Core\Auth;
$user = Auth::user();
$flashSuccess = $_SESSION['_flash']['success'] ?? null;
$flashError = $_SESSION['_flash']['error'] ?? null;
unset($_SESSION['_flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Office Building Rental Management System</title>
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="sidebar-brand">Office Building Rental Management System</div>
        <nav class="sidebar-nav">
            <?php if (($user['role_slug'] ?? null) === 'tenant'): ?>
            <a href="/portal"><?= \App\Core\View::icon('layout-dashboard') ?> Dashboard</a>
            <a href="/portal/invoices"><?= \App\Core\View::icon('receipt') ?> Invoices</a>
            <a href="/portal/maintenance"><?= \App\Core\View::icon('wrench') ?> Maintenance</a>
            <a href="/portal/documents"><?= \App\Core\View::icon('file-text') ?> Documents</a>
            <a href="/portal/profile"><?= \App\Core\View::icon('user') ?> My Profile</a>
            <?php else: ?>
            <a href="/dashboard"><?= \App\Core\View::icon('layout-dashboard') ?> Dashboard</a>
            <a href="/buildings"><?= \App\Core\View::icon('building') ?> Buildings</a>
            <a href="/units"><?= \App\Core\View::icon('package') ?> Units</a>
            <a href="/tenants"><?= \App\Core\View::icon('users') ?> Tenants</a>
            <a href="/leases"><?= \App\Core\View::icon('file-text') ?> Leases</a>
            <a href="/invoices"><?= \App\Core\View::icon('receipt') ?> Invoices</a>
            <a href="/maintenance"><?= \App\Core\View::icon('wrench') ?> Maintenance</a>
            <a href="/documents"><?= \App\Core\View::icon('file-text') ?> Documents</a>
            <a href="/notifications"><?= \App\Core\View::icon('bell') ?> Notifications</a>
            <a href="/help"><?= \App\Core\View::icon('help-circle') ?> Help</a>
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

    <main class="main-content">
        <?php if ($flashSuccess): ?>
            <div class="alert alert-success"><?= htmlspecialchars($flashSuccess) ?></div>
        <?php endif; ?>
        <?php if ($flashError): ?>
            <div class="alert alert-error"><?= htmlspecialchars($flashError) ?></div>
        <?php endif; ?>

        <?= $content ?>
    </main>
</div>
</body>
</html>

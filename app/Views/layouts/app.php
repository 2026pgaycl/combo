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
            <a href="/dashboard">Dashboard</a>
            <a href="/buildings">Buildings</a>
            <a href="/units">Units</a>
            <a href="/tenants">Tenants</a>
            <a href="/leases">Leases</a>
            <a href="/invoices">Invoices</a>
            <a href="/maintenance">Maintenance</a>
            <a href="/documents">Documents</a>
            <a href="/notifications">Notifications</a>
        </nav>
        <?php if ($user): ?>
        <div class="sidebar-user">
            <div class="sidebar-user-name"><?= htmlspecialchars($user['name']) ?></div>
            <div class="sidebar-user-role"><?= htmlspecialchars($user['role_name']) ?></div>
            <form method="POST" action="/logout">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="link-button">Log out</button>
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

<?php
$error = $_SESSION['_flash']['error'] ?? null;
unset($_SESSION['_flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Log in — Office Building Rental Management System</title>
<link rel="stylesheet" href="/assets/css/style.css?v=<?= @filemtime($_SERVER['DOCUMENT_ROOT'] . '/assets/css/style.css') ?: time() ?>">
</head>
<body>
<div class="guest-wrapper">
    <div class="guest-visual">
        <div class="guest-visual-brand"><?= \App\Core\View::icon('building') ?> Combo</div>
        <h2>Run every building from one place.</h2>
        <p>Units, tenants, leases, billing, maintenance and documents — one console for the whole portfolio.</p>
    </div>

    <div class="guest-form-panel">
        <div class="guest-card">
            <div class="guest-logo">
                <?= \App\Core\View::icon('building', 'guest-mark') ?>
                <h1>Office Building Rental Management System</h1>
                <p>Sign in to your account to continue.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= \App\Core\View::icon('alert-circle') ?> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="/login">
                <?= \App\Core\Csrf::field() ?>

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control" type="email" id="email" name="email" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-control" type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Log in</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>

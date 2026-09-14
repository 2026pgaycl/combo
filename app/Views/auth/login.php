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
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="guest-wrapper">
    <div class="guest-card">
        <div class="guest-logo">
            <?= \App\Core\View::icon('building', 'guest-mark') ?>
            <h1>Office Building Rental Management System</h1>
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
</body>
</html>

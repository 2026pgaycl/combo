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
<body class="auth-body">
<div class="auth-card">
    <div class="auth-brand">Office Building Rental Management System</div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/login">
        <?= \App\Core\Csrf::field() ?>
        <label>Email
            <input type="email" name="email" required autofocus>
        </label>
        <label>Password
            <input type="password" name="password" required>
        </label>
        <button type="submit" class="btn btn-primary btn-block">Log in</button>
    </form>
</div>
</body>
</html>

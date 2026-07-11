<?php
session_start();
require __DIR__ . '/config.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } else {
        try {
            $pdo = db_connect();
            $stmt = $pdo->prepare('SELECT id, username, password_hash FROM users WHERE username = :username');
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: welcome.php');
                exit;
            }

            $error = 'Invalid username or password.';
        } catch (PDOException $e) {
            $error = 'Could not reach the database.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Login - cicd-approval-demo</title>
  <style>
    body { font-family: system-ui, sans-serif; max-width: 360px; margin: 4rem auto; }
    label { display: block; margin-top: 1rem; font-weight: 600; }
    input { width: 100%; padding: 0.5rem; margin-top: 0.25rem; box-sizing: border-box; }
    button { margin-top: 1.5rem; padding: 0.5rem 1rem; }
    .error { color: #b00020; margin-top: 1rem; }
    .hint { color: #666; font-size: 0.85rem; margin-top: 2rem; }
  </style>
</head>
<body>
  <h1>Log in</h1>
  <form method="post" action="login.php">
    <label for="username">Username</label>
    <input type="text" id="username" name="username" autocomplete="username" required />

    <label for="password">Password</label>
    <input type="password" id="password" name="password" autocomplete="current-password" required />

    <button type="submit">Log in</button>
  </form>

  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <p class="hint">Demo accounts: admin / admin123, demo / demo123</p>
</body>
</html>

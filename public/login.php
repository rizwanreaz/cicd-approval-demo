<?php
session_start();
require __DIR__ . '/config.php';
require __DIR__ . '/../src/Auth.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        $pdo = db_connect();
        $user = Auth::attempt($pdo, $username, $password);

        if ($user) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: dashboard.php');
            exit;
        }

        $error = 'Invalid username or password.';
    } catch (PDOException $e) {
        $error = 'Could not reach the database.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Login - cicd-approval-demo</title>
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
  <div class="page">
    <div class="card">
      <h1>Log in</h1>
      <form method="post" action="login.php" class="js-form">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" autocomplete="username" required />

        <label for="password">Password</label>
        <div class="password-field">
          <input type="password" id="password" name="password" autocomplete="current-password" required />
          <button type="button" class="password-toggle">Show</button>
        </div>

        <button type="submit">Log in</button>
      </form>

      <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <p class="hint">Demo accounts: admin / admin123, demo / demo123</p>
      <a class="nav-link" href="register.php">Need an account? Register</a>
    </div>
  </div>
  <script src="assets/app.js"></script>
</body>
</html>

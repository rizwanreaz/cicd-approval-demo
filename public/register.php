<?php
session_start();
require __DIR__ . '/config.php';
require __DIR__ . '/../src/Registration.php';

$errors = [];
$success = false;
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    try {
        $pdo = db_connect();
        $result = Registration::register($pdo, $username, $email, $password, $passwordConfirm);

        if ($result['ok']) {
            $success = true;
            $username = '';
            $email = '';
        } else {
            $errors = $result['errors'];
        }
    } catch (PDOException $e) {
        $errors = ['Could not reach the database.'];
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Register - cicd-approval-demo</title>
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
  <div class="page">
    <div class="card">
      <h1>Create an account</h1>

      <?php if ($success): ?>
        <p class="success">Account created &mdash; please log in.</p>
        <a class="nav-link" href="login.php">Go to login</a>
      <?php else: ?>
        <form method="post" action="register.php" class="js-form" novalidate>
          <label for="username">Username</label>
          <input
            type="text"
            id="username"
            name="username"
            data-register-field="1"
            autocomplete="username"
            value="<?= htmlspecialchars($username) ?>"
            required
          />
          <p class="field-hint" data-hint-for="username">3-30 characters: letters, numbers, underscores.</p>

          <label for="email">Email</label>
          <input
            type="email"
            id="email"
            name="email"
            data-register-field="1"
            autocomplete="email"
            value="<?= htmlspecialchars($email) ?>"
            required
          />
          <p class="field-hint" data-hint-for="email">We will only use this for your account.</p>

          <label for="password">Password</label>
          <div class="password-field">
            <input
              type="password"
              id="password"
              name="password"
              data-register-field="1"
              autocomplete="new-password"
              required
            />
            <button type="button" class="password-toggle">Show</button>
          </div>
          <p class="field-hint" data-hint-for="password">At least 8 characters.</p>

          <label for="password_confirm">Confirm password</label>
          <div class="password-field">
            <input
              type="password"
              id="password_confirm"
              name="password_confirm"
              data-register-field="1"
              autocomplete="new-password"
              required
            />
            <button type="button" class="password-toggle">Show</button>
          </div>
          <p class="field-hint" data-hint-for="password_confirm"></p>

          <button type="submit">Create account</button>
        </form>

        <?php if ($errors): ?>
          <ul class="error-list">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <a class="nav-link" href="login.php">Already have an account? Log in</a>
      <?php endif; ?>
    </div>
  </div>
  <script src="assets/app.js"></script>
</body>
</html>

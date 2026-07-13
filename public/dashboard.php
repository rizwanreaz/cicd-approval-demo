<?php
session_start();
require __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$account = null;
$users = [];
$dbError = null;

try {
    $pdo = db_connect();

    $stmt = $pdo->prepare('SELECT username, email, created_at FROM users WHERE id = :id');
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $account = $stmt->fetch();

    $users = $pdo->query('SELECT username, created_at FROM users ORDER BY created_at DESC')->fetchAll();
} catch (PDOException $e) {
    $dbError = 'Could not load data from the database.';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Dashboard - cicd-approval-demo</title>
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
  <div class="page wide">
    <div class="card">
      <div class="topbar">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h1>
        <a href="logout.php">Log out</a>
      </div>

      <?php if ($dbError): ?>
        <p class="error"><?= htmlspecialchars($dbError) ?></p>
      <?php else: ?>
        <h2 class="section-title">Your account</h2>
        <dl class="account-details">
          <dt>Username</dt>
          <dd><?= htmlspecialchars($account['username']) ?></dd>
          <dt>Email</dt>
          <dd><?= htmlspecialchars($account['email']) ?></dd>
          <dt>Joined</dt>
          <dd><?= htmlspecialchars($account['created_at']) ?></dd>
        </dl>

        <h2 class="section-title">Registered users</h2>
        <table>
          <thead>
            <tr><th>Username</th><th>Joined</th></tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
              <tr>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['created_at']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>

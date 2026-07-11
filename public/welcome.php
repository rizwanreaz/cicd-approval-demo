<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Welcome - cicd-approval-demo</title>
</head>
<body>
  <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h1>
  <p>You are logged in.</p>
  <p><a href="logout.php">Log out</a></p>
</body>
</html>

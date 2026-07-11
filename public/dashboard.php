<?php
session_start();
require __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$customers = [];
$orders = [];
$dbError = null;

try {
    $pdo = db_connect();
    $customers = $pdo->query('SELECT id, first_name, last_name, email FROM customers ORDER BY id')->fetchAll();
    $orders = $pdo->query(
        'SELECT orders.id, customers.first_name, customers.last_name, orders.order_date, orders.total_amount, orders.status
         FROM orders JOIN customers ON customers.id = orders.customer_id
         ORDER BY orders.order_date DESC'
    )->fetchAll();
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
        <h2 class="section-title">Customers</h2>
        <table>
          <thead>
            <tr><th>Name</th><th>Email</th></tr>
          </thead>
          <tbody>
            <?php foreach ($customers as $customer): ?>
              <tr>
                <td><?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></td>
                <td><?= htmlspecialchars($customer['email']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <h2 class="section-title">Orders</h2>
        <table>
          <thead>
            <tr><th>Customer</th><th>Date</th><th>Total</th><th>Status</th></tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): ?>
              <tr>
                <td><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></td>
                <td><?= htmlspecialchars($order['order_date']) ?></td>
                <td>$<?= htmlspecialchars(number_format((float) $order['total_amount'], 2)) ?></td>
                <td><?= htmlspecialchars($order['status']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>

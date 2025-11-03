<?php
session_start();

// ✅ Correct include path
include(__DIR__ . '/../includes/db_connect.php');


// ✅ Ensure admin session exists
if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// ✅ Ensure user_id is passed
if(!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    header("Location: manage_users.php");
    exit;
}

$user_id = intval($_GET['user_id']);

// ✅ Fetch user info safely
$user_result = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
if(!$user_result || mysqli_num_rows($user_result) === 0) {
    header("Location: manage_users.php?error=user_not_found");
    exit;
}
$user = mysqli_fetch_assoc($user_result);

// ✅ Fetch user's orders
$orders = mysqli_query($conn, "
    SELECT * FROM orders 
    WHERE user_id=$user_id 
    ORDER BY created_at DESC
");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($user['first_name'].' '.$user['last_name']) ?> - Purchases</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
  background:#0f1724;
  color:#f8f9fa;
  font-family:'Poppins', sans-serif;
}
h3, h5 { color:#ffffff; }
.card {
  background:#16233a;
  border-radius:10px;
  padding:16px;
  color:#e6eef6;
  box-shadow:0 0 10px rgba(0,0,0,0.3);
}
.table {
  color:#f8f9fa !important;
  background-color:#1b2a47;
}
.table thead th {
  background-color:#22355c;
  color:#fff;
}
.table-striped tbody tr:nth-of-type(odd) {
  background-color:rgba(255,255,255,0.05);
}
.table-striped tbody tr:nth-of-type(even) {
  background-color:rgba(255,255,255,0.1);
}
a, a:hover { color:#0dcaf0; text-decoration:none; }
.btn-light {
  background-color:#e6eef6;
  color:#000;
  font-weight:600;
}
.btn-light:hover {
  background-color:#cfd8e2;
}
img {
  border:1px solid #555;
}
</style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?= htmlspecialchars($user['first_name'].' '.$user['last_name']) ?>'s Purchases</h3>
    <a href="manage_users.php" class="btn btn-light">Back</a>
  </div>

  <?php if(mysqli_num_rows($orders) > 0): ?>
    <?php while($order = mysqli_fetch_assoc($orders)): ?>
      <div class="card mb-4">
        <h5>Order #<?= $order['id'] ?> 
          <small class="text-info">(<?= htmlspecialchars($order['status']) ?>)</small>
        </h5>
        <p class="mb-1"><strong>Total:</strong> ₱ <?= number_format($order['total'], 2) ?></p>
        <p class="mb-1"><strong>Placed on:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
        <p class="mb-3"><strong>Delivery Date:</strong> <?= htmlspecialchars($order['delivery_date'] ?? 'N/A') ?></p>

        <?php
        // ✅ Fetch order items with safe join
        $items = mysqli_query($conn, "
          SELECT oi.*, p.name, p.image 
          FROM order_items oi 
          JOIN products p ON oi.product_id = p.id
          WHERE oi.order_id = {$order['id']}
        ");
        ?>

        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead>
              <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php while($item = mysqli_fetch_assoc($items)): ?>
                <?php 
                  // ✅ Fixed image path — works inside admin folder
                  $image_path = "../images/" . $item['image'];
                  if(empty($item['image']) || !file_exists($image_path)){
                      $image_path = "../images/default.jpg";
                  }
                ?>
                <tr>
                  <td>
                    <img src="<?= htmlspecialchars($image_path) ?>" 
                         style="width:50px; height:50px; object-fit:cover; border-radius:4px; margin-right:8px;">
                    <?= htmlspecialchars($item['name']) ?>
                  </td>
                  <td>₱ <?= number_format($item['price'], 2) ?></td>
                  <td><?= $item['quantity'] ?></td>
                  <td>₱ <?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <div class="alert alert-info text-dark bg-light">This customer has no purchase history yet.</div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

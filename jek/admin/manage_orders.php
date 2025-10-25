<?php
session_start();
include('../db_connect.php');
if(!isset($_SESSION['admin_id'])) header("Location: admin_login.php");

// handle update
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])){
    $order_id = intval($_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $delivery_date = (!empty($_POST['delivery_date'])) ? mysqli_real_escape_string($conn, $_POST['delivery_date']) : null;
    $updated_at = date('Y-m-d H:i:s');

    $sql = "UPDATE orders SET status='$status', updated_at='$updated_at' ";
    if($delivery_date) $sql .= ", delivery_date='$delivery_date' ";
    $sql .= "WHERE id=$order_id";
    mysqli_query($conn, $sql);
    header("Location: manage_orders.php");
    exit;
}

// fetch
$orders = mysqli_query($conn, "SELECT o.*, u.first_name, u.last_name FROM orders o LEFT JOIN users u ON o.user_id=u.id ORDER BY o.created_at DESC");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Manage Orders - Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#0f1724;color:#e6eef6;font-family:Poppins, sans-serif}.card{background:#0b1220;border-radius:10px;padding:16px}</style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Orders</h3>
    <a href="admin_dashboard.php" class="btn btn-light">Back</a>
  </div>
  <div class="card">
    <div class="table-responsive">
      <table class="table table-dark table-striped align-middle">
        <thead>
          <tr><th>#</th><th>Customer</th><th>Total</th><th>Status</th><th>Delivery Date</th><th>Updated</th><th>Action</th></tr>
        </thead>
        <tbody>
        <?php while($o = mysqli_fetch_assoc($orders)): ?>
          <tr>
            <td><?= $o['id'] ?></td>
            <td><?= htmlspecialchars($o['first_name'].' '.$o['last_name']) ?></td>
            <td>₱ <?= number_format($o['total'],2) ?></td>
            <td><?= htmlspecialchars($o['status']) ?></td>
            <td><?= htmlspecialchars($o['delivery_date'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($o['updated_at'] ?? $o['created_at']) ?></td>
            <td>
              <form method="post" class="d-flex gap-1">
                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                <select name="status" class="form-select form-select-sm">
                  <option <?= $o['status']=='Pending'?'selected':'' ?>>Pending</option>
                  <option <?= $o['status']=='Processing'?'selected':'' ?>>Processing</option>
                  <option <?= $o['status']=='Shipped'?'selected':'' ?>>Shipped</option>
                  <option <?= $o['status']=='Delivered'?'selected':'' ?>>Delivered</option>
                  <option <?= $o['status']=='Cancelled'?'selected':'' ?>>Cancelled</option>
                </select>
                <input type="date" name="delivery_date" class="form-control form-control-sm" value="<?= htmlspecialchars($o['delivery_date'] ?? '') ?>">
                <button class="btn btn-primary btn-sm">Update</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>

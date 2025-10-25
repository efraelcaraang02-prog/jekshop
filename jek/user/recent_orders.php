<?php
session_start();
include('../db_connect.php');

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Fetch recent order items
$recent_orders_query = mysqli_query($conn, "
    SELECT o.id as order_id, p.name as product_name, o.status, o.created_at
    FROM orders o
    JOIN order_items oi ON o.id=oi.order_id
    JOIN products p ON oi.product_id=p.id
    WHERE o.user_id=$user_id
    ORDER BY o.created_at DESC LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recent Orders - Caraang Aluminum Shop</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
body { font-family:'Poppins', sans-serif; background:#f0f0f0; }
.navbar { background:#000; }
.navbar-brand, .navbar-nav .nav-link { color:#fff !important; }
.navbar-nav .nav-link:hover { color:#ccc !important; }
.card { border-radius:12px; background:#fff; padding:20px; }
.footer { background:#000; color:#fff; padding:20px 0; text-align:center; margin-top:20px; }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
<div class="container">
    <a class="navbar-brand" href="home.php"><i class="fa-solid fa-shop"></i> Caraang Aluminum Shop</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fa-solid fa-user"></i> My Profile</a></li>
            <li class="nav-item"><a class="nav-link" href="cart.php"><i class="fa-solid fa-cart-shopping"></i> My Cart</a></li>
        </ul>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>
</div>
</nav>

<div class="container my-5">
    <h3>Recent Orders</h3>
    <div class="card">
        <?php if(mysqli_num_rows($recent_orders_query)>0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Product</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while($ro = mysqli_fetch_assoc($recent_orders_query)): ?>
                <tr>
                    <td><?= $ro['order_id'] ?></td>
                    <td><?= $ro['product_name'] ?></td>
                    <td><?= $ro['status'] ?></td>
                    <td><?= $ro['created_at'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No recent orders found. <a href="shop.php">Shop now!</a></p>
        <?php endif; ?>
    </div>
</div>

<footer class="footer">
    &copy; <?= date('Y') ?> Caraang Aluminum Shop. All rights reserved.
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

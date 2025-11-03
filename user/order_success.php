<?php
session_start();
include(__DIR__ . '/../includes/db_connect.php');

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Get order ID from URL
if(!isset($_GET['order_id'])){
    header("Location: shop.php");
    exit;
}

$order_id = intval($_GET['order_id']);

// Fetch order items
$order_items_query = mysqli_query($conn, "
    SELECT products.name, products.price, order_items.quantity
    FROM order_items
    JOIN products ON order_items.product_id = products.id
    WHERE order_items.order_id = $order_id
");

$total = 0;
while($item = mysqli_fetch_assoc($order_items_query)){
    $total += $item['price'] * $item['quantity'];
}
mysqli_data_seek($order_items_query, 0); // Reset pointer for display

// Cart count
$cart_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM cart WHERE user_id=$user_id"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Success - Caraang Aluminum Shop</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
body { font-family:'Poppins', sans-serif; background:#f8f8f8; }
.navbar { background:#000; }
.navbar-brand, .navbar-nav .nav-link { color:#fff !important; }
.navbar-nav .nav-link:hover { color:#ccc !important; }
.card { border-radius:12px; margin-top:20px; }
.footer { background:#000; color:#fff; padding:20px 0; text-align:center; margin-top:40px; }
.btn-shop { background:#000; color:#fff; }
.btn-shop:hover { background:#444; color:#fff; }
.badge { font-size:0.7rem; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
<div class="container">
    <a class="navbar-brand" href="home.php"><i class="fa-solid fa-shop"></i> Caraang Aluminum Shop</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link" href="home.php"><i class="fa-solid fa-house"></i> Home</a></li>
            <li class="nav-item"><a class="nav-link" href="shop.php"><i class="fa-solid fa-bag-shopping"></i> Shop</a></li>
        </ul>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item position-relative">
                <a class="nav-link" href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Cart
                    <?php if($cart_count>0): ?><span class="badge bg-danger rounded-circle px-2"><?= $cart_count ?></span><?php endif; ?>
                </a>
            </li>
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fa-solid fa-user"></i> <?= $user_name ?></a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>
</div>
</nav>

<!-- Order Success Section -->
<div class="container my-5">
    <div class="card p-4">
        <h2 class="text-center mb-4 text-success"><i class="fa-solid fa-circle-check"></i> Order Placed Successfully!</h2>
        <p class="text-center mb-4">Your order ID is <strong>#<?= $order_id ?></strong>.</p>

        <?php if(mysqli_num_rows($order_items_query) > 0): ?>
        <div class="table-responsive mb-4">
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
                    <?php while($item = mysqli_fetch_assoc($order_items_query)): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td>₱ <?= number_format($item['price'],2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>₱ <?= number_format($item['price'] * $item['quantity'],2) ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Total:</td>
                        <td class="fw-bold">₱ <?= number_format($total,2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <div class="text-center">
            <a href="shop.php" class="btn btn-shop"><i class="fa-solid fa-bag-shopping"></i> Continue Shopping</a>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    &copy; <?= date('Y') ?> Caraang Aluminum Shop. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

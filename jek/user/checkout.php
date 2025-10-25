<?php
session_start();
include('../db_connect.php');

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Fetch cart items
$cart_items = mysqli_query($conn, "
    SELECT cart.id as cart_id, products.id as product_id, products.name, products.price, cart.quantity
    FROM cart
    JOIN products ON cart.product_id = products.id
    WHERE cart.user_id=$user_id
");

// Calculate total
$total = 0;
while($item = mysqli_fetch_assoc($cart_items)){
    $total += $item['price'] * $item['quantity'];
}
mysqli_data_seek($cart_items, 0); // Reset pointer for display

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // Insert order
    mysqli_query($conn, "INSERT INTO orders (user_id, total) VALUES ($user_id, $total)");
    $order_id = mysqli_insert_id($conn);

    // Insert order items
    while($item = mysqli_fetch_assoc($cart_items)){
        mysqli_query($conn, "
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES ($order_id, ".$item['product_id'].", ".$item['quantity'].", ".$item['price'].")
        ");
    }

    // Clear user cart
    mysqli_query($conn, "DELETE FROM cart WHERE user_id=$user_id");

    header("Location: order_success.php?order_id=$order_id");
    exit;
}

// Cart count
$cart_count = mysqli_num_rows($cart_items);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout - Caraang Aluminum Shop</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
body { font-family:'Poppins', sans-serif; background:#f8f8f8; }
.navbar { background:#000; }
.navbar-brand, .navbar-nav .nav-link { color:#fff !important; }
.navbar-nav .nav-link:hover { color:#ccc !important; }
.card { border-radius:12px; }
.footer { background:#000; color:#fff; padding:20px 0; text-align:center; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
<div class="container">
    <a class="navbar-brand" href="home.php"><i class="fa-solid fa-shop"></i> Caraang Aluminum Shop</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
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

<!-- Checkout Section -->
<section class="container my-5">
    <h2 class="mb-4">Checkout</h2>

    <?php if(mysqli_num_rows($cart_items) > 0): ?>
    <form method="POST">
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
                    <?php while($item = mysqli_fetch_assoc($cart_items)): ?>
                    <tr>
                        <td><?= $item['name'] ?></td>
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

        <button type="submit" class="btn btn-dark float-end"><i class="fa-solid fa-credit-card"></i> Confirm Order</button>
    </form>
    <?php else: ?>
        <p class="text-center">Your cart is empty. <a href="shop.php">Shop now!</a></p>
    <?php endif; ?>
</section>

<!-- Footer -->
<footer class="footer">
    &copy; <?= date('Y') ?> Caraang Aluminum Shop. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
include('../db_connect.php');

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Fetch cart items including product image
$cart_items = mysqli_query($conn, "
    SELECT c.id as cart_id, p.name, p.price, p.image, c.quantity
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id=$user_id
");

$total = 0;
while($item = mysqli_fetch_assoc($cart_items)){
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Cart - Caraang Aluminum Shop</title>
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

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
<div class="container">
    <a class="navbar-brand" href="home.php"><i class="fa-solid fa-shop"></i> Caraang Aluminum Shop</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fa-solid fa-user"></i> My Profile</a></li>
            <li class="nav-item"><a class="nav-link active" href="cart.php"><i class="fa-solid fa-cart-shopping"></i> My Cart</a></li>
        </ul>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>
</div>
</nav>

<div class="container my-5">
    <h3>My Cart</h3>
    <div class="card">
        <?php
        $cart_items = mysqli_query($conn, "
            SELECT c.id as cart_id, p.name, p.price, p.image, c.quantity
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id=$user_id
        ");
        if(mysqli_num_rows($cart_items)>0):
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
                <?php $total=0; while($item = mysqli_fetch_assoc($cart_items)):
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                    // Product image path
                    $image_path = 'images/' . $item['image'];
                    if(empty($item['image']) || !file_exists($image_path)){
                        $image_path = 'images/default.jpg';
                    }
                ?>
                <tr>
                    <td>
                        <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width:50px; height:50px; object-fit:cover; border-radius:4px; margin-right:8px;">
                        <?= htmlspecialchars($item['name']) ?>
                    </td>
                    <td>₱ <?= number_format($item['price'],2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>₱ <?= number_format($subtotal,2) ?></td>
                </tr>
                <?php endwhile; ?>
                <tr>
                    <td colspan="3" class="text-end fw-bold">Total:</td>
                    <td class="fw-bold">₱ <?= number_format($total,2) ?></td>
                </tr>
            </tbody>
        </table>
        </div>
        <a href="checkout.php" class="btn btn-dark"><i class="fa-solid fa-credit-card"></i> Proceed to Checkout</a>
        <?php else: ?>
            <p>Your cart is empty. <a href="shop.php">Shop now!</a></p>
        <?php endif; ?>
    </div>
</div>

<footer class="footer">
    &copy; <?= date('Y') ?> Caraang Aluminum Shop. All rights reserved.
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
include('../db_connect.php');

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Get product ID
if(!isset($_GET['id'])){
    header("Location: shop.php");
    exit;
}

$product_id = intval($_GET['id']);

// Fetch product info
$product_query = mysqli_query($conn, "SELECT * FROM products WHERE id=$product_id");
if(mysqli_num_rows($product_query) == 0){
    echo "Product not found.";
    exit;
}

$product = mysqli_fetch_assoc($product_query);

// Cart count
$cart_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM cart WHERE user_id=$user_id"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $product['name'] ?> - Caraang Aluminum Shop</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
body { font-family:'Poppins', sans-serif; background:#f0f0f0; }
.navbar { background:#000; }
.navbar-brand, .navbar-nav .nav-link { color:#fff !important; }
.navbar-nav .nav-link:hover { color:#ccc !important; }
.card-product { border-radius:12px; background:#fff; padding:20px; margin-top:20px; }
.card-product img { max-width:100%; border-radius:12px; }
.btn-cart { background:#000; color:#fff; }
.btn-cart:hover { background:#444; color:#fff; }
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
            <li class="nav-item"><a class="nav-link" href="shop.php"><i class="fa-solid fa-bag-shopping"></i> Shop</a></li>
        </ul>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item position-relative">
                <a class="nav-link" href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Cart
                    <?php if($cart_count>0): ?><span class="badge rounded-circle bg-danger position-absolute top-0 start-100 translate-middle px-2"><?= $cart_count ?></span><?php endif; ?>
                </a>
            </li>
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fa-solid fa-user"></i> <?= $user_name ?></a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>
</div>
</nav>

<!-- Product Detail -->
<div class="container my-5">
    <div class="card card-product">
        <div class="row">
            <div class="col-md-6">
                <img src="images/<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
            </div>
            <div class="col-md-6">
                <h3><?= $product['name'] ?></h3>
                <p class="fw-bold fs-4">₱ <?= number_format($product['price'],2) ?></p>
                <p><?= $product['description'] ?></p>
                <a href="add_to_cart.php?id=<?= $product['id'] ?>" class="btn btn-cart"><i class="fa-solid fa-cart-plus"></i> Add to Cart</a>
                <a href="shop.php" class="btn btn-outline-dark"><i class="fa-solid fa-arrow-left"></i> Back to Shop</a>
            </div>
        </div>
    </div>
</div>

<footer class="footer">
    &copy; <?= date('Y') ?> Caraang Aluminum Shop. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
include(__DIR__ . '/../includes/db_connect.php'); // Make sure this exists

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Fetch products
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id ASC");

// Count cart items
$cart_count = 0;
$cart_result = mysqli_query($conn, "SELECT SUM(quantity) as cart_count FROM cart WHERE user_id=$user_id");
if($cart_result){
    $cart_count = mysqli_fetch_assoc($cart_result)['cart_count'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Caraang Aluminum Shop</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
body { font-family:'Poppins', sans-serif; background:#f8f8f8; }
.navbar { background:#000; }
.navbar-brand, .navbar-nav .nav-link { color:#fff !important; }
.navbar-nav .nav-link:hover { color:#ccc !important; }
.nav-link i { font-size:18px; }
.badge-cart { background:red; color:#fff; border-radius:50%; padding:3px 7px; font-size:0.9em; position:absolute; top:-8px; right:-10px; }
.card { border-radius:12px; transition:0.3s; display:flex; flex-direction:column; }
.card:hover { transform:translateY(-5px); box-shadow:0 8px 20px rgba(0,0,0,0.2); }
.card-img-top { height:200px; object-fit:cover; border-top-left-radius:12px; border-top-right-radius:12px; }
.card-body { flex-grow:1; display:flex; flex-direction:column; justify-content:space-between; }
.card-title { font-size:1rem; margin-bottom:0.5rem; }
.card-text { margin-bottom:0.5rem; }
section { padding:60px 0; }
.hero { background:#000 url('../images/hero-banner.jpg') center/cover no-repeat; color:#fff; text-align:center; padding:120px 0; }
.hero h1 { font-size:3rem; font-weight:bold; }
.hero p { font-size:1.2rem; margin:20px 0; }
.footer { background:#000; color:#fff; padding:20px 0; text-align:center; }
.btn-add-cart { margin-top:auto; }

/* Navbar layout adjustments like in shop.php */
.navbar-nav.center-icons {
    margin: 0 auto;
    display: flex;
    justify-content: center;
    gap: 25px;
}

.navbar-nav.right-icons {
    display: flex;
    align-items: center;
    gap: 15px;
}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
<div class="container">
    <!-- Left: Brand -->
    <a class="navbar-brand" href="home.php"><i class="fa-solid fa-shop"></i> Caraang Aluminum Shop</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
        <!-- Center Icons -->
        <ul class="navbar-nav center-icons">
            <li class="nav-item"><a class="nav-link active" href="home.php" title="Home"><i class="fa-solid fa-house"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="shop.php" title="Shop"><i class="fa-solid fa-bag-shopping"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="#" title="About"><i class="fa-solid fa-info-circle"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="#" title="Contact"><i class="fa-solid fa-phone"></i></a></li>
            <li class="nav-item position-relative">
                <a class="nav-link" href="cart.php" title="Cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <?php if($cart_count>0): ?><span class="badge-cart"><?= $cart_count ?></span><?php endif; ?>
                </a>
            </li>
        </ul>

        <!-- Right: User/Login -->
        <ul class="navbar-nav right-icons ms-auto">
            <?php if(isset($_SESSION['user_id'])): ?>
            <li class="nav-item"><a class="nav-link" href="dashboard.php" title="<?= htmlspecialchars($user_name) ?>"><i class="fa-solid fa-user"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a></li>
            <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php" title="Login"><i class="fa-solid fa-right-to-bracket"></i></a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>
</nav>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Caraang Aluminum Shop</h1>
        <p>Premium aluminum windows, doors, and accessories</p>
        <a href="shop.php" class="btn btn-light btn-lg"><i class="fa-solid fa-bag-shopping"></i> Shop Now</a>
    </div>
</section>

<!-- Featured Products -->
<section>
    <div class="container">
        <h2 class="text-center mb-5">Featured Products</h2>
        <div class="row g-4">
            <?php while($product = mysqli_fetch_assoc($products)):
                $image_path = 'images/' . $product['image'];
                if(empty($product['image']) || !file_exists($image_path)){
                    $image_path = 'images/default.jpg';
                }
            ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card h-100 text-center">
                    <img src="<?= htmlspecialchars($image_path) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="card-text fw-bold">₱ <?= number_format($product['price'],2) ?></p>
                        <a href="shop.php" class="btn btn-dark btn-add-cart mt-auto"><i class="fa-solid fa-cart-plus"></i> Add to Cart</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="bg-light text-center">
    <div class="container">
        <h2>About Us</h2>
        <p>Caraang Aluminum Shop provides premium aluminum products for residential and commercial spaces. Quality and elegance guaranteed.</p>
    </div>
</section>

<!-- Contact Section -->
<section>
    <div class="container text-center">
        <h2>Contact Us</h2>
        <p>Phone: 0949-804-8699 | Email: efraelcaraang2@gmail.com | Address: San Luis, Apayao, Philippines</p>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    &copy; <?= date('Y') ?> Caraang Aluminum Shop. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

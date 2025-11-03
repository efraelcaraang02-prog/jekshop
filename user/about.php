<?php
session_start();
include(__DIR__ . '/../includes/db_connect.php'); // ✅ fixed path

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_name = $_SESSION['user_name'];
$cart_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM cart WHERE user_id={$_SESSION['user_id']}"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About - Caraang Aluminum Shop</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
body { font-family:'Poppins',sans-serif; background:#f4f4f4; }
.navbar { background:#000; }
.navbar-brand, .navbar-nav .nav-link { color:#fff !important; }
.navbar-nav .nav-link:hover { color:#ccc !important; }
.nav-link i { font-size:18px; }
.footer { background:#000; color:#fff; padding:20px 0; text-align:center; margin-top:40px; }
.about-section { background:#fff; border-radius:15px; padding:40px; box-shadow:0 4px 10px rgba(0,0,0,0.1); }

/* Navbar layout for consistency */
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

.badge {
    font-size: 0.7rem;
    position: absolute;
    top: -5px;
    right: -10px;
}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
<div class="container">
    <!-- Left: Shop name -->
    <a class="navbar-brand" href="home.php"><i class="fa-solid fa-shop"></i> Caraang Aluminum Shop</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
        <!-- Center icons -->
        <ul class="navbar-nav center-icons">
            <li class="nav-item"><a class="nav-link" href="home.php" title="Home"><i class="fa-solid fa-house"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="shop.php" title="Shop"><i class="fa-solid fa-bag-shopping"></i></a></li>
            <li class="nav-item"><a class="nav-link active" href="about.php" title="About"><i class="fa-solid fa-info-circle"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="contact.php" title="Contact"><i class="fa-solid fa-envelope"></i></a></li>
            <li class="nav-item position-relative">
                <a class="nav-link" href="cart.php" title="Cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <?php if($cart_count>0): ?><span class="badge bg-danger"><?= $cart_count ?></span><?php endif; ?>
                </a>
            </li>
        </ul>

        <!-- Right: Profile and Logout -->
        <ul class="navbar-nav right-icons ms-auto">
            <li class="nav-item"><a class="nav-link" href="dashboard.php" title="<?= htmlspecialchars($user_name) ?>"><i class="fa-solid fa-user"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a></li>
        </ul>
    </div>
</div>
</nav>

<!-- About Section -->
<div class="container my-5">
    <div class="about-section">
        <h2 class="text-center mb-4">About Caraang Aluminum Shop</h2>
        <p>
            Caraang Aluminum Shop specializes in providing premium-quality aluminum windows, doors, mirrors, and accessories 
            for both residential and commercial projects. We are committed to offering durable, stylish, and affordable aluminum 
            products to enhance your home or business space.
        </p>
        <p>
            Our mission is to deliver products that combine strength, elegance, and modern design — ensuring that every customer 
            experiences satisfaction with every purchase.
        </p>
        <p class="fw-bold text-center mt-3">✨ Quality. Durability. Elegance. ✨</p>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    &copy; <?= date('Y') ?> Caraang Aluminum Shop. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

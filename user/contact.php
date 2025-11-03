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
<title>Contact - Caraang Aluminum Shop</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
body { font-family:'Poppins',sans-serif; background:#f4f4f4; }
.navbar { background:#000; }
.navbar-brand, .navbar-nav .nav-link { color:#fff !important; }
.navbar-nav .nav-link:hover { color:#ccc !important; }
.nav-link i { font-size:18px; }
.footer { background:#000; color:#fff; padding:20px 0; text-align:center; margin-top:40px; }
.contact-box { background:#fff; border-radius:15px; padding:40px; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
.form-control:focus { box-shadow:none; border-color:#000; }
.btn-send { background:#000; color:#fff; }
.btn-send:hover { background:#444; color:#fff; }

/* Center icon layout */
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
            <li class="nav-item"><a class="nav-link" href="about.php" title="About"><i class="fa-solid fa-info-circle"></i></a></li>
            <li class="nav-item"><a class="nav-link active" href="contact.php" title="Contact"><i class="fa-solid fa-envelope"></i></a></li>
            <li class="nav-item position-relative">
                <a class="nav-link" href="cart.php" title="Cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <?php if($cart_count>0): ?><span class="badge bg-danger"><?= $cart_count ?></span><?php endif; ?>
                </a>
            </li>
        </ul>

        <!-- Right icons -->
        <ul class="navbar-nav right-icons ms-auto">
            <li class="nav-item"><a class="nav-link" href="dashboard.php" title="<?= htmlspecialchars($user_name) ?>"><i class="fa-solid fa-user"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a></li>
        </ul>
    </div>
</div>
</nav>

<!-- Contact Section -->
<div class="container my-5">
    <div class="contact-box">
        <h2 class="text-center mb-4">Contact Us</h2>
        <p class="text-center mb-4">We’d love to hear from you! Please reach out using the form below or our contact details.</p>
        <div class="row">
            <div class="col-md-6">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" placeholder="Enter your name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" placeholder="Enter your email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" rows="5" placeholder="Write your message here..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-send w-100"><i class="fa-solid fa-paper-plane"></i> Send Message</button>
                </form>
            </div>
            <div class="col-md-6">
                <h5><i class="fa-solid fa-phone"></i> Phone</h5>
                <p>0949-804-8699</p>
                <h5><i class="fa-solid fa-envelope"></i> Email</h5>
                <p>efraelcaraang2@gmail.com</p>
                <h5><i class="fa-solid fa-location-dot"></i> Address</h5>
                <p>San Luis, Apayao, Philippines</p>

<?php
session_start();
include('../db_connect.php');

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Cart count
$cart_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM cart WHERE user_id=$user_id"));

// Handle feedback submission
$msg = '';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $feedback = mysqli_real_escape_string($conn, $_POST['feedback']);
    if(!empty($feedback)){
        mysqli_query($conn, "INSERT INTO feedback (user_id, feedback, created_at) VALUES ($user_id, '$feedback', NOW())");
        $msg = "Thank you for your feedback!";
    } else {
        $msg = "Please write something before submitting.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Feedback - Caraang Aluminum Shop</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
body { font-family:'Poppins',sans-serif; background:#f4f4f4; }
.navbar { background:#000; }
.navbar-brand, .navbar-nav .nav-link { color:#fff !important; }
.navbar-nav .nav-link:hover { color:#ccc !important; }
.footer { background:#000; color:#fff; padding:20px 0; text-align:center; margin-top:40px; }
.feedback-box { background:#fff; border-radius:15px; padding:40px; box-shadow:0 4px 10px rgba(0,0,0,0.1); max-width:700px; margin:auto; }
.btn-submit { background:#000; color:#fff; }
.btn-submit:hover { background:#444; color:#fff; }
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
    <a class="navbar-brand" href="home.php"><i class="fa-solid fa-shop"></i> Caraang Aluminum Shop</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link" href="home.php"><i class="fa-solid fa-house"></i> Home</a></li>
            <li class="nav-item"><a class="nav-link" href="shop.php"><i class="fa-solid fa-bag-shopping"></i> Shop</a></li>
            <li class="nav-item"><a class="nav-link active" href="feedback.php"><i class="fa-solid fa-comment-dots"></i> Feedback</a></li>
        </ul>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item position-relative">
                <a class="nav-link" href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Cart
                    <?php if($cart_count>0): ?><span class="badge bg-danger rounded-circle px-2"><?= $cart_count ?></span><?php endif; ?>
                </a>
            </li>
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fa-solid fa-user"></i> <?= htmlspecialchars($user_name) ?></a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>
</div>
</nav>

<!-- Feedback Section -->
<div class="container my-5">
    <div class="feedback-box">
        <h2 class="text-center mb-4">We Value Your Feedback</h2>
        <?php if($msg): ?>
            <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <textarea name="feedback" class="form-control" rows="6" placeholder="Write your feedback here..." required></textarea>
            </div>
            <button type="submit" class="btn btn-submit w-100"><i class="fa-solid fa-paper-plane"></i> Submit Feedback</button>
        </form>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    &copy; <?= date('Y') ?> Caraang Aluminum Shop. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

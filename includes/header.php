<?php
if (session_status() == PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Caraang Aluminum Shop</title>
  <link rel="stylesheet" href="/caraang_aluminum_shop/assets/css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container">
    <h1><a href="/caraang_aluminum_shop/user/index.php">Caraang Aluminum Shop</a></h1>
    <nav>
      <?php if(isset($_SESSION['user_id'])): ?>
        <a href="/caraang_aluminum_shop/user/cart.php">Cart</a>
        <a href="/caraang_aluminum_shop/user/orders.php">My Orders</a>
        <a href="/caraang_aluminum_shop/user/logout.php">Logout</a>
      <?php else: ?>
        <a href="/caraang_aluminum_shop/user/login.php">Login</a>
        <a href="/caraang_aluminum_shop/user/signup.php">Sign Up</a>
      <?php endif; ?>
      <a href="/caraang_aluminum_shop/admin/admin_login.php">Admin</a>
    </nav>
  </div>
</header>
<main class="container">

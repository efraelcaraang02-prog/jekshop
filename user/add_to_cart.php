<?php
session_start();

// ✅ Correct the include path
include(__DIR__ . '/../includes/db_connect.php');

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get product ID
if(!isset($_GET['id'])){
    header("Location: shop.php");
    exit;
}

$product_id = intval($_GET['id']);

// Check if product already in cart
$check_cart = mysqli_query($conn, "SELECT * FROM cart WHERE user_id=$user_id AND product_id=$product_id");
if(mysqli_num_rows($check_cart) > 0){
    // Already in cart, maybe increase quantity
    mysqli_query($conn, "UPDATE cart SET quantity = quantity + 1 WHERE user_id=$user_id AND product_id=$product_id");
} else {
    // Add new product to cart
    mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, 1)");
}

// Redirect back to shop or product page
header("Location: shop.php");
exit;

<?php
session_start();
include('../db_connect.php');

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: shop.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_GET['id']);

// Check if product is already in cart
$check = mysqli_query($conn, "SELECT * FROM cart WHERE user_id=$user_id AND product_id=$product_id");

if(mysqli_num_rows($check) > 0){
    // Increase quantity
    mysqli_query($conn, "UPDATE cart SET quantity = quantity + 1 WHERE user_id=$user_id AND product_id=$product_id");
}else{
    // Add new item
    mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, 1)");
}

header("Location: cart.php");
exit;

<?php
session_start();
include('../db_connect.php');

if(!isset($_SESSION['user_id']) || !isset($_POST['cart_id'], $_POST['quantity'])){
    header("Location: cart.php");
    exit;
}

$cart_id = intval($_POST['cart_id']);
$quantity = max(1, intval($_POST['quantity']));

mysqli_query($conn, "UPDATE cart SET quantity=$quantity WHERE id=$cart_id AND user_id=".$_SESSION['user_id']);
header("Location: cart.php");
exit;

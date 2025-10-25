<?php
session_start();
include('../db_connect.php');

if(!isset($_SESSION['user_id']) || !isset($_POST['cart_id'])){
    header("Location: cart.php");
    exit;
}

$cart_id = intval($_POST['cart_id']);
mysqli_query($conn, "DELETE FROM cart WHERE id=$cart_id AND user_id=".$_SESSION['user_id']);
header("Location: cart.php");
exit;

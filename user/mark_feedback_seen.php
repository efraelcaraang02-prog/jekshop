<?php
session_start();
include('../includes/db_connect.php');

if(!isset($_SESSION['user_id'])) exit;

$user_id = $_SESSION['user_id'];
mysqli_query($conn, "UPDATE feedback SET seen=1 WHERE user_id=$user_id AND seen=0");
?>

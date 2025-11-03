<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "efrael_db"; // ✅ correct database

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

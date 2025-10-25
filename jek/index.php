<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome - Caraang Aluminum Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light text-center p-5">
    <h1>Welcome, <?php echo $_SESSION['first_name']; ?>!</h1>
    <p>You have successfully logged in to <strong>Caraang Aluminum Shop</strong>.</p>
    <a href="logout.php" class="btn btn-danger">Logout</a>
</body>
</html>

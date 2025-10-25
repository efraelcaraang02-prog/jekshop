<?php
session_start();
include('../db_connect.php');

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
$user = mysqli_fetch_assoc($user_query);

// Determine which section to display
$view = isset($_GET['view']) ? $_GET['view'] : 'profile';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Caraang Aluminum Shop</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
body { font-family:'Poppins', sans-serif; background:#f0f0f0; }
.navbar { background:#000; color:#fff; }
.navbar a { color:#fff; text-decoration:none; margin-right:20px; }
.sidebar { height:100vh; background:#111; color:#fff; padding-top:20px; position:fixed; width:220px; }
.sidebar a { display:block; color:#fff; padding:12px 20px; text-decoration:none; }
.sidebar a.active, .sidebar a:hover { background:#444; color:#fff; border-radius:8px; }
.content { margin-left:240px; padding:20px; }
.card { border-radius:12px; background:#fff; padding:20px; }
.footer { background:#000; color:#fff; padding:20px 0; text-align:center; margin-top:20px; margin-left:240px; }
.status-badge { padding:6px 10px; border-radius:8px; color:#fff; font-weight:bold; }
.status-pending { background:#ffc107; }
.status-processing { background:#17a2b8; }
.status-delivered { background:#28a745; }
.status-cancelled { background:#dc3545; }
</style>
</head>
<body>

<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg p-3">
    <div class="container-fluid">
        <a class="navbar-brand" href="shop.php"><i class="fa-solid fa-house"></i> Home</a>
        <span class="ms-auto">Welcome, <?= $user['first_name'] ?> 
            <a href="logout.php" class="btn btn-danger btn-sm ms-3">Logout</a>
        </span>
    </div>
</nav>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center mb-4">Caraang Aluminum</h4>
    <a class="<?= $view=='profile'?'active':'' ?>" href="dashboard.php?view=profile"><i class="fa-solid fa-user"></i> Profile</a>
    <a class="<?= $view=='orders'?'active':'' ?>" href="dashboard.php?view=orders"><i class="fa-solid fa-list"></i> My Orders</a>
    <a class="<?= $view=='cart'?'active':'' ?>" href="dashboard.php?view=cart"><i class="fa-solid fa-cart-shopping"></i> Cart</a>
    <a class="<?= $view=='payments'?'active':'' ?>" href="dashboard.php?view=payments"><i class="fa-solid fa-credit-card"></i> Payment Status</a>
    <a class="<?= $view=='recent'?'active':'' ?>" href="dashboard.php?view=recent"><i class="fa-solid fa-clock-rotate-left"></i> Recent Orders</a>
    <a class="<?= $view=='tracking'?'active':'' ?>" href="dashboard.php?view=tracking"><i class="fa-solid fa-truck-fast"></i> Tracking Status</a>
</div>

<!-- Content -->
<div class="content">
    <div class="card">
        <?php
        switch($view){
            case 'profile':
                echo "<h3>My Profile</h3>
                <p><strong>First Name:</strong> {$user['first_name']}</p>
                <p><strong>Middle Name:</strong> {$user['middle_name']}</p>
                <p><strong>Last Name:</strong> {$user['last_name']}</p>
                <p><strong>Birthday:</strong> {$user['birthday']}</p>
                <p><strong>Age:</strong> {$user['age']}</p>
                <p><strong>Sex:</strong> {$user['sex']}</p>
                <p><strong>Region:</strong> {$user['region']}</p>
                <p><strong>Province:</strong> {$user['province']}</p>
                <p><strong>Municipality:</strong> {$user['municipality']}</p>
                <p><strong>Barangay:</strong> {$user['barangay']}</p>
                <p><strong>Email:</strong> {$user['email']}</p>";
                break;

            case 'orders':
                echo "<h3>My Orders</h3>";
                $orders_query = mysqli_query($conn, "SELECT * FROM orders WHERE user_id=$user_id ORDER BY created_at DESC");
                if(mysqli_num_rows($orders_query)>0){
                    echo "<table class='table table-bordered'>
                    <thead><tr><th>Order #</th><th>Total</th><th>Status</th><th>Date</th></tr></thead><tbody>";
                    while($order = mysqli_fetch_assoc($orders_query)){
                        echo "<tr>
                        <td>{$order['id']}</td>
                        <td>₱ ".number_format($order['total'],2)."</td>
                        <td>{$order['status']}</td>
                        <td>{$order['created_at']}</td>
                        </tr>";
                    }
                    echo "</tbody></table>";
                } else { echo "<p>No orders yet.</p>"; }
                break;

            case 'cart':
                echo "<h3>My Cart</h3>";
                $cart_query = mysqli_query($conn, "SELECT c.*, p.name, p.price FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=$user_id");
                if(mysqli_num_rows($cart_query)>0){
                    echo "<table class='table table-bordered'>
                    <thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th></tr></thead><tbody>";
                    while($item = mysqli_fetch_assoc($cart_query)){
                        $total = $item['price']*$item['quantity'];
                        echo "<tr>
                        <td>{$item['name']}</td>
                        <td>₱ ".number_format($item['price'],2)."</td>
                        <td>{$item['quantity']}</td>
                        <td>₱ ".number_format($total,2)."</td>
                        </tr>";
                    }
                    echo "</tbody></table>";
                } else { echo "<p>Cart is empty.</p>"; }
                break;

            case 'payments':
                echo "<h3>Payment Status</h3>";
                $payment_query = mysqli_query($conn, "SELECT * FROM orders WHERE user_id=$user_id ORDER BY created_at DESC");
                if(mysqli_num_rows($payment_query)>0){
                    echo "<table class='table table-bordered'>
                    <thead><tr><th>Order #</th><th>Total</th><th>Status</th><th>Date</th></tr></thead><tbody>";
                    while($payment = mysqli_fetch_assoc($payment_query)){
                        echo "<tr>
                        <td>{$payment['id']}</td>
                        <td>₱ ".number_format($payment['total'],2)."</td>
                        <td>{$payment['status']}</td>
                        <td>{$payment['created_at']}</td>
                        </tr>";
                    }
                    echo "</tbody></table>";
                } else { echo "<p>No payments yet.</p>"; }
                break;

            case 'recent':
                echo "<h3>Recent Orders</h3>";
                $recent_query = mysqli_query($conn, "SELECT * FROM orders WHERE user_id=$user_id ORDER BY created_at DESC LIMIT 5");
                if(mysqli_num_rows($recent_query)>0){
                    echo "<ul class='list-group'>";
                    while($recent = mysqli_fetch_assoc($recent_query)){
                        echo "<li class='list-group-item'>Order #{$recent['id']} - ₱ ".number_format($recent['total'],2)." - {$recent['status']} - {$recent['created_at']}</li>";
                    }
                    echo "</ul>";
                } else { echo "<p>No recent orders.</p>"; }
                break;

            case 'tracking':
                echo "<h3>Tracking Order Status</h3>";
                $track_query = mysqli_query($conn, "SELECT * FROM orders WHERE user_id=$user_id ORDER BY created_at DESC");
                if(mysqli_num_rows($track_query)>0){
                    echo "<table class='table table-bordered'>
                    <thead><tr><th>Order #</th><th>Status</th><th>Estimated Delivery</th><th>Last Update</th></tr></thead><tbody>";
                    while($track = mysqli_fetch_assoc($track_query)){
                        $status_class = '';
                        switch(strtolower($track['status'])){
                            case 'pending': $status_class='status-pending'; break;
                            case 'processing': $status_class='status-processing'; break;
                            case 'delivered': $status_class='status-delivered'; break;
                            case 'cancelled': $status_class='status-cancelled'; break;
                        }
                        echo "<tr>
                        <td>{$track['id']}</td>
                        <td><span class='status-badge {$status_class}'>{$track['status']}</span></td>
                        <td>".($track['delivery_date'] ?? 'N/A')."</td>
                        <td>{$track['updated_at']}</td>
                        </tr>";
                    }
                    echo "</tbody></table>";
                } else { echo "<p>No tracking information available.</p>"; }
                break;

            default:
                echo "<p>Select an option from the sidebar.</p>";
        }
        ?>
    </div>
</div>

<footer class="footer">
    &copy; <?= date('Y') ?> Caraang Aluminum Shop. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

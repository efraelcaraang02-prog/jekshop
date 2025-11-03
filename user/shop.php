<?php
session_start();
include(__DIR__ . '/../includes/db_connect.php'); // Make sure this exists

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Fetch products
$products_query = mysqli_query($conn, "SELECT * FROM products ORDER BY id ASC");

// Count cart items
$cart_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM cart WHERE user_id=$user_id"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Shop - Caraang Aluminum Shop</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
body { font-family:'Poppins', sans-serif; background:#f0f0f0; }
.navbar { background:#000; }
.navbar-brand, .navbar-nav .nav-link { color:#fff !important; }
.navbar-nav .nav-link:hover { color:#ccc !important; }
.card-product { border-radius:12px; background:#fff; padding:15px; transition:0.3s; display:flex; flex-direction:column; justify-content:space-between; }
.card-product:hover { box-shadow:0px 4px 15px rgba(0,0,0,0.2); }
.card-product img { max-height:180px; object-fit:cover; border-radius:8px; margin-bottom:10px; }
.footer { background:#000; color:#fff; padding:20px 0; text-align:center; margin-top:20px; }
.btn-cart { background:#000; color:#fff; }
.btn-cart:hover { background:#444; color:#fff; }
.nav-link i { font-size:18px; }

/* Center the icons */
.navbar-nav.center-icons {
    margin: 0 auto;
    display: flex;
    justify-content: center;
    gap: 25px;
}

/* Adjust spacing for right section */
.navbar-nav.right-icons {
    display: flex;
    align-items: center;
    gap: 15px;
}

.badge {
    font-size: 0.7rem;
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
        <ul class="navbar-nav center-icons">
            <li class="nav-item"><a class="nav-link" href="home.php" title="Home"><i class="fa-solid fa-house"></i></a></li>
            <li class="nav-item"><a class="nav-link active" href="shop.php" title="Shop"><i class="fa-solid fa-bag-shopping"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="about.php" title="About"><i class="fa-solid fa-info-circle"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="contact.php" title="Contact"><i class="fa-solid fa-envelope"></i></a></li>
            <li class="nav-item position-relative">
                <a class="nav-link" href="cart.php" title="Cart"><i class="fa-solid fa-cart-shopping"></i>
                    <?php if($cart_count>0): ?>
                        <span class="badge rounded-circle bg-danger position-absolute top-0 start-100 translate-middle px-2"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav right-icons ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php" title="<?= htmlspecialchars($user_name) ?>"><i class="fa-solid fa-user"></i></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a>
            </li>
        </ul>
    </div>
</div>
</nav>

<!-- Products Grid -->
<div class="container my-5">
    <h3 class="mb-4">Our Products</h3>
    <div class="row g-4">
        <?php while($product = mysqli_fetch_assoc($products_query)): ?>
        <div class="col-lg-3 col-md-4 col-sm-6 d-flex align-items-stretch">
            <div class="card-product text-center w-100">
                <?php
                    // ✅ FIXED: Proper image path checking
                    $image_path = 'images/' . $product['image'];
                    if(empty($product['image']) || !file_exists(__DIR__ . '/' . $image_path)){
                        $image_path = 'images/default.jpg';
                    }
                ?>
                <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid mb-3">
                <h5 class="mb-2"><?= htmlspecialchars($product['name']) ?></h5>
                <p class="fw-bold mb-3">₱ <?= number_format($product['price'],2) ?></p>
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <a href="view_product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-dark mb-1"><i class="fa-solid fa-eye"></i> View</a>
                    <a href="add_to_cart.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-cart mb-1"><i class="fa-solid fa-cart-plus"></i> Add to Cart</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<footer class="footer">
    &copy; <?= date('Y') ?> Caraang Aluminum Shop. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

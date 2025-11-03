<?php
session_start();

// ✅ Fix db_connect path
include(__DIR__ . '/../includes/db_connect.php');

// ✅ Ensure admin session
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// ✅ Create images folder path
$img_dir = __DIR__ . '/images';
if (!is_dir($img_dir)) {
    mkdir($img_dir, 0777, true);
}

// handle create/update/delete
$actionMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);
        $desc = mysqli_real_escape_string($conn, $_POST['description']);

        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = 'product_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $img_dir . '/' . $image);
        }

        mysqli_query($conn, "INSERT INTO products (name, price, description, stock, image)
            VALUES ('$name',$price,'$desc',$stock," . ($image ? "'$image'" : "NULL") . ")");
        $actionMsg = 'Product added.';
    }

    if (isset($_POST['update'])) {
        $id = intval($_POST['id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);
        $desc = mysqli_real_escape_string($conn, $_POST['description']);
        $imageSql = "";

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = 'product_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $img_dir . '/' . $image);
            $imageSql = ", image='$image'";
        }

        mysqli_query($conn, "UPDATE products SET name='$name', price=$price,
            description='$desc', stock=$stock $imageSql WHERE id=$id");
        $actionMsg = 'Product updated.';
    }

    if (isset($_POST['delete'])) {
        $id = intval($_POST['delete']);
        mysqli_query($conn, "DELETE FROM products WHERE id=$id");
        $actionMsg = 'Product deleted.';
    }

    header("Location: manage_products.php");
    exit;
}

$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Manage Products</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background:#0f1724;
    color:#e6eef6;
    font-family:Poppins, sans-serif;
}
.card {
    background:#0b1220;
    padding:16px;
    border-radius:10px;
}
img.product-thumb {
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:8px;
    border:1px solid #444;
}
</style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Products</h3>
    <a href="dashboard.php" class="btn btn-light">Back</a>
  </div>

  <div class="card mb-3">
    <h5>Add Product</h5>
    <form method="post" enctype="multipart/form-data" class="row g-2">
      <div class="col-md-4"><input name="name" class="form-control" placeholder="Name" required></div>
      <div class="col-md-2"><input name="price" type="number" step="0.01" class="form-control" placeholder="Price" required></div>
      <div class="col-md-2"><input name="stock" type="number" class="form-control" placeholder="Stock" required></div>
      <div class="col-md-4"><input name="image" type="file" class="form-control"></div>
      <div class="col-12"><textarea name="description" class="form-control" placeholder="Description"></textarea></div>
      <div class="col-12"><button name="create" class="btn btn-success">Add Product</button></div>
    </form>
  </div>

  <div class="card">
    <h5>All Products</h5>
    <div class="table-responsive mt-2">
      <table class="table table-dark table-striped align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php while($p = mysqli_fetch_assoc($products)):
            // ✅ FIX: Use full path relative to admin folder for browser
            $img_path = 'images/' . $p['image'];
            if(empty($p['image']) || !file_exists($img_dir . '/' . $p['image'])){
                $img_path = 'images/default.jpg';
            }
        ?>
          <tr>
            <td><?= $p['id'] ?></td>
            <td><img src="<?= htmlspecialchars($img_path) ?>" alt="Product Image" class="product-thumb"></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td>₱ <?= number_format($p['price'],2) ?></td>
            <td><?= $p['stock'] ?></td>
            <td>
              <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $p['id'] ?>">Edit</button>
              <form method="post" class="d-inline" onsubmit="return confirm('Delete product?')">
                <button name="delete" value="<?= $p['id'] ?>" class="btn btn-sm btn-danger">Delete</button>
              </form>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div class="modal fade" id="editModal<?= $p['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content" style="background:#071021;color:#e6eef6">
                <div class="modal-header">
                  <h5 class="modal-title">Edit Product #<?= $p['id'] ?></h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" enctype="multipart/form-data">
                  <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <div class="mb-2"><input name="name" class="form-control" value="<?= htmlspecialchars($p['name']) ?>" required></div>
                    <div class="mb-2"><input name="price" type="number" step="0.01" class="form-control" value="<?= $p['price'] ?>" required></div>
                    <div class="mb-2"><input name="stock" type="number" class="form-control" value="<?= $p['stock'] ?>"></div>
                    <div class="mb-2">
                      <label>Current Image:</label><br>
                      <img src="<?= htmlspecialchars($img_path) ?>" class="product-thumb mb-2"><br>
                      <input name="image" type="file" class="form-control">
                    </div>
                    <div class="mb-2"><textarea name="description" class="form-control"><?= htmlspecialchars($p['description']) ?></textarea></div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" name="update" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
include '../includes/db_connect.php';
include '../includes/header.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$q = mysqli_query($conn, "SELECT * FROM products WHERE id=$id");
$p = mysqli_fetch_assoc($q);
if (!$p) {
  echo "<p>Product not found.</p>";
  include '../includes/footer.php';
  exit;
}
?>
<h2><?php echo htmlspecialchars($p['name']); ?></h2>
<img src="/caraang_aluminum_shop/assets/images/<?php echo htmlspecialchars($p['image']); ?>" style="width:300px;height:200px;object-fit:cover;">
<p><?php echo nl2br(htmlspecialchars($p['description'])); ?></p>
<p>Stock: <?php echo intval($p['stock']); ?></p>
<p>₱ <?php echo number_format($p['price'],2); ?></p>

<form method="post" action="cart.php">
  <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
  <div class="form-group">
    <label>Quantity</label>
    <input type="number" name="qty" value="1" min="1" max="<?php echo intval($p['stock']); ?>">
  </div>
  <button class="btn" type="submit" name="add_to_cart">Add to Cart</button>
</form>

<?php include '../includes/footer.php'; ?>

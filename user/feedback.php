<?php
include '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msg = mysqli_real_escape_string($conn, $_POST['message']);
    $uid = intval($_SESSION['user_id']);
    $oid = !empty($_POST['order_id']) ? intval($_POST['order_id']) : 'NULL';
    mysqli_query($conn, "INSERT INTO feedback (user_id, order_id, message) VALUES ($uid, ".($oid === 'NULL' ? "NULL" : $oid).", '$msg')");
    header("Location: feedback.php");
}
include '../includes/header.php';
?>
<h2>Feedback</h2>
<form method="post">
  <div class="form-group"><label>Order ID (optional)</label><input name="order_id"></div>
  <div class="form-group"><label>Message</label><textarea name="message" required></textarea></div>
  <button class="btn" type="submit">Send Feedback</button>
</form>
<?php include '../includes/footer.php'; ?>

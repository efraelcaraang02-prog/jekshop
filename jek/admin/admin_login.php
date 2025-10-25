<?php
session_start();
include('../db_connect.php');

$err = '';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = trim($_POST['username']);
    $password = $_POST['password'] ?? '';

    if($username === '' || $password === ''){
        $err = "Please enter username and password.";
    } else {
        // Use prepared statement to fetch the admin by username
        $stmt = mysqli_prepare($conn, "SELECT id, username, password, name FROM admins WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if($res && mysqli_num_rows($res) === 1){
            $admin = mysqli_fetch_assoc($res);
            // Compare SHA-256 of entered password with stored password
            if(hash('sha256', $password) === $admin['password']){
                // Login success
                $_SESSION['admin_id'] = $admin['id'];
                // Prefer stored name if available
                $_SESSION['admin_name'] = !empty($admin['name']) ? $admin['name'] : $admin['username'];
                header("Location: admin_dashboard.php");
                exit;
            } else {
                $err = "Incorrect credentials.";
            }
        } else {
            $err = "Incorrect credentials.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Login - Caraang Aluminum</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background:#0f1724; color:#e6eef6; font-family:Poppins, sans-serif; }
.login-box { max-width:420px; margin:8vh auto; background:#0b1220; border-radius:12px; padding:30px; box-shadow:0 6px 24px rgba(0,0,0,0.6); }
.logo { font-weight:700; color:#fff; letter-spacing:0.5px; }
.form-control { background:#0b1226; color:#fff; border:1px solid #222; }
.btn-primary { background:#111827; border-color:#222; color:#fff; }
.small-note { color:#98a0ac; }
</style>
</head>
<body>
<div class="login-box">
  <h3 class="logo mb-3">Caraang Aluminum — Admin</h3>
  <?php if($err): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
  <?php endif; ?>
  <form method="post">
    <div class="mb-3">
      <label class="form-label small-note">Username</label>
      <input name="username" class="form-control" required value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
    </div>
    <div class="mb-3">
      <label class="form-label small-note">Password</label>
      <input name="password" type="password" class="form-control" required>
    </div>
    <div class="d-grid">
      <button class="btn btn-primary">Sign in</button>
    </div>
  </form>
</div>
</body>
</html>

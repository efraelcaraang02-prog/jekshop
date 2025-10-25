<?php
session_start();
include('../db_connect.php');
if(!isset($_SESSION['admin_id'])) header("Location: admin_login.php");

// handle delete / deactivate
if($_SERVER['REQUEST_METHOD']=='POST'){
    if(isset($_POST['delete_user'])){
        $id = intval($_POST['delete_user']);
        mysqli_query($conn, "DELETE FROM users WHERE id=$id");
        header("Location: manage_users.php"); exit;
    }
    if(isset($_POST['deactivate_user'])){
        $id = intval($_POST['deactivate_user']);
        mysqli_query($conn, "UPDATE users SET active=0 WHERE id=$id");
        header("Location: manage_users.php"); exit;
    }
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Manage Users</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#0f1724;color:#e6eef6;font-family:Poppins, sans-serif}.card{background:#0b1220;padding:16px;border-radius:10px}</style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Customers</h3>
    <a href="admin_dashboard.php" class="btn btn-light">Back</a>
  </div>
  <div class="card">
    <div class="table-responsive">
      <table class="table table-dark table-striped">
        <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Region</th><th>Joined</th><th>Action</th></tr></thead>
        <tbody>
        <?php while($u = mysqli_fetch_assoc($users)): ?>
          <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['province'] ?? '-') ?></td>
            <td><?= htmlspecialchars($u['created_at']) ?></td>
            <td>
              <form method="post" onsubmit="return confirm('Delete this user?')">
                <button name="delete_user" value="<?= $u['id'] ?>" class="btn btn-sm btn-danger">Delete</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>

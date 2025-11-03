<?php
session_start();

// ✅ Correct include for db_connect.php inside includes/
include(__DIR__ . '/../includes/db_connect.php');

// ✅ Ensure admin session check
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Delete user
    if (isset($_POST['delete_user'])) {
        $id = intval($_POST['delete_user']);
        mysqli_query($conn, "DELETE FROM users WHERE id=$id");
        header("Location: manage_users.php");
        exit;
    }

    // Update user info
    if (isset($_POST['update_user'])) {
        $id = intval($_POST['update_user']);
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
        $region = mysqli_real_escape_string($conn, $_POST['region']);
        $province = mysqli_real_escape_string($conn, $_POST['province']);
        $municipality = mysqli_real_escape_string($conn, $_POST['municipality']);
        $barangay = mysqli_real_escape_string($conn, $_POST['barangay']);

        $sql = "UPDATE users SET 
                first_name='$first_name',
                last_name='$last_name',
                email='$email',
                region='$region',
                province='$province',
                municipality='$municipality',
                barangay='$barangay'";
        if ($password) $sql .= ", password='$password'";
        $sql .= " WHERE id=$id";
        mysqli_query($conn, $sql);

        header("Location: manage_users.php?updated=1");
        exit;
    }
}

// Fetch users safely
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
if (!$users) {
    die("Query Error: " . mysqli_error($conn));
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Manage Users</title>
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
.table thead th {
  color: #00b4d8;
}
.modal-content {
  background:#0b1220;
  color:#fff;
  border:1px solid #1e2a40;
}
.modal-header, .modal-footer {
  border-color:#1e2a40;
}
.form-control {
  background-color:#152238;
  color:#fff;
  border:1px solid #2e3b55;
}
.form-control:focus {
  background-color:#1b2a48;
  color:#fff;
}
.btn-close { filter:invert(1); }
</style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Customers</h3>
    <!-- Fixed Back button to always go to dashboard -->
   <a href="dashboard.php" class="btn btn-light">Back</a>
  </div>
  <div class="card">
    <div class="table-responsive">
      <table class="table table-dark table-striped align-middle">
        <thead>
          <tr>
            <th>#</th><th>Name</th><th>Email</th><th>Region</th><th>Province</th><th>Municipality</th><th>Joined</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php while($u = mysqli_fetch_assoc($users)): ?>
          <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['region'] ?? '-') ?></td>
            <td><?= htmlspecialchars($u['province'] ?? '-') ?></td>
            <td><?= htmlspecialchars($u['municipality'] ?? '-') ?></td>
            <td><?= htmlspecialchars($u['created_at']) ?></td>
            <td class="d-flex gap-1">
              <a href="view_purchases.php?user_id=<?= $u['id'] ?>" class="btn btn-sm btn-info">View</a>
              <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $u['id'] ?>">Edit</button>
              <form method="post" onsubmit="return confirm('Delete this user?')" class="d-inline">
                <button name="delete_user" value="<?= $u['id'] ?>" class="btn btn-sm btn-danger">Delete</button>
              </form>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div class="modal fade" id="editModal<?= $u['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Edit Customer Info</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                  <div class="modal-body">
                    <input type="hidden" name="update_user" value="<?= $u['id'] ?>">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($u['first_name']) ?>" required>
                      </div>
                      <div class="col-md-6">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($u['last_name']) ?>" required>
                      </div>
                      <div class="col-md-6">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($u['email']) ?>" required>
                      </div>
                      <div class="col-md-6">
                        <label>Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control">
                      </div>
                      <hr class="text-secondary my-3">
                      <div class="col-md-6">
                        <label>Region</label>
                        <input type="text" name="region" class="form-control" value="<?= htmlspecialchars($u['region']) ?>">
                      </div>
                      <div class="col-md-6">
                        <label>Province</label>
                        <input type="text" name="province" class="form-control" value="<?= htmlspecialchars($u['province']) ?>">
                      </div>
                      <div class="col-md-6">
                        <label>Municipality / City</label>
                        <input type="text" name="municipality" class="form-control" value="<?= htmlspecialchars($u['municipality']) ?>">
                      </div>
                      <div class="col-md-6">
                        <label>Barangay</label>
                        <input type="text" name="barangay" class="form-control" value="<?= htmlspecialchars($u['barangay']) ?>">
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
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

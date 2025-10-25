<?php
include('../db_connect.php');
session_start();

$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "Email not registered!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | Caraang Aluminum Shop</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: #121212;
    font-family: 'Poppins', sans-serif;
    color: #fff;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}
.login-container {
    background: #f9f9f9;
    color: #222;
    border-radius: 15px;
    padding: 40px 30px;
    width: 100%;
    max-width: 400px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    border: 1px solid #ccc;
}
h2 {
    text-align: center;
    color: #111;
    margin-bottom: 25px;
    font-weight: 700;
    font-size: 1.8rem;
}
.form-control {
    border-radius: 8px;
    border: 1px solid #bbb;
    background: #fff;
    color: #111;
    box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);
    transition: 0.3s;
    margin-bottom: 15px;
}
.form-control:focus { box-shadow: inset 0 2px 5px rgba(0,0,0,0.1); }
.btn-aluminum {
    width: 100%;
    border: none;
    border-radius: 8px;
    padding: 10px;
    background: #000;
    color: #fff;
    font-weight: 600;
    transition: 0.3s;
}
.btn-aluminum:hover { background: #333; transform: scale(1.03); }
.text-center a { color: #000; text-decoration: none; }
.text-center a:hover { text-decoration: underline; }
.alert { border-radius: 8px; }
</style>
</head>
<body>

<div class="login-container">
<h2>Caraang Aluminum Shop<br><small>Login</small></h2>

<?php if($error): ?>
<div class="alert alert-danger text-center"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <input type="email" name="email" class="form-control" placeholder="Email Address" required>
    <input type="password" name="password" class="form-control" placeholder="Password" required>
    <button type="submit" class="btn btn-aluminum mt-2">Login</button>
</form>

<div class="text-center mt-3">
    <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
</div>
</div>

</body>
</html>

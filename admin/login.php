<?php
session_start();

require_once '../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("
        SELECT *
        FROM admins
        WHERE username = ?
        LIMIT 1
    ");

    $stmt->execute([$username]);

    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    $passwordIsValid = $admin && (password_verify($password, $admin['password']) || hash_equals($admin['password'], $password));

    if ($passwordIsValid) {

    // Upgrade legacy plaintext credentials after the next successful login.
    if (!password_get_info($admin['password'])['algo']) {
        $pdo->prepare('UPDATE admins SET password = ? WHERE id = ?')->execute([password_hash($password, PASSWORD_DEFAULT), $admin['id']]);
    }

    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_name'] = $admin['fullname'];

    header("Location: dashboard.php");
    exit;

} else {

    $error = "Invalid username or password.";

}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Login | Jyoti Hardware</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>

body{

background:#f5f7fb;

display:flex;

justify-content:center;

align-items:center;

height:100vh;

}

.card{

width:420px;

border:none;

border-radius:20px;

box-shadow:0 15px 40px rgba(0,0,0,.1);

}

.btn-warning{

background:#F59E0B;

border:none;

}

</style>

</head>

<body>

<div class="card">

<div class="card-body p-5">

<h2 class="text-center mb-4">

Admin Login

</h2>

<?php if($error): ?>

<div class="alert alert-danger">

<?= htmlspecialchars($error); ?>

</div>

<?php endif; ?>

<form method="POST">

<div class="mb-3">

<label>Username</label>

<input
type="text"
name="username"
class="form-control"
required>

</div>

<div class="mb-4">

<label>Password</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<button
class="btn btn-warning w-100">

Login

</button>

</form>

</div>

</div>

</body>

</html>

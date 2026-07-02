<?php
require_once 'config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    if (loginUser($email, $password)) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - <?= APP_NAME ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">

    <div class="card shadow p-4" style="width: 360px;">

        <h4 class="text-center mb-3">Fuel Station Login</h4>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">

            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control mb-2" required>

            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control mb-3" required>

            <button class="btn btn-primary w-100">Login</button>

        </form>

    </div>

</div>

</body>
</html>
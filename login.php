<?php
require_once 'config/config.php';

session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {

        $error = "Please enter your email and password.";

    } else {

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {

            if ($user['status'] == 0) {

                $error = "Your account is inactive.";

            } else {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                header("Location: index.php");
                exit;
            }

        } else {

            $error = "Invalid email or password.";

        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>FuelDex - Login</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/flames.png">
    <link rel="shortcut icon" href="assets/images/flames.png">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>

        body{
            margin:0;
            font-family:Arial, Helvetica, sans-serif;
            background:#111;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
        }

        .login-box{

            width:400px;
            background:#fff;
            padding:35px;
            border-radius:12px;
            box-shadow:0 15px 40px rgba(0,0,0,.35);

        }

        .logo{

            text-align:center;
            margin-bottom:8px;
            font-size:32px;
            font-weight:700;

        }

        .logo i{

            color:#dc3545;
            font-size:40px;
            margin-right:8px;

        }

        .subtitle{

            text-align:center;
            color:#6c757d;
            margin-bottom:30px;
            font-size:15px;

        }

        .form-label{

            font-weight:600;

        }

        .form-control{

            border-radius:8px;
            height:45px;

        }

        .btn-login{

            width:100%;
            background:#dc3545;
            color:#fff;
            border:none;
            height:45px;
            border-radius:8px;
            font-weight:600;
            transition:.2s;

        }

        .btn-login:hover{

            background:#bb2d3b;

        }

        .footer{

            text-align:center;
            margin-top:20px;
            color:#888;
            font-size:13px;

        }

    </style>

</head>

<body>

<div class="login-box">

    <div class="logo">

        <i class="bi bi-fire"></i>FuelDex

    </div>

    <div class="subtitle">

        Fuel Station Management System

    </div>

    <?php if($error): ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>

    <form method="POST">

        <div class="mb-3">

            <label class="form-label">

                Email Address

            </label>

            <input
                type="email"
                name="email"
                class="form-control"
                placeholder="Enter your email"
                required>

        </div>

        <div class="mb-4">

            <label class="form-label">

                Password

            </label>

            <input
                type="password"
                name="password"
                class="form-control"
                placeholder="Enter your password"
                required>

        </div>

        <button type="submit" class="btn btn-login">

            <i class="bi bi-box-arrow-in-right"></i>
            Login

        </button>

    </form>

    <div class="footer">

        &copy; <?= date('Y') ?> FuelDex. All rights reserved.

    </div>

</div>

</body>
</html>
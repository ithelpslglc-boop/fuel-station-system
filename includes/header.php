<?php require_once ROOT_PATH . '/config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>FuelDex</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/assets/images/flames.png">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">

    <style>

        .top-navbar{

            position:fixed;
            top:0;
            left:240px;
            right:0;

            height:60px;

            background:#fff;

            border-bottom:1px solid #ddd;

            display:flex;

            justify-content:space-between;

            align-items:center;

            padding:0 25px;

            z-index:1000;

        }

        .page-content{

            margin-left:240px;
            margin-top:60px;
            padding:20px;

        }

    </style>

</head>

<body>

<nav class="top-navbar">

    <h5 class="mb-0">
        
    </h5>

    <div>

        <span class="me-3">
            Welcome,
            <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></strong>
        </span>

        <a href="<?= APP_URL ?>/logout.php"
           class="btn btn-danger btn-sm">

            <i class="bi bi-box-arrow-right"></i>

            Logout

        </a>

    </div>

</nav>
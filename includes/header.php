<?php
require_once ROOT_PATH . '/config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

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

</head>

<body>

<!-- TOP NAVBAR -->
<nav class="top-navbar">

    <div class="d-flex align-items-center justify-content-end w-100">

        <div class="me-3 text-muted">
            Welcome,
            <strong>
                <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>
            </strong>
        </div>

        <a href="<?= APP_URL ?>/logout.php" class="btn btn-danger btn-sm">
            <i class="bi bi-box-arrow-right"></i>
            Logout
        </a>

    </div>

</nav>

<!-- PAGE WRAPPER START -->
<div class="app-wrapper">
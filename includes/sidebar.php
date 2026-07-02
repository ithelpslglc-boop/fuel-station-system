<?php
if (!isset($_SESSION)) {
    session_start();
}
?>

<div class="bg-dark text-white position-fixed h-100" style="width: 240px;">

    <div class="p-3 border-bottom">
        <h5 class="mb-0">
            ⛽ Fuel Station
        </h5>
        <small>v3.0 System</small>
    </div>

    <div class="p-2">
        <a href="<?= APP_URL ?>/index.php" class="d-block text-white p-2 text-decoration-none">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <a href="<?= APP_URL ?>/modules/users/index.php" class="d-block text-white p-2 text-decoration-none">
            <i class="bi bi-people"></i> Users
        </a>

        <a href="#" class="d-block text-white p-2 text-decoration-none">
            <i class="bi bi-fuel-pump"></i> Fuel Inventory
        </a>

        <a href="#" class="d-block text-white p-2 text-decoration-none">
            <i class="bi bi-truck"></i> Suppliers
        </a>

        <a href="#" class="d-block text-white p-2 text-decoration-none">
            <i class="bi bi-cash-stack"></i> Sales
        </a>

        <a href="#" class="d-block text-white p-2 text-decoration-none">
            <i class="bi bi-gear"></i> Settings
        </a>

        <hr class="text-white">

        <a href="<?= APP_URL ?>/logout.php" class="d-block text-white p-2 text-decoration-none">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>

</div>
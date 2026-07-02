<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<style>
    .sidebar {
        width: 240px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        background: #000;
        color: #fff;
        overflow-y: auto;
    }

    .sidebar-header {
        padding: 20px;
        text-align: center;
        border-bottom: 1px solid #222;
    }

    .sidebar-header h4 {
        margin: 0;
        font-weight: 600;
        color: #fff;
    }

    .sidebar-header .bi-fire {
        color: #dc3545;
        margin-right: 8px;
    }

    .sidebar-menu {
        padding: 15px 0;
    }

    .sidebar-menu a {
        display: block;
        color: #ddd;
        text-decoration: none;
        padding: 12px 20px;
        transition: 0.2s;
        font-size: 15px;
    }

    .sidebar-menu a i {
        width: 24px;
        margin-right: 8px;
    }

    .sidebar-menu a:hover {
        background: #1f1f1f;
        color: #fff;
    }

    .sidebar-menu a.active {
        background: #dc3545;
        color: #fff;
    }

    .main-content {
        margin-left: 240px;
        padding: 20px;
    }
</style>

<div class="sidebar">

    <div class="sidebar-header">
        <h4>
            <i class="bi bi-fire"></i>FuelDex
        </h4>
    </div>

    <div class="sidebar-menu">

        <a href="<?= APP_URL ?>/index.php">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <a href="<?= APP_URL ?>/modules/users/index.php">
            <i class="bi bi-people-fill"></i> Users
        </a>

        <a href="<?= APP_URL ?>/modules/fuel/index.php">
            <i class="bi bi-fuel-pump-fill"></i> Fuel
        </a>

        <a href="<?= APP_URL ?>/modules/pumps/index.php">
            <i class="bi bi-droplet-half"></i> Pumps
        </a>

        <a href="<?= APP_URL ?>/modules/suppliers/index.php">
            <i class="bi bi-truck"></i> Suppliers
        </a>

        <a href="<?= APP_URL ?>/modules/sales/index.php">
            <i class="bi bi-receipt"></i> Sales
        </a>

        <a href="<?= APP_URL ?>/modules/expenses/index.php">
            <i class="bi bi-cash-stack"></i> Expenses
        </a>

        <a href="<?= APP_URL ?>/modules/reports/index.php">
            <i class="bi bi-bar-chart-fill"></i> Reports
        </a>

        <hr class="text-secondary">

    </div>

</div>
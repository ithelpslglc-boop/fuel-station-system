<?php
if (!isset($_SESSION)) {
    session_start();
}
?>

<style>
    .sidebar {
        width: 240px;
        height: 100vh;
        position: fixed;
        background: #000;
        color: #fff;
    }

    .sidebar a {
        color: #fff;
        text-decoration: none;
        display: block;
        padding: 10px 15px;
        margin: 4px 10px;
        border-radius: 6px;
    }

    .sidebar a:hover {
        background: #1f1f1f;
    }

    .sidebar-header {
        padding: 15px;
        border-bottom: 1px solid #222;
        text-align: center;
    }

    .main-content {
        margin-left: 240px;
        padding: 20px;
    }
</style>

<div class="sidebar">

    <div class="sidebar-header">
        <h5>🔥 Fuel Station</h5>
        <small>Management System</small>
    </div>

    <a href="<?= APP_URL ?>/index.php">🏠 Dashboard</a>
    <a href="<?= APP_URL ?>/modules/users/index.php">👤 Users</a>
    <a href="<?= APP_URL ?>/modules/fuel/index.php">⛽ Fuel</a>
    <a href="<?= APP_URL ?>/modules/pumps/index.php">🛢 Pumps</a>
    <a href="<?= APP_URL ?>/modules/suppliers/index.php">🚚 Suppliers</a>
    <a href="<?= APP_URL ?>/modules/sales/index.php">💰 Sales</a>
    <a href="<?= APP_URL ?>/modules/expenses/index.php">📉 Expenses</a>
    <a href="<?= APP_URL ?>/modules/reports/index.php">📊 Reports</a>

    <a href="<?= APP_URL ?>/logout.php">🚪 Logout</a>

</div>
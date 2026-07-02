<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="sidebar" id="sidebar">

    <!-- HEADER -->
    <div class="sidebar-header">
        <h4>
            <i class="bi bi-fire"></i>
            <span>FuelDex</span>
        </h4>
    </div>

    <!-- MENU -->
    <div class="sidebar-menu">

        <a href="<?= APP_URL ?>/index.php">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <a href="<?= APP_URL ?>/modules/users/index.php">
            <i class="bi bi-people-fill"></i>
            <span>Users</span>
        </a>

        <a href="<?= APP_URL ?>/modules/fuel/index.php">
            <i class="bi bi-fuel-pump-fill"></i>
            <span>Fuel</span>
        </a>

        <a href="<?= APP_URL ?>/modules/pumps/index.php">
            <i class="bi bi-droplet-half"></i>
            <span>Pumps</span>
        </a>

        <a href="<?= APP_URL ?>/modules/suppliers/index.php">
            <i class="bi bi-truck"></i>
            <span>Suppliers</span>
        </a>

        <a href="<?= APP_URL ?>/modules/sales/index.php">
            <i class="bi bi-receipt"></i>
            <span>Sales</span>
        </a>

        <a href="<?= APP_URL ?>/modules/expenses/index.php">
            <i class="bi bi-cash-stack"></i>
            <span>Expenses</span>
        </a>

        <a href="<?= APP_URL ?>/modules/reports/index.php">
            <i class="bi bi-bar-chart-fill"></i>
            <span>Reports</span>
        </a>

    </div>

    <!-- TOGGLE -->
    <div class="sidebar-footer">

        <button class="toggle-sidebar" id="toggleSidebar">

            <i class="bi bi-chevron-double-left" id="toggleIcon"></i>

        </button>

    </div>

</div>

<script>

document.addEventListener("DOMContentLoaded", function () {

    const body = document.body;
    const toggleBtn = document.getElementById("toggleSidebar");
    const toggleIcon = document.getElementById("toggleIcon");

    // restore state
    if (localStorage.getItem("sidebar") === "collapsed") {
        body.classList.add("sidebar-collapsed");

        toggleIcon.classList.remove("bi-chevron-double-left");
        toggleIcon.classList.add("bi-chevron-double-right");
    }

    toggleBtn.addEventListener("click", function () {

        body.classList.toggle("sidebar-collapsed");

        const isCollapsed = body.classList.contains("sidebar-collapsed");

        localStorage.setItem("sidebar", isCollapsed ? "collapsed" : "expanded");

        if (isCollapsed) {
            toggleIcon.classList.remove("bi-chevron-double-left");
            toggleIcon.classList.add("bi-chevron-double-right");
        } else {
            toggleIcon.classList.remove("bi-chevron-double-right");
            toggleIcon.classList.add("bi-chevron-double-left");
        }

    });

});

</script>
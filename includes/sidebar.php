<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="sidebar" id="sidebar">

    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <h4>
            <i class="bi bi-fire"></i>
            <span>FuelDex</span>
        </h4>
    </div>

    <!-- Sidebar Menu -->
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
        
        <a href="<?= APP_URL ?>/modules/tanks/index.php">
            <i class="bi bi-box-seam"></i>
            <span>Tanks</span>
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

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">

        <button
            class="toggle-sidebar"
            id="toggleSidebar"
            title="Collapse / Expand Sidebar">

            <i
                class="bi bi-layout-sidebar-inset"
                id="toggleIcon"
                style="font-size:24px;">
            </i>

        </button>

    </div>

</div>

<script>

document.addEventListener("DOMContentLoaded", function () {

    const body = document.body;
    const toggleBtn = document.getElementById("toggleSidebar");
    const toggleIcon = document.getElementById("toggleIcon");

    // Restore previous state
    if (localStorage.getItem("sidebar") === "collapsed") {

        body.classList.add("sidebar-collapsed");

        toggleIcon.classList.remove("bi-layout-sidebar-inset");
        toggleIcon.classList.add("bi-layout-sidebar");

        toggleBtn.title = "Expand Sidebar";

    } else {

        toggleBtn.title = "Collapse Sidebar";

    }

    toggleBtn.addEventListener("click", function () {

        body.classList.toggle("sidebar-collapsed");

        const collapsed = body.classList.contains("sidebar-collapsed");

        localStorage.setItem(
            "sidebar",
            collapsed ? "collapsed" : "expanded"
        );

        if (collapsed) {

            toggleIcon.classList.remove("bi-layout-sidebar-inset");
            toggleIcon.classList.add("bi-layout-sidebar");

            toggleBtn.title = "Expand Sidebar";

        } else {

            toggleIcon.classList.remove("bi-layout-sidebar");
            toggleIcon.classList.add("bi-layout-sidebar-inset");

            toggleBtn.title = "Collapse Sidebar";

        }

    });

});

</script>
<?php
require_once 'config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <h3>Welcome, <?= $_SESSION['user_name'] ?></h3>

    <div class="card mt-3 p-3">
        <h5>Dashboard</h5>
        <p>System is running successfully.</p>
    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name  = trim($_POST['name']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    if (empty($name) || empty($price)) {
        $error = "Fuel name and price are required";
    } else {

        // INSERT FUEL TYPE
        $stmt = $pdo->prepare("
            INSERT INTO fuel_types (name, price_per_liter, current_stock, status)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $name,
            $price,
            $stock,
            1 // default ACTIVE
        ]);

        header("Location: index.php");
        exit;
    }
}
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Add Fuel Type</h4>

        <a href="index.php" class="btn btn-secondary btn-sm">
            ← Back
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label>Fuel Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Price per Litre</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Initial Stock (Litres)</label>
                    <input type="number" step="0.01" name="stock" class="form-control" value="0">
                </div>

                <button class="btn btn-success w-100">
                    Create Fuel Type
                </button>

            </form>

        </div>
    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
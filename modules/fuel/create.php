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

        $error = "Name and Price are required";

    } else {

        $stmt = $pdo->prepare("
            INSERT INTO fuel_types (name, price_per_liter, current_stock)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([
            $name,
            $price,
            $stock
        ]);

        header("Location: index.php");
        exit;
    }
}
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="page-content">

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h3 class="mb-0">
                Add Fuel Type
            </h3>

            <a href="index.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
                Back
            </a>

        </div>

        <div class="card">

            <div class="card-body">

                <?php if ($error): ?>

                    <div class="alert alert-danger">

                        <?= htmlspecialchars($error) ?>

                    </div>

                <?php endif; ?>

                <form method="POST">

                    <div class="mb-3">

                        <label class="form-label">
                            Fuel Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Price per Litre
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="price"
                            class="form-control"
                            required>

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Initial Stock (Litres)
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="stock"
                            value="0"
                            class="form-control">

                    </div>

                    <button
                        type="submit"
                        class="btn btn-success">

                        <i class="bi bi-check-circle"></i>

                        Save Fuel Type

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
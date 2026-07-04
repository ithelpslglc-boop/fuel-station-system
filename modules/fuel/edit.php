<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

// Fetch Fuel Type
$stmt = $pdo->prepare("SELECT * FROM fuel_types WHERE id = ?");
$stmt->execute([$id]);
$fuel = $stmt->fetch();

if (!$fuel) {
    die("Fuel type not found");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $status = $_POST['status'];

    if (empty($name) || empty($price)) {

        $error = "Name and Price are required";

    } else {

        $stmt = $pdo->prepare("
            UPDATE fuel_types
            SET
                name = ?,
                price_per_liter = ?,
                current_stock = ?,
                status = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $name,
            $price,
            $stock,
            $status,
            $id
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
                Edit Fuel Type
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
                            value="<?= htmlspecialchars($fuel['name']) ?>"
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
                            value="<?= $fuel['price_per_liter'] ?>"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Stock (Litres)
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="stock"
                            class="form-control"
                            value="<?= $fuel['current_stock'] ?>"
                            required>

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select">

                            <option value="1" <?= $fuel['status'] == 1 ? 'selected' : '' ?>>
                                Active
                            </option>

                            <option value="0" <?= $fuel['status'] == 0 ? 'selected' : '' ?>>
                                Inactive
                            </option>

                        </select>

                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-check-circle"></i>

                        Update Fuel Type

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
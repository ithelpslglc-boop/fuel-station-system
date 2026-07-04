<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

$error = '';

$stmt = $pdo->prepare("
    SELECT
        pumps.id,
        pumps.pump_name,
        fuel_types.id AS fuel_id,
        fuel_types.name AS fuel_name,
        fuel_types.price_per_liter,
        fuel_types.current_stock
    FROM pumps
    INNER JOIN fuel_types
        ON pumps.fuel_type_id = fuel_types.id
    WHERE pumps.status = 1
    ORDER BY pumps.pump_name
");
$stmt->execute();
$pumps = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pump_id = (int)$_POST['pump_id'];
    $liters = (float)$_POST['liters'];
    $payment_method = $_POST['payment_method'];

    if ($pump_id <= 0 || $liters <= 0) {

        $error = "Please enter valid details.";

    } else {

        $stmt = $pdo->prepare("
            SELECT
                fuel_types.id,
                fuel_types.price_per_liter,
                fuel_types.current_stock
            FROM pumps
            INNER JOIN fuel_types
                ON pumps.fuel_type_id = fuel_types.id
            WHERE pumps.id = ?
        ");

        $stmt->execute([$pump_id]);
        $fuel = $stmt->fetch();

        if (!$fuel) {

            $error = "Invalid pump selected.";

        } elseif ($fuel['current_stock'] < $liters) {

            $error = "Insufficient fuel stock. Available stock: "
                . number_format($fuel['current_stock'], 2)
                . " Litres.";

        } else {

            $price = $fuel['price_per_liter'];
            $total = $price * $liters;

            try {

                $pdo->beginTransaction();

                // Record Sale
                $stmt = $pdo->prepare("
                    INSERT INTO sales
                    (
                        pump_id,
                        fuel_type_id,
                        liters,
                        price_per_liter,
                        total_amount,
                        payment_method
                    )
                    VALUES (?, ?, ?, ?, ?, ?)
                ");

                $stmt->execute([
                    $pump_id,
                    $fuel['id'],
                    $liters,
                    $price,
                    $total,
                    $payment_method
                ]);

                // Deduct Stock
                $stmt = $pdo->prepare("
                    UPDATE fuel_types
                    SET current_stock = current_stock - ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    $liters,
                    $fuel['id']
                ]);

                $pdo->commit();

                header("Location: index.php");
                exit;

            } catch (Exception $e) {

                $pdo->rollBack();
                $error = "Unable to complete sale.";
            }
        }
    }
}
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="page-content">

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h3 class="mb-0">New Sale</h3>

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
                            Pump
                        </label>

                        <select
                            name="pump_id"
                            class="form-select"
                            required>

                            <option value="">
                                Select Pump
                            </option>

                            <?php foreach ($pumps as $pump): ?>

                                <option value="<?= $pump['id'] ?>">

                                    <?= htmlspecialchars($pump['pump_name']) ?>

                                    -

                                    <?= htmlspecialchars($pump['fuel_name']) ?>

                                    (<?= number_format($pump['current_stock'],2) ?> L)

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Litres
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            min="0.01"
                            name="liters"
                            class="form-control"
                            required>

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Payment Method
                        </label>

                        <select
                            name="payment_method"
                            class="form-select">

                            <option value="cash">
                                Cash
                            </option>

                            <option value="card">
                                Card
                            </option>

                        </select>

                    </div>

                    <button
                        type="submit"
                        class="btn btn-success">

                        <i class="bi bi-check-circle"></i>

                        Complete Sale

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
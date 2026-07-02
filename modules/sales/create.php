<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

$error = '';

// FETCH PUMPS
$stmt = $pdo->prepare("
    SELECT pumps.*, fuel_types.price_per_liter, fuel_types.id AS fuel_id
    FROM pumps
    JOIN fuel_types ON pumps.fuel_type_id = fuel_types.id
    WHERE pumps.status = 1
");
$stmt->execute();
$pumps = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pump_id = $_POST['pump_id'];
    $liters = $_POST['liters'];
    $payment_method = $_POST['payment_method'];

    if (empty($pump_id) || empty($liters)) {
        $error = "All fields are required";
    } else {

        // GET FUEL PRICE
        $stmt = $pdo->prepare("
            SELECT fuel_types.price_per_liter, pumps.fuel_type_id
            FROM pumps
            JOIN fuel_types ON pumps.fuel_type_id = fuel_types.id
            WHERE pumps.id = ?
        ");
        $stmt->execute([$pump_id]);
        $data = $stmt->fetch();

        $price = $data['price_per_liter'];
        $fuel_id = $data['fuel_type_id'];

        $total = $price * $liters;

        // INSERT SALE
        $stmt = $pdo->prepare("
            INSERT INTO sales 
            (pump_id, fuel_type_id, liters, price_per_liter, total_amount, payment_method)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $pump_id,
            $fuel_id,
            $liters,
            $price,
            $total,
            $payment_method
        ]);

        header("Location: index.php");
        exit;
    }
}
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <h4>New Sale</h4>

    <div class="card p-3 shadow-sm mt-3" style="max-width:500px;">

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-2">
                <label>Pump</label>
                <select name="pump_id" class="form-select" required>
                    <option value="">Select Pump</option>
                    <?php foreach ($pumps as $pump): ?>
                        <option value="<?= $pump['id'] ?>">
                            <?= htmlspecialchars($pump['pump_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-2">
                <label>Liters</label>
                <input type="number" step="0.01" name="liters" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Payment Method</label>
                <select name="payment_method" class="form-select">
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                </select>
            </div>

            <button class="btn btn-success w-100">Save Sale</button>

        </form>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
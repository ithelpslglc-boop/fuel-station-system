<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

$stmt = $pdo->query("SELECT * FROM fuel_types WHERE status = 1");
$fuels = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM pumps WHERE status = 1");
$pumps = $stmt->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fuel_type_id = $_POST['fuel_type_id'];
    $pump_id = $_POST['pump_id'] ?: null;
    $liters = $_POST['liters'];

    if (empty($fuel_type_id) || empty($liters)) {
        $error = "Fuel and liters are required";
    } else {

        // get fuel price + stock
        $stmt = $pdo->prepare("SELECT * FROM fuel_types WHERE id = ?");
        $stmt->execute([$fuel_type_id]);
        $fuel = $stmt->fetch();

        if (!$fuel) {
            $error = "Invalid fuel type";
        } elseif ($fuel['current_stock'] < $liters) {
            $error = "Not enough stock available";
        } else {

            $price = $fuel['price_per_liter'];
            $total = $price * $liters;

            // insert sale
            $stmt = $pdo->prepare("
                INSERT INTO sales (fuel_type_id, pump_id, liters, price_per_liter, total_amount)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $fuel_type_id,
                $pump_id,
                $liters,
                $price,
                $total
            ]);

            // reduce stock
            $stmt = $pdo->prepare("
                UPDATE fuel_types
                SET current_stock = current_stock - ?
                WHERE id = ?
            ");

            $stmt->execute([$liters, $fuel_type_id]);

            header("Location: index.php");
            exit;
        }
    }
}
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <h4>New Sale</h4>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-3">

        <div class="mb-2">
            <label>Fuel Type</label>
            <select name="fuel_type_id" class="form-select" required>
                <option value="">Select Fuel</option>
                <?php foreach ($fuels as $f): ?>
                    <option value="<?= $f['id'] ?>">
                        <?= htmlspecialchars($f['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-2">
            <label>Pump (optional)</label>
            <select name="pump_id" class="form-select">
                <option value="">None</option>
                <?php foreach ($pumps as $p): ?>
                    <option value="<?= $p['id'] ?>">
                        <?= htmlspecialchars($p['pump_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Liters</label>
            <input type="number" step="0.01" name="liters" class="form-control" required>
        </div>

        <button class="btn btn-success w-100">Complete Sale</button>

    </form>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
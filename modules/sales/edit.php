<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/fuel_functions.php';

checkAuth();

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];
$error = "";

/*
|--------------------------------------------------------------------------
| LOAD SALE
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT sales.*, pumps.tank_id
    FROM sales
    INNER JOIN pumps ON sales.pump_id = pumps.id
    WHERE sales.id = ?
");

$stmt->execute([$id]);
$sale = $stmt->fetch();

if (!$sale) {
    die("Sale not found.");
}

/*
|--------------------------------------------------------------------------
| LOAD PUMPS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT pumps.*, fuel_tanks.tank_name, fuel_types.name AS fuel_name
    FROM pumps
    INNER JOIN fuel_tanks ON pumps.tank_id = fuel_tanks.id
    INNER JOIN fuel_types ON fuel_tanks.fuel_type_id = fuel_types.id
    WHERE pumps.status = 1
");

$stmt->execute();
$pumps = $stmt->fetchAll();

/*
|--------------------------------------------------------------------------
| HANDLE UPDATE
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pump_id = (int)$_POST['pump_id'];
    $liters  = (float)$_POST['liters'];
    $payment = $_POST['payment_method'];

    if ($pump_id <= 0 || $liters <= 0) {
        $error = "Invalid input.";
    } else {

        /*
        |--------------------------------------------------------------------------
        | GET OLD TANK INFO (REVERSE OLD SALE)
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            SELECT pumps.tank_id
            FROM sales
            INNER JOIN pumps ON sales.pump_id = pumps.id
            WHERE sales.id = ?
        ");

        $stmt->execute([$id]);
        $old = $stmt->fetch();

        /*
        |--------------------------------------------------------------------------
        | RESTORE OLD STOCK
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            UPDATE fuel_tanks
            SET current_level = current_level + ?
            WHERE id = ?
        ");

        $stmt->execute([
            $sale['liters'],
            $old['tank_id']
        ]);

        /*
        |--------------------------------------------------------------------------
        | GET NEW PUMP DATA
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            SELECT pumps.tank_id,
                   fuel_tanks.current_level,
                   fuel_types.price_per_liter,
                   fuel_tanks.fuel_type_id
            FROM pumps
            INNER JOIN fuel_tanks ON pumps.tank_id = fuel_tanks.id
            INNER JOIN fuel_types ON fuel_tanks.fuel_type_id = fuel_types.id
            WHERE pumps.id = ?
        ");

        $stmt->execute([$pump_id]);
        $data = $stmt->fetch();

        if (!$data) {
            $error = "Invalid pump selected.";
        } else {

            $price = $data['price_per_liter'];
            $total = $price * $liters;

            $new_level = $data['current_level'] - $liters;

            if ($new_level < 0) {
                $error = "Not enough fuel in tank.";
            } else {

                /*
                |--------------------------------------------------------------------------
                | APPLY NEW STOCK
                |--------------------------------------------------------------------------
                */

                $stmt = $pdo->prepare("
                    UPDATE fuel_tanks
                    SET current_level = ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    $new_level,
                    $data['tank_id']
                ]);

                /*
                |--------------------------------------------------------------------------
                | UPDATE SALE
                |--------------------------------------------------------------------------
                */

                $stmt = $pdo->prepare("
                    UPDATE sales
                    SET pump_id = ?,
                        fuel_type_id = ?,
                        liters = ?,
                        price_per_liter = ?,
                        total_amount = ?,
                        payment_method = ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    $pump_id,
                    $data['fuel_type_id'],
                    $liters,
                    $price,
                    $total,
                    $payment,
                    $id
                ]);

                /*
                |--------------------------------------------------------------------------
                | SYNC INVENTORY
                |--------------------------------------------------------------------------
                */

                updateFuelStock($pdo, $data['fuel_type_id']);

                header("Location: index.php");
                exit;
            }
        }
    }
}
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Edit Sale</h4>
        <a href="index.php" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">
                    <label>Pump</label>
                    <select name="pump_id" class="form-select" required>

                        <?php foreach ($pumps as $pump): ?>
                            <option value="<?= $pump['id'] ?>"
                                <?= $sale['pump_id'] == $pump['id'] ? 'selected' : '' ?>>

                                <?= htmlspecialchars($pump['pump_name']) ?>
                                (<?= htmlspecialchars($pump['fuel_name']) ?>)

                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="mb-3">
                    <label>Liters</label>
                    <input type="number"
                           step="0.01"
                           name="liters"
                           class="form-control"
                           value="<?= $sale['liters'] ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label>Payment Method</label>
                    <select name="payment_method" class="form-select">

                        <option value="cash" <?= $sale['payment_method'] == 'cash' ? 'selected' : '' ?>>Cash</option>
                        <option value="card" <?= $sale['payment_method'] == 'card' ? 'selected' : '' ?>>Card</option>
                        <option value="credit" <?= $sale['payment_method'] == 'credit' ? 'selected' : '' ?>>Credit</option>

                    </select>
                </div>

                <button class="btn btn-success">
                    Update Sale
                </button>

            </form>

        </div>
    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/fuel_functions.php';

checkAuth();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$tank_id = (int)$_GET['id'];
$error = "";

/*
|--------------------------------------------------------------------------
| LOAD TANK
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT fuel_tanks.*, fuel_types.name AS fuel_name
    FROM fuel_tanks
    INNER JOIN fuel_types ON fuel_tanks.fuel_type_id = fuel_types.id
    WHERE fuel_tanks.id = ?
");

$stmt->execute([$tank_id]);

$tank = $stmt->fetch();

if (!$tank) {
    die("Tank not found.");
}

/*
|--------------------------------------------------------------------------
| HANDLE FORM SUBMIT
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $type     = $_POST['type']; // increase / decrease
    $quantity = (float)$_POST['quantity'];
    $reason   = trim($_POST['reason']);
    $remarks  = trim($_POST['remarks']);

    if ($quantity <= 0) {
        $error = "Quantity must be greater than zero.";
    }

    elseif (empty($reason)) {
        $error = "Reason is required.";
    }

    else {

        $current = (float)$tank['current_level'];

        /*
        |--------------------------------------------------------------------------
        | CALCULATE NEW LEVEL
        |--------------------------------------------------------------------------
        */

        if ($type === 'increase') {
            $new_level = $current + $quantity;
        } else {
            $new_level = $current - $quantity;
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDATIONS
        |--------------------------------------------------------------------------
        */

        if ($new_level < 0) {
            $error = "Cannot reduce below 0 litres.";
        }

        elseif ($new_level > $tank['capacity']) {
            $error = "Cannot exceed tank capacity.";
        }

        else {

            /*
            |--------------------------------------------------------------------------
            | UPDATE TANK
            |--------------------------------------------------------------------------
            */

            $stmt = $pdo->prepare("
                UPDATE fuel_tanks
                SET current_level = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $new_level,
                $tank_id
            ]);

            /*
            |--------------------------------------------------------------------------
            | INSERT LOG
            |--------------------------------------------------------------------------
            */

            $stmt = $pdo->prepare("
                INSERT INTO tank_adjustments
                (
                    tank_id,
                    adjustment_type,
                    quantity,
                    reason,
                    remarks,
                    adjusted_by
                )
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $tank_id,
                $type,
                $quantity,
                $reason,
                $remarks,
                $_SESSION['user_id']
            ]);

            /*
            |--------------------------------------------------------------------------
            | SYNC FUEL STOCK
            |--------------------------------------------------------------------------
            */

            updateFuelStock($pdo, $tank['fuel_type_id']);

            header("Location: index.php");
            exit;
        }
    }
}
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4>Adjust Tank - <?= htmlspecialchars($tank['tank_name']) ?></h4>

        <a href="index.php" class="btn btn-secondary btn-sm">
            ← Back
        </a>

    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <div class="mb-3">

                <strong>Fuel Type:</strong>
                <?= htmlspecialchars($tank['fuel_name']) ?>

                <br>

                <strong>Current Level:</strong>
                <?= number_format($tank['current_level'], 2) ?> L

                <br>

                <strong>Capacity:</strong>
                <?= number_format($tank['capacity'], 2) ?> L

            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">

                    <label class="form-label">Adjustment Type</label>

                    <select name="type" class="form-select" required>

                        <option value="increase">Increase (Stock In)</option>
                        <option value="decrease">Decrease (Stock Out)</option>

                    </select>

                </div>

                <div class="mb-3">

                    <label class="form-label">Quantity (Litres)</label>

                    <input type="number"
                           step="0.01"
                           name="quantity"
                           class="form-control"
                           required>

                </div>

                <div class="mb-3">

                    <label class="form-label">Reason</label>

                    <input type="text"
                           name="reason"
                           class="form-control"
                           placeholder="e.g. Supplier delivery, evaporation"
                           required>

                </div>

                <div class="mb-3">

                    <label class="form-label">Remarks</label>

                    <textarea name="remarks"
                              class="form-control"
                              rows="3"></textarea>

                </div>

                <button type="submit" class="btn btn-success">

                    Save Adjustment

                </button>

            </form>

        </div>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
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
| LOAD TANK
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM fuel_tanks
    WHERE id = ?
");

$stmt->execute([$id]);

$tank = $stmt->fetch();

if (!$tank) {
    die("Tank not found.");
}

/*
|--------------------------------------------------------------------------
| LOAD ACTIVE FUEL TYPES
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM fuel_types
    WHERE status = 1
    ORDER BY name ASC
");

$stmt->execute();

$fuelTypes = $stmt->fetchAll();

/*
|--------------------------------------------------------------------------
| SAVE CHANGES
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tank_name = trim($_POST['tank_name']);
    $fuel_type_id = (int)$_POST['fuel_type_id'];
    $capacity = (float)$_POST['capacity'];
    $status = (int)$_POST['status'];

    if (
        empty($tank_name) ||
        empty($fuel_type_id) ||
        empty($capacity)
    ) {

        $error = "Please fill all required fields.";

    } elseif ($capacity < $tank['current_level']) {

        $error = "Capacity cannot be less than the current fuel level.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | CHECK DUPLICATE NAME
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            SELECT id
            FROM fuel_tanks
            WHERE tank_name = ?
            AND id <> ?
        ");

        $stmt->execute([
            $tank_name,
            $id
        ]);

        if ($stmt->rowCount() > 0) {

            $error = "A tank with this name already exists.";

        } else {

            $oldFuelType = $tank['fuel_type_id'];

            /*
            |--------------------------------------------------------------------------
            | UPDATE TANK
            |--------------------------------------------------------------------------
            */

            $stmt = $pdo->prepare("
                UPDATE fuel_tanks
                SET
                    tank_name = ?,
                    fuel_type_id = ?,
                    capacity = ?,
                    status = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $tank_name,
                $fuel_type_id,
                $capacity,
                $status,
                $id
            ]);

            /*
            |--------------------------------------------------------------------------
            | RECALCULATE STOCK
            |--------------------------------------------------------------------------
            */

            updateFuelStock($pdo, $oldFuelType);

            if ($oldFuelType != $fuel_type_id) {

                updateFuelStock($pdo, $fuel_type_id);

            }

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

        <h4>Edit Fuel Tank</h4>

        <a href="index.php" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>

    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <?php if (!empty($error)): ?>

                <div class="alert alert-danger">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">

                    <label class="form-label">
                        Tank Name
                    </label>

                    <input
                        type="text"
                        name="tank_name"
                        class="form-control"
                        required
                        value="<?= htmlspecialchars($tank['tank_name']) ?>">

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Fuel Type
                    </label>

                    <select
                        name="fuel_type_id"
                        class="form-select"
                        required>

                        <?php foreach ($fuelTypes as $fuel): ?>

                            <option
                                value="<?= $fuel['id'] ?>"
                                <?= $tank['fuel_type_id'] == $fuel['id'] ? 'selected' : '' ?>>

                                <?= htmlspecialchars($fuel['name']) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Tank Capacity (L)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        min="<?= $tank['current_level'] ?>"
                        name="capacity"
                        class="form-control"
                        required
                        value="<?= $tank['capacity'] ?>">

                    <small class="text-muted">
                        Capacity cannot be less than the current fuel level
                        (<?= number_format($tank['current_level'], 2) ?> L).
                    </small>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Current Fuel Level
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="<?= number_format($tank['current_level'], 2) ?> L"
                        readonly>

                    <small class="text-muted">
                        Fuel level can only be changed using the Adjust page.
                    </small>

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Status
                    </label>

                    <select
                        name="status"
                        class="form-select">

                        <option
                            value="1"
                            <?= $tank['status'] ? 'selected' : '' ?>>

                            Active

                        </option>

                        <option
                            value="0"
                            <?= !$tank['status'] ? 'selected' : '' ?>>

                            Inactive

                        </option>

                    </select>

                </div>

                <button
                    type="submit"
                    class="btn btn-success">

                    <i class="bi bi-check-circle"></i>

                    Save Changes

                </button>

            </form>

        </div>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
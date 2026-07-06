<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/fuel_functions.php';

checkAuth();

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

$error = "";

/*
|--------------------------------------------------------------------------
| FETCH ACTIVE FUEL TYPES
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
| SAVE TANK
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tank_name      = trim($_POST['tank_name']);
    $fuel_type_id   = $_POST['fuel_type_id'];
    $capacity       = $_POST['capacity'];
    $current_level  = $_POST['current_level'];
    $status         = $_POST['status'];

    if (
        empty($tank_name) ||
        empty($fuel_type_id) ||
        empty($capacity)
    ) {

        $error = "Please fill all required fields.";

    } elseif ($current_level > $capacity) {

        $error = "Initial fuel level cannot exceed tank capacity.";

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
        ");

        $stmt->execute([$tank_name]);

        if ($stmt->rowCount()) {

            $error = "Tank name already exists.";

        } else {

            /*
            |--------------------------------------------------------------------------
            | CREATE TANK
            |--------------------------------------------------------------------------
            */

            $stmt = $pdo->prepare("
                INSERT INTO fuel_tanks
                (
                    tank_name,
                    fuel_type_id,
                    capacity,
                    current_level,
                    status
                )
                VALUES
                (
                    ?, ?, ?, ?, ?
                )
            ");

            $stmt->execute([
                $tank_name,
                $fuel_type_id,
                $capacity,
                $current_level,
                $status
            ]);

            /*
            |--------------------------------------------------------------------------
            | UPDATE FUEL INVENTORY
            |--------------------------------------------------------------------------
            */

            updateFuelStock($pdo, $fuel_type_id);

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

        <h4>Add Fuel Tank</h4>

        <a href="index.php" class="btn btn-secondary btn-sm">

            ← Back

        </a>

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

                    <label class="form-label">

                        Tank Name

                    </label>

                    <input
                        type="text"
                        name="tank_name"
                        class="form-control"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Fuel Type

                    </label>

                    <select
                        name="fuel_type_id"
                        class="form-select"
                        required>

                        <option value="">

                            Select Fuel

                        </option>

                        <?php foreach($fuelTypes as $fuel): ?>

                            <option
                                value="<?= $fuel['id'] ?>">

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
                        min="0"
                        name="capacity"
                        class="form-control"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Initial Fuel Level (L)

                    </label>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        value="0"
                        name="current_level"
                        class="form-control"
                        required>

                </div>

                <div class="mb-4">

                    <label class="form-label">

                        Status

                    </label>

                    <select
                        name="status"
                        class="form-select">

                        <option value="1">

                            Active

                        </option>

                        <option value="0">

                            Inactive

                        </option>

                    </select>

                </div>

                <button
                    type="submit"
                    class="btn btn-success">

                    Save Tank

                </button>

            </form>

        </div>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

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
| LOAD PUMP
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM pumps
    WHERE id = ?
");

$stmt->execute([$id]);
$pump = $stmt->fetch();

if (!$pump) {
    die("Pump not found.");
}

/*
|--------------------------------------------------------------------------
| LOAD TANKS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT fuel_tanks.*, fuel_types.name AS fuel_name
    FROM fuel_tanks
    INNER JOIN fuel_types ON fuel_tanks.fuel_type_id = fuel_types.id
    WHERE fuel_tanks.status = 1
    ORDER BY fuel_tanks.id DESC
");

$stmt->execute();
$tanks = $stmt->fetchAll();

/*
|--------------------------------------------------------------------------
| UPDATE PUMP
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pump_name = trim($_POST['pump_name']);
    $tank_id   = (int)$_POST['tank_id'];
    $status    = (int)$_POST['status'];

    if (empty($pump_name) || empty($tank_id)) {
        $error = "Pump name and tank are required.";
    } else {

        $stmt = $pdo->prepare("
            UPDATE pumps
            SET pump_name = ?, tank_id = ?, status = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $pump_name,
            $tank_id,
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

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Edit Pump</h4>
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
                    <label>Pump Name</label>
                    <input type="text"
                           name="pump_name"
                           class="form-control"
                           required
                           value="<?= htmlspecialchars($pump['pump_name']) ?>">
                </div>

                <div class="mb-3">
                    <label>Tank</label>

                    <select name="tank_id" class="form-select" required>

                        <?php foreach ($tanks as $tank): ?>
                            <option value="<?= $tank['id'] ?>"
                                <?= $pump['tank_id'] == $tank['id'] ? 'selected' : '' ?>>

                                <?= htmlspecialchars($tank['tank_name']) ?>
                                (<?= htmlspecialchars($tank['fuel_name']) ?>)

                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="mb-3">
                    <label>Status</label>

                    <select name="status" class="form-select">

                        <option value="1" <?= $pump['status'] ? 'selected' : '' ?>>
                            Active
                        </option>

                        <option value="0" <?= !$pump['status'] ? 'selected' : '' ?>>
                            Inactive
                        </option>

                    </select>
                </div>

                <button class="btn btn-success">
                    Save Changes
                </button>

            </form>

        </div>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
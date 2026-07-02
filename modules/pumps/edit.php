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

// FETCH PUMP
$stmt = $pdo->prepare("SELECT * FROM pumps WHERE id = ?");
$stmt->execute([$id]);
$pump = $stmt->fetch();

if (!$pump) {
    die("Pump not found");
}

// FUEL TYPES
$stmt = $pdo->prepare("SELECT * FROM fuel_types WHERE status = 1");
$stmt->execute();
$fuels = $stmt->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pump_name = trim($_POST['pump_name']);
    $fuel_type_id = $_POST['fuel_type_id'];
    $status = $_POST['status'];

    if (empty($pump_name) || empty($fuel_type_id)) {
        $error = "All fields are required";
    } else {

        $stmt = $pdo->prepare("
            UPDATE pumps
            SET pump_name = ?, fuel_type_id = ?, status = ?
            WHERE id = ?
        ");

        $stmt->execute([$pump_name, $fuel_type_id, $status, $id]);

        header("Location: index.php");
        exit;
    }
}
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <h4>Edit Pump</h4>

    <div class="card p-3 shadow-sm mt-3" style="max-width:500px;">

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-2">
                <label>Pump Name</label>
                <input type="text" name="pump_name" class="form-control"
                       value="<?= htmlspecialchars($pump['pump_name']) ?>" required>
            </div>

            <div class="mb-2">
                <label>Fuel Type</label>
                <select name="fuel_type_id" class="form-select">
                    <?php foreach ($fuels as $fuel): ?>
                        <option value="<?= $fuel['id'] ?>"
                            <?= $pump['fuel_type_id'] == $fuel['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($fuel['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-select">
                    <option value="1" <?= $pump['status'] == 1 ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= $pump['status'] == 0 ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <button class="btn btn-primary w-100">Update Pump</button>

        </form>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
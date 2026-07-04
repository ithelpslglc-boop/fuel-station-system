<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

/* FETCH TANK */
$stmt = $pdo->prepare("
    SELECT * FROM fuel_tanks WHERE id = ?
");
$stmt->execute([$id]);
$tank = $stmt->fetch();

if (!$tank) {
    die("Tank not found");
}

/* FUEL TYPES */
$stmt = $pdo->prepare("SELECT * FROM fuel_types WHERE status = 1");
$stmt->execute();
$fuels = $stmt->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tank_name = trim($_POST['tank_name']);
    $fuel_type_id = $_POST['fuel_type_id'];
    $capacity = $_POST['capacity'];
    $current_level = $_POST['current_level'];

    if (empty($tank_name) || empty($fuel_type_id) || $capacity === '') {
        $error = "All fields are required";
    } else {

        $stmt = $pdo->prepare("
            UPDATE fuel_tanks
            SET tank_name = ?, fuel_type_id = ?, capacity = ?, current_level = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $tank_name,
            $fuel_type_id,
            $capacity,
            $current_level,
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

    <h4>Edit Tank</h4>

    <div class="card p-3 shadow-sm mt-3" style="max-width:600px;">

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label>Tank Name</label>
                <input type="text" name="tank_name" class="form-control"
                       value="<?= htmlspecialchars($tank['tank_name']) ?>" required>
            </div>

            <div class="mb-3">
                <label>Fuel Type</label>
                <select name="fuel_type_id" class="form-select">
                    <?php foreach ($fuels as $fuel): ?>
                        <option value="<?= $fuel['id'] ?>"
                            <?= $tank['fuel_type_id'] == $fuel['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($fuel['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Capacity (Litres)</label>
                <input type="number" step="0.01" name="capacity" class="form-control"
                       value="<?= $tank['capacity'] ?>" required>
            </div>

            <div class="mb-3">
                <label>Current Level (Litres)</label>
                <input type="number" step="0.01" name="current_level" class="form-control"
                       value="<?= $tank['current_level'] ?>" required>
            </div>

            <button class="btn btn-primary w-100">
                Update Tank
            </button>

        </form>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

/* FETCH FUEL TYPES */
$stmt = $pdo->prepare("SELECT * FROM fuel_types WHERE status = 1");
$stmt->execute();
$fuels = $stmt->fetchAll();

$error = '';

/* HANDLE FORM */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tank_name = trim($_POST['tank_name']);
    $fuel_type_id = $_POST['fuel_type_id'];
    $capacity = $_POST['capacity'];
    $current_level = $_POST['current_level'];

    if (empty($tank_name) || empty($fuel_type_id) || $capacity === '') {
        $error = "All fields are required";
    } else {

        /* DEFAULT CURRENT LEVEL IF EMPTY */
        if ($current_level === '' || $current_level === null) {
            $current_level = 0;
        }

        /* VALIDATION */
        if ($current_level > $capacity) {
            $error = "Current level cannot exceed capacity";
        } else {

            $stmt = $pdo->prepare("
                INSERT INTO fuel_tanks (tank_name, fuel_type_id, capacity, current_level)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $tank_name,
                $fuel_type_id,
                $capacity,
                $current_level
            ]);

            header("Location: index.php");
            exit;
        }
    }
}
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <h4>Create Fuel Tank</h4>

    <div class="card p-3 shadow-sm mt-3" style="max-width:600px;">

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label>Tank Name</label>
                <input type="text" name="tank_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Fuel Type</label>
                <select name="fuel_type_id" class="form-select" required>
                    <option value="">Select Fuel Type</option>
                    <?php foreach ($fuels as $fuel): ?>
                        <option value="<?= $fuel['id'] ?>">
                            <?= htmlspecialchars($fuel['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Capacity (Litres)</label>
                <input type="number" step="0.01" name="capacity" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Current Level (Litres)</label>
                <input type="number" step="0.01" name="current_level" class="form-control" value="0">
            </div>

            <button class="btn btn-success w-100">
                Create Tank
            </button>

        </form>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
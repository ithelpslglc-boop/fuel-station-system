<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

$error = '';

// FETCH FUEL TYPES
$stmt = $pdo->prepare("SELECT * FROM fuel_types WHERE status = 1");
$stmt->execute();
$fuels = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pump_name = trim($_POST['pump_name']);
    $fuel_type_id = $_POST['fuel_type_id'];

    if (empty($pump_name) || empty($fuel_type_id)) {
        $error = "All fields are required";
    } else {

        $stmt = $pdo->prepare("
            INSERT INTO pumps (pump_name, fuel_type_id)
            VALUES (?, ?)
        ");

        $stmt->execute([$pump_name, $fuel_type_id]);

        header("Location: index.php");
        exit;
    }
}
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <h4>Add Pump</h4>

    <div class="card p-3 shadow-sm mt-3" style="max-width:500px;">

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-2">
                <label>Pump Name</label>
                <input type="text" name="pump_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Fuel Type</label>
                <select name="fuel_type_id" class="form-select" required>
                    <option value="">Select Fuel</option>
                    <?php foreach ($fuels as $fuel): ?>
                        <option value="<?= $fuel['id'] ?>">
                            <?= htmlspecialchars($fuel['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button class="btn btn-success w-100">Create Pump</button>

        </form>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
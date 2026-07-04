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

        $stmt->execute([
            $pump_name,
            $fuel_type_id
        ]);

        header("Location: index.php");
        exit;
    }
}
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="page-content">

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h3 class="mb-0">
                Add Pump
            </h3>

            <a href="index.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
                Back
            </a>

        </div>

        <div class="card">

            <div class="card-body">

                <?php if ($error): ?>

                    <div class="alert alert-danger">

                        <?= htmlspecialchars($error) ?>

                    </div>

                <?php endif; ?>

                <form method="POST">

                    <div class="mb-3">

                        <label class="form-label">
                            Pump Name
                        </label>

                        <input
                            type="text"
                            name="pump_name"
                            class="form-control"
                            required>

                    </div>

                    <div class="mb-4">

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

                            <?php foreach ($fuels as $fuel): ?>

                                <option value="<?= $fuel['id'] ?>">

                                    <?= htmlspecialchars($fuel['name']) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <button
                        type="submit"
                        class="btn btn-success">

                        <i class="bi bi-check-circle"></i>

                        Create Pump

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
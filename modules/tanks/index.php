<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

// DELETE TANK (Optional)
if (isset($_GET['delete'])) {

    if ($_SESSION['user_role'] !== 'admin') {
        die("Access denied");
    }

    $id = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM fuel_tanks WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: index.php");
    exit;
}

// FETCH TANKS
$stmt = $pdo->prepare("
    SELECT
        fuel_tanks.*,
        fuel_types.name AS fuel_name
    FROM fuel_tanks
    INNER JOIN fuel_types
        ON fuel_tanks.fuel_type_id = fuel_types.id
    ORDER BY fuel_tanks.id DESC
");

$stmt->execute();

$tanks = $stmt->fetchAll();

?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4>Fuel Tanks</h4>

        <a href="create.php" class="btn btn-primary btn-sm">
            + Add Tank
        </a>

    </div>

    <?php if (empty($tanks)): ?>

        <div class="alert alert-warning">
            No tanks found.
        </div>

    <?php else: ?>

    <div class="card shadow-sm">

        <div class="card-body">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">

                    <tr>

                        <th>ID</th>

                        <th>Tank Name</th>

                        <th>Fuel Type</th>

                        <th>Capacity</th>

                        <th>Current Level</th>

                        <th>Filled</th>

                        <th>Status</th>

                        <th width="180">Actions</th>

                    </tr>

                </thead>

                <tbody>

                <?php foreach ($tanks as $tank): ?>

                    <?php

                    $percentage = 0;

                    if ($tank['capacity'] > 0) {

                        $percentage = ($tank['current_level'] / $tank['capacity']) * 100;

                    }

                    if ($percentage <= 20) {

                        $bar = "bg-danger";

                    } elseif ($percentage <= 60) {

                        $bar = "bg-warning";

                    } else {

                        $bar = "bg-success";

                    }

                    ?>

                    <tr>

                        <td>

                            <?= $tank['id'] ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($tank['tank_name']) ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($tank['fuel_name']) ?>

                        </td>

                        <td>

                            <?= number_format($tank['capacity'],2) ?> L

                        </td>

                        <td>

                            <?= number_format($tank['current_level'],2) ?> L

                        </td>

                        <td width="220">

                            <div class="progress">

                                <div
                                    class="progress-bar <?= $bar ?>"
                                    role="progressbar"
                                    style="width: <?= $percentage ?>%;">

                                    <?= number_format($percentage,1) ?>%

                                </div>

                            </div>

                        </td>

                        <td>

                            <?php if ($tank['status']) : ?>

                                <span class="badge bg-success">

                                    Active

                                </span>

                            <?php else : ?>

                                <span class="badge bg-secondary">

                                    Inactive

                                </span>

                            <?php endif; ?>

                        </td>

                        <td>

                            <a
                                href="edit.php?id=<?= $tank['id'] ?>"
                                class="btn btn-warning btn-sm">

                                Edit

                            </a>

                            <a
                                href="adjust.php?id=<?= $tank['id'] ?>"
                                class="btn btn-info btn-sm">

                                Adjust

                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

    <?php endif; ?>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
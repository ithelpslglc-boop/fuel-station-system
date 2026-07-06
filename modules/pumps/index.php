<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

/*
|--------------------------------------------------------------------------
| FETCH PUMPS WITH TANK + FUEL INFO
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        pumps.*,
        fuel_tanks.tank_name,
        fuel_tanks.current_level,
        fuel_types.name AS fuel_name
    FROM pumps
    INNER JOIN fuel_tanks ON pumps.tank_id = fuel_tanks.id
    INNER JOIN fuel_types ON fuel_tanks.fuel_type_id = fuel_types.id
    ORDER BY pumps.id DESC
");

$stmt->execute();
$pumps = $stmt->fetchAll();
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Pumps</h4>
        <a href="create.php" class="btn btn-primary btn-sm">+ Add Pump</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-bordered table-hover">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Pump Name</th>
                        <th>Tank</th>
                        <th>Fuel Type</th>
                        <th>Tank Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($pumps as $pump): ?>

                        <tr>
                            <td><?= $pump['id'] ?></td>
                            <td><?= htmlspecialchars($pump['pump_name']) ?></td>
                            <td><?= htmlspecialchars($pump['tank_name']) ?></td>
                            <td><?= htmlspecialchars($pump['fuel_name']) ?></td>
                            <td><?= number_format($pump['current_level'], 2) ?> L</td>
                            <td>
                                <?= $pump['status'] ? 'Active' : 'Inactive' ?>
                            </td>
                            <td>
                                <a href="edit.php?id=<?= $pump['id'] ?>" class="btn btn-warning btn-sm">
                                    Edit
                                </a>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>
    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
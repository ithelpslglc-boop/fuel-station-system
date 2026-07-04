<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

// FETCH PUMPS WITH FUEL NAME
$stmt = $pdo->prepare("
    SELECT 
        pumps.*,
        fuel_types.name AS fuel_name
    FROM pumps
    INNER JOIN fuel_types 
        ON pumps.fuel_type_id = fuel_types.id
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

        <a href="create.php" class="btn btn-primary btn-sm">
            + Add Pump
        </a>
    </div>

    <?php if (empty($pumps)): ?>
        <div class="alert alert-warning">
            No pumps found. Please add a pump.
        </div>
    <?php else: ?>

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-bordered table-hover">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Pump Name</th>
                        <th>Fuel Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($pumps as $pump): ?>
                        <tr>
                            <td><?= $pump['id'] ?></td>

                            <td>
                                <?= htmlspecialchars($pump['pump_name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($pump['fuel_name']) ?>
                            </td>

                            <td>
                                <?php if ($pump['status'] == 1): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>

                            <td class="d-flex gap-2">

                                <a href="edit.php?id=<?= $pump['id'] ?>"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <a href="delete.php?id=<?= $pump['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this pump?')">
                                    Delete
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
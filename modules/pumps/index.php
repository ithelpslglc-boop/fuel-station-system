<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

// DELETE PUMP
if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM pumps WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: index.php");
    exit;
}

// FETCH PUMPS
$stmt = $pdo->prepare("
    SELECT pumps.*, fuel_types.name AS fuel_name
    FROM pumps
    JOIN fuel_types ON pumps.fuel_type_id = fuel_types.id
    ORDER BY pumps.id DESC
");
$stmt->execute();
$pumps = $stmt->fetchAll();
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Pump Management</h4>
        <a href="create.php" class="btn btn-primary btn-sm">+ Add Pump</a>
    </div>

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
                            <td><?= htmlspecialchars($pump['pump_name']) ?></td>
                            <td><?= htmlspecialchars($pump['fuel_name']) ?></td>
                            <td><?= $pump['status'] ? 'Active' : 'Inactive' ?></td>
                            <td>

                                <a href="edit.php?id=<?= $pump['id'] ?>" class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <a href="index.php?delete=<?= $pump['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Delete this pump?')">
                                    Delete
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
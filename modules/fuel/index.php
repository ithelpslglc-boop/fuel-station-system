<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

// DELETE FUEL TYPE
if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM fuel_types WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: index.php");
    exit;
}

// FETCH FUEL TYPES
$stmt = $pdo->prepare("
    SELECT *
    FROM fuel_types
    ORDER BY id DESC
");
$stmt->execute();

$fuels = $stmt->fetchAll();
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Fuel Inventory</h4>

        <a href="create.php" class="btn btn-primary btn-sm">
            + Add Fuel Type
        </a>
    </div>

    <?php if (empty($fuels)): ?>
        <div class="alert alert-warning">
            No fuel types found. Please add one.
        </div>
    <?php else: ?>

    <div class="card shadow-sm">

        <div class="card-body">

            <table class="table table-bordered table-hover">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Fuel Name</th>
                        <th>Price / Litre</th>
                        <th>Stock (L)</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($fuels as $fuel): ?>
                        <tr>

                            <td><?= $fuel['id'] ?></td>

                            <td>
                                <?= htmlspecialchars($fuel['name']) ?>
                            </td>

                            <td>
                                <?= number_format($fuel['price_per_liter'], 2) ?>
                            </td>

                            <td>
                                <?= number_format($fuel['current_stock'], 2) ?> L
                            </td>

                            <td>
                                <?php if ($fuel['status'] == 1): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>

                            <td class="d-flex gap-2">

                                <a href="edit.php?id=<?= $fuel['id'] ?>"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <a href="index.php?delete=<?= $fuel['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Delete this fuel type?')">
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
<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

// DELETE SUPPLIER
if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM suppliers WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: index.php");
    exit;
}

// FETCH SUPPLIERS
$stmt = $pdo->prepare("SELECT * FROM suppliers ORDER BY id DESC");
$stmt->execute();
$suppliers = $stmt->fetchAll();
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="page-content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Supplier Management</h4>
        <a href="create.php" class="btn btn-primary btn-sm">+ Add Supplier</a>
    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <table class="table table-bordered table-hover">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($suppliers as $supplier): ?>
                        <tr>
                            <td><?= $supplier['id'] ?></td>
                            <td><?= htmlspecialchars($supplier['name']) ?></td>
                            <td><?= htmlspecialchars($supplier['contact']) ?></td>
                            <td><?= htmlspecialchars($supplier['email']) ?></td>
                            <td><?= $supplier['status'] ? 'Active' : 'Inactive' ?></td>
                            <td>

                                <a href="edit.php?id=<?= $supplier['id'] ?>" class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <a href="index.php?delete=<?= $supplier['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Delete this supplier?')">
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
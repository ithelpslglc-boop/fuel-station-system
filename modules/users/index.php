<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

// DELETE USER (admin only)
if (isset($_GET['delete'])) {

    if ($_SESSION['user_role'] !== 'admin') {
        die("Access denied");
    }

    $id = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: index.php");
    exit;
}

// FETCH USERS
$stmt = $pdo->prepare("
    SELECT *
    FROM users
    ORDER BY id DESC
");
$stmt->execute();

$users = $stmt->fetchAll();
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Users</h4>

        <a href="create.php" class="btn btn-primary btn-sm">
            + Add User
        </a>
    </div>

    <?php if (empty($users)): ?>
        <div class="alert alert-warning">
            No users found.
        </div>
    <?php else: ?>

    <div class="card shadow-sm">

        <div class="card-body">

            <table class="table table-bordered table-hover">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($users as $user): ?>
                        <tr>

                            <td><?= $user['id'] ?></td>

                            <td>
                                <?= htmlspecialchars($user['name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($user['email']) ?>
                            </td>

                            <td>
                                <span class="badge bg-info text-dark">
                                    <?= htmlspecialchars($user['role']) ?>
                                </span>
                            </td>

                            <td>
                                <?php if (!empty($user['status']) && $user['status'] == 1): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>

                            <td class="d-flex gap-2">

                                <a href="edit.php?id=<?= $user['id'] ?>"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <a href="index.php?delete=<?= $user['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this user?')">
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
<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

// DELETE USER (safety included)
if (isset($_GET['delete'])) {

    $deleteId = $_GET['delete'];

    if ($deleteId == $_SESSION['user_id']) {
        die("You cannot delete your own account");
    }

    $del = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $del->execute([$deleteId]);

    header("Location: index.php");
    exit;
}

// FETCH USERS
$stmt = $pdo->prepare("SELECT * FROM users ORDER BY id DESC");
$stmt->execute();
$users = $stmt->fetchAll();
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="page-content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Users</h4>
        <a href="create.php" class="btn btn-primary btn-sm">+ Add User</a>
    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <table class="table table-bordered table-hover mb-0">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= ucfirst($user['role']) ?></td>
                            <td>
                                <?= $user['status'] ? 'Active' : 'Inactive' ?>
                            </td>
                            <td>
                                <?= $user['last_login'] ?? 'Never' ?>
                            </td>

                            <td>
                                <a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">
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

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
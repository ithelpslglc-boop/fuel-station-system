<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

// Fetch User
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name   = trim($_POST['name']);
    $email  = trim($_POST['email']);
    $role   = $_POST['role'];
    $status = $_POST['status'];

    if (empty($name) || empty($email)) {

        $error = "Name and Email are required";

    } else {

        $check = $pdo->prepare("
            SELECT id
            FROM users
            WHERE email = ?
            AND id != ?
        ");

        $check->execute([$email, $id]);

        if ($check->rowCount() > 0) {

            $error = "Email already exists";

        } else {

            $update = $pdo->prepare("
                UPDATE users
                SET
                    name = ?,
                    email = ?,
                    role = ?,
                    status = ?
                WHERE id = ?
            ");

            $update->execute([
                $name,
                $email,
                $role,
                $status,
                $id
            ]);

            header("Location: index.php");
            exit;
        }
    }
}
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="page-content">

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h3 class="mb-0">
                Edit User
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
                            Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="<?= htmlspecialchars($user['name']) ?>"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="<?= htmlspecialchars($user['email']) ?>"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Role
                        </label>

                        <select
                            name="role"
                            class="form-select">

                            <option value="staff" <?= $user['role'] == 'staff' ? 'selected' : '' ?>>
                                Staff
                            </option>

                            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>
                                Admin
                            </option>

                        </select>

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select">

                            <option value="1" <?= $user['status'] == 1 ? 'selected' : '' ?>>
                                Active
                            </option>

                            <option value="0" <?= $user['status'] == 0 ? 'selected' : '' ?>>
                                Inactive
                            </option>

                        </select>

                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-check-circle"></i>

                        Update User

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
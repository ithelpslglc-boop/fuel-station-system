<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

// ONLY ADMIN CAN EDIT USERS
if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

// FETCH USER
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found");
}

$error = '';

// HANDLE UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $status = $_POST['status'];
    $password = $_POST['password'];

    if (empty($name) || empty($email)) {
        $error = "Name and Email are required";
    } else {

        // CHECK EMAIL EXISTS (EXCEPT CURRENT USER)
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);

        if ($stmt->fetch()) {

            $error = "Email already exists";

        } else {

            // IF PASSWORD ENTERED → UPDATE IT
            if (!empty($password)) {

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("
                    UPDATE users
                    SET name = ?, email = ?, password = ?, role = ?, status = ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    $name,
                    $email,
                    $hashedPassword,
                    $role,
                    $status,
                    $id
                ]);

            } else {

                // WITHOUT PASSWORD CHANGE
                $stmt = $pdo->prepare("
                    UPDATE users
                    SET name = ?, email = ?, role = ?, status = ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    $name,
                    $email,
                    $role,
                    $status,
                    $id
                ]);
            }

            header("Location: index.php");
            exit;
        }
    }
}
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Edit User</h4>

        <a href="index.php" class="btn btn-secondary btn-sm">
            ← Back
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="name"
                           class="form-control"
                           value="<?= htmlspecialchars($user['name']) ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email"
                           class="form-control"
                           value="<?= htmlspecialchars($user['email']) ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label>Password (leave blank to keep current)</label>
                    <input type="password" name="password"
                           class="form-control">
                </div>

                <div class="mb-3">
                    <label>Role</label>
                    <select name="role" class="form-select">

                        <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>
                            Admin
                        </option>

                        <option value="staff" <?= $user['role']=='staff'?'selected':'' ?>>
                            Staff
                        </option>

                    </select>
                </div>

                <div class="mb-3">
                    <label>Status</label>
                    <select name="status" class="form-select">

                        <option value="1" <?= $user['status']==1?'selected':'' ?>>
                            Active
                        </option>

                        <option value="0" <?= $user['status']==0?'selected':'' ?>>
                            Inactive
                        </option>

                    </select>
                </div>

                <button class="btn btn-primary w-100">
                    Update User
                </button>

            </form>

        </div>
    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
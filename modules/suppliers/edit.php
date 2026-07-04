<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM suppliers WHERE id = ?");
$stmt->execute([$id]);
$supplier = $stmt->fetch();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $status = $_POST['status'];

    $stmt = $pdo->prepare("
        UPDATE suppliers
        SET name = ?, contact = ?, email = ?, address = ?, status = ?
        WHERE id = ?
    ");

    $stmt->execute([$name, $contact, $email, $address, $status, $id]);

    header("Location: index.php");
    exit;
}
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <h4>Edit Supplier</h4>

    <form method="POST" class="card p-3">

        <div class="mb-2">
            <label>Name</label>
            <input type="text" name="name" class="form-control"
                   value="<?= htmlspecialchars($supplier['name']) ?>" required>
        </div>

        <div class="mb-2">
            <label>Contact</label>
            <input type="text" name="contact" class="form-control"
                   value="<?= htmlspecialchars($supplier['contact']) ?>" required>
        </div>

        <div class="mb-2">
            <label>Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($supplier['email']) ?>">
        </div>

        <div class="mb-3">
            <label>Address</label>
            <textarea name="address" class="form-control"><?= htmlspecialchars($supplier['address']) ?></textarea>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-select">
                <option value="1" <?= $supplier['status'] == 1 ? 'selected' : '' ?>>Active</option>
                <option value="0" <?= $supplier['status'] == 0 ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>

        <button class="btn btn-primary w-100">Update Supplier</button>

    </form>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
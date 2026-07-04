<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM expenses WHERE id = ?");
$stmt->execute([$id]);
$exp = $stmt->fetch();

if (!$exp) {
    die("Expense not found");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title']);
    $amount = $_POST['amount'];
    $date = $_POST['expense_date'];
    $category = $_POST['category'];
    $note = trim($_POST['note']);

    if (empty($title) || empty($amount) || empty($date)) {
        $error = "Required fields missing";
    } else {

        $stmt = $pdo->prepare("
            UPDATE expenses
            SET title = ?, amount = ?, expense_date = ?, category = ?, note = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $title,
            $amount,
            $date,
            $category,
            $note,
            $id
        ]);

        header("Location: index.php");
        exit;
    }
}
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <h4>Edit Expense</h4>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-3">

        <div class="mb-2">
            <label>Title</label>
            <input type="text" name="title" class="form-control"
                   value="<?= htmlspecialchars($exp['title']) ?>" required>
        </div>

        <div class="mb-2">
            <label>Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control"
                   value="<?= $exp['amount'] ?>" required>
        </div>

        <div class="mb-2">
            <label>Date</label>
            <input type="date" name="expense_date" class="form-control"
                   value="<?= $exp['expense_date'] ?>" required>
        </div>

        <div class="mb-2">
            <label>Category</label>
            <select name="category" class="form-select">
                <option value="electricity" <?= $exp['category']=='electricity'?'selected':'' ?>>Electricity</option>
                <option value="maintenance" <?= $exp['category']=='maintenance'?'selected':'' ?>>Maintenance</option>
                <option value="salary" <?= $exp['category']=='salary'?'selected':'' ?>>Salary</option>
                <option value="other" <?= $exp['category']=='other'?'selected':'' ?>>Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Note</label>
            <textarea name="note" class="form-control"><?= htmlspecialchars($exp['note']) ?></textarea>
        </div>

        <button class="btn btn-primary w-100">Update Expense</button>

    </form>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title']);
    $amount = $_POST['amount'];
    $date = $_POST['expense_date'];
    $category = $_POST['category'];
    $note = $_POST['note'];

    if (empty($title) || empty($amount) || empty($date)) {
        $error = "Title, Amount and Date are required";
    } else {

        $stmt = $pdo->prepare("
            INSERT INTO expenses (title, amount, expense_date, category, note)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([$title, $amount, $date, $category, $note]);

        header("Location: index.php");
        exit;
    }
}
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <h4>Add Expense</h4>

    <div class="card p-3 shadow-sm mt-3" style="max-width:500px;">

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-2">
                <label>Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-2">
                <label>Amount</label>
                <input type="number" step="0.01" name="amount" class="form-control" required>
            </div>

            <div class="mb-2">
                <label>Date</label>
                <input type="date" name="expense_date" class="form-control" required>
            </div>

            <div class="mb-2">
                <label>Category</label>
                <select name="category" class="form-select">
                    <option value="electricity">Electricity</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="salary">Salary</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Note</label>
                <textarea name="note" class="form-control"></textarea>
            </div>

            <button class="btn btn-success w-100">Save Expense</button>

        </form>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

// Fetch Expense
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
            SET
                title = ?,
                amount = ?,
                expense_date = ?,
                category = ?,
                note = ?
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

<div class="page-content">

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h3 class="mb-0">
                Edit Expense
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
                            Title
                        </label>

                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            value="<?= htmlspecialchars($exp['title']) ?>"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Amount
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="amount"
                            class="form-control"
                            value="<?= htmlspecialchars($exp['amount']) ?>"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Date
                        </label>

                        <input
                            type="date"
                            name="expense_date"
                            class="form-control"
                            value="<?= htmlspecialchars($exp['expense_date']) ?>"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Category
                        </label>

                        <select
                            name="category"
                            class="form-select">

                            <option value="electricity" <?= $exp['category'] == 'electricity' ? 'selected' : '' ?>>
                                Electricity
                            </option>

                            <option value="maintenance" <?= $exp['category'] == 'maintenance' ? 'selected' : '' ?>>
                                Maintenance
                            </option>

                            <option value="salary" <?= $exp['category'] == 'salary' ? 'selected' : '' ?>>
                                Salary
                            </option>

                            <option value="other" <?= $exp['category'] == 'other' ? 'selected' : '' ?>>
                                Other
                            </option>

                        </select>

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Note
                        </label>

                        <textarea
                            name="note"
                            class="form-control"
                            rows="4"><?= htmlspecialchars($exp['note']) ?></textarea>

                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-check-circle"></i>

                        Update Expense

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
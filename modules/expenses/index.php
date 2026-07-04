<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

$stmt = $pdo->query("
    SELECT * FROM expenses
    ORDER BY expense_date DESC
");
$expenses = $stmt->fetchAll();
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Expenses</h4>
        <a href="create.php" class="btn btn-primary">+ Add Expense</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-bordered table-hover">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Note</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($expenses as $e): ?>
                        <tr>
                            <td><?= $e['id'] ?></td>
                            <td><?= htmlspecialchars($e['title']) ?></td>
                            <td><?= number_format($e['amount'], 2) ?></td>
                            <td><?= $e['expense_date'] ?></td>
                            <td><?= htmlspecialchars($e['category']) ?></td>
                            <td><?= htmlspecialchars($e['note']) ?></td>

                            <td>

                                <a href="edit.php?id=<?= $e['id'] ?>"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <a href="delete.php?id=<?= $e['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Delete this expense?')">
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
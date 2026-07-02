<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

// FETCH EXPENSES
$stmt = $pdo->prepare("SELECT * FROM expenses ORDER BY id DESC");
$stmt->execute();
$expenses = $stmt->fetchAll();
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Expense Management</h4>
        <a href="create.php" class="btn btn-primary btn-sm">+ Add Expense</a>
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
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($expenses as $exp): ?>
                        <tr>
                            <td><?= $exp['id'] ?></td>
                            <td><?= htmlspecialchars($exp['title']) ?></td>
                            <td><?= number_format($exp['amount'], 2) ?></td>
                            <td><?= $exp['expense_date'] ?></td>
                            <td><?= ucfirst($exp['category']) ?></td>
                            <td>

                                <a href="edit.php?id=<?= $exp['id'] ?>" class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <a href="index.php?delete=<?= $exp['id'] ?>"
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
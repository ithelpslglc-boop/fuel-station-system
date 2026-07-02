<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

// DATE FILTER (optional)
$from = $_GET['from'] ?? null;
$to = $_GET['to'] ?? null;

// SALES QUERY
$salesQuery = "
    SELECT SUM(total_amount) as total_sales
    FROM sales
";

if ($from && $to) {
    $salesQuery .= " WHERE DATE(created_at) BETWEEN '$from' AND '$to'";
}

$salesStmt = $pdo->query($salesQuery);
$totalSales = $salesStmt->fetch()['total_sales'] ?? 0;

// EXPENSE QUERY
$expenseQuery = "
    SELECT SUM(amount) as total_expenses
    FROM expenses
";

if ($from && $to) {
    $expenseQuery .= " WHERE expense_date BETWEEN '$from' AND '$to'";
}

$expenseStmt = $pdo->query($expenseQuery);
$totalExpenses = $expenseStmt->fetch()['total_expenses'] ?? 0;

// PROFIT
$profit = $totalSales - $totalExpenses;
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="page-content">

    <h4>Reports Dashboard</h4>

    <!-- FILTER -->
    <form method="GET" class="card p-3 mb-3">
        <div class="row">

            <div class="col-md-4">
                <label>From</label>
                <input type="date" name="from" class="form-control" value="<?= $from ?>">
            </div>

            <div class="col-md-4">
                <label>To</label>
                <input type="date" name="to" class="form-control" value="<?= $to ?>">
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-primary w-100">Filter</button>
            </div>

        </div>
    </form>

    <!-- CARDS -->
    <div class="row">

        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h6>Total Sales</h6>
                <h3><?= number_format($totalSales, 2) ?></h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h6>Total Expenses</h6>
                <h3><?= number_format($totalExpenses, 2) ?></h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h6>Net Profit</h6>
                <h3><?= number_format($profit, 2) ?></h3>
            </div>
        </div>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
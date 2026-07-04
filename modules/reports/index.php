<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

$from = $_GET['from'] ?? null;
$to   = $_GET['to'] ?? null;

/*
|--------------------------------------------------------------------------
| TOTAL SALES
|--------------------------------------------------------------------------
*/
if ($from && $to) {

    $stmt = $pdo->prepare("
        SELECT SUM(total_amount) AS total_sales
        FROM sales
        WHERE DATE(created_at) BETWEEN ? AND ?
    ");
    $stmt->execute([$from, $to]);

} else {

    $stmt = $pdo->query("
        SELECT SUM(total_amount) AS total_sales
        FROM sales
    ");
}

$totalSales = $stmt->fetch()['total_sales'] ?? 0;

/*
|--------------------------------------------------------------------------
| TOTAL EXPENSES
|--------------------------------------------------------------------------
*/
if ($from && $to) {

    $stmt = $pdo->prepare("
        SELECT SUM(amount) AS total_expenses
        FROM expenses
        WHERE expense_date BETWEEN ? AND ?
    ");
    $stmt->execute([$from, $to]);

} else {

    $stmt = $pdo->query("
        SELECT SUM(amount) AS total_expenses
        FROM expenses
    ");
}

$totalExpenses = $stmt->fetch()['total_expenses'] ?? 0;

$profit = $totalSales - $totalExpenses;

/*
|--------------------------------------------------------------------------
| TABLE DATA
|--------------------------------------------------------------------------
*/
if ($from && $to) {

    $salesStmt = $pdo->prepare("
        SELECT * FROM sales
        WHERE DATE(created_at) BETWEEN ? AND ?
        ORDER BY created_at DESC
    ");
    $salesStmt->execute([$from, $to]);

    $expenseStmt = $pdo->prepare("
        SELECT * FROM expenses
        WHERE expense_date BETWEEN ? AND ?
        ORDER BY expense_date DESC
    ");
    $expenseStmt->execute([$from, $to]);

} else {

    $salesStmt = $pdo->query("
        SELECT * FROM sales
        ORDER BY created_at DESC
    ");

    $expenseStmt = $pdo->query("
        SELECT * FROM expenses
        ORDER BY expense_date DESC
    ");
}

$salesData = $salesStmt->fetchAll();
$expenseData = $expenseStmt->fetchAll();
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="page-content">

<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Reports Dashboard</h3>
    </div>

    <!-- FILTER -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="date" name="from" class="form-control" value="<?= $from ?>">
                </div>
                <div class="col-md-4">
                    <input type="date" name="to" class="form-control" value="<?= $to ?>">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- TOGGLE BUTTONS -->
    <div class="mb-3">

        <button id="btnCharts"
            class="btn btn-dark"
            onclick="showView('charts')">
            📊 Charts
        </button>

        <button id="btnTables"
            class="btn btn-outline-dark"
            onclick="showView('tables')">
            📋 Tables
        </button>

    </div>

    <!-- SUMMARY CARDS -->
    <div class="row mb-3">

        <div class="col-md-4">
            <div class="card p-3">
                <h6>Total Sales</h6>
                <h3><?= number_format($totalSales, 2) ?></h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3">
                <h6>Total Expenses</h6>
                <h3><?= number_format($totalExpenses, 2) ?></h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3">
                <h6>Net Profit</h6>
                <h3><?= number_format($profit, 2) ?></h3>
            </div>
        </div>

    </div>

    <!-- CHART VIEW -->
    <div id="chartsView">

        <div class="card">
            <div class="card-body">
                <canvas id="reportChart"></canvas>
            </div>
        </div>

    </div>

    <!-- TABLE VIEW -->
    <div id="tablesView" style="display:none;">

        <div class="row">

            <!-- SALES TABLE -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Sales</div>
                    <div class="card-body table-responsive">

                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($salesData as $s): ?>
                                    <tr>
                                        <td><?= $s['created_at'] ?></td>
                                        <td><?= number_format($s['total_amount'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <!-- EXPENSE TABLE -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Expenses</div>
                    <div class="card-body table-responsive">

                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($expenseData as $e): ?>
                                    <tr>
                                        <td><?= $e['expense_date'] ?></td>
                                        <td><?= number_format($e['amount'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>

                        </table>

                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

</div>

<!-- CHART + TOGGLE SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Chart(document.getElementById('reportChart'), {
    type: 'bar',
    data: {
        labels: ['Sales', 'Expenses', 'Profit'],
        datasets: [{
            data: [
                <?= (float)$totalSales ?>,
                <?= (float)$totalExpenses ?>,
                <?= (float)$profit ?>
            ],
            backgroundColor: ['#0d6efd','#dc3545','#198754']
        }]
    }
});

function showView(view) {

    const charts = document.getElementById('chartsView');
    const tables = document.getElementById('tablesView');

    const btnCharts = document.getElementById('btnCharts');
    const btnTables = document.getElementById('btnTables');

    if (view === 'charts') {

        charts.style.display = 'block';
        tables.style.display = 'none';

        btnCharts.classList.add('btn-dark');
        btnCharts.classList.remove('btn-outline-dark');

        btnTables.classList.add('btn-outline-dark');
        btnTables.classList.remove('btn-dark');

    } else {

        charts.style.display = 'none';
        tables.style.display = 'block';

        btnTables.classList.add('btn-dark');
        btnTables.classList.remove('btn-outline-dark');

        btnCharts.classList.add('btn-outline-dark');
        btnCharts.classList.remove('btn-dark');
    }
}

/* default view */
document.addEventListener('DOMContentLoaded', function () {
    showView('charts');
});
</script>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
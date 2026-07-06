<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

// DATE FILTER
$from = $_GET['from'] ?? null;
$to = $_GET['to'] ?? null;

// BASE QUERIES
$salesQuery = "SELECT SUM(total_amount) as total_sales FROM sales";
$expenseQuery = "SELECT SUM(amount) as total_expenses FROM expenses";

$params = [];

if ($from && $to) {
    $salesQuery .= " WHERE DATE(created_at) BETWEEN ? AND ?";
    $expenseQuery .= " WHERE expense_date BETWEEN ? AND ?";
    $params = [$from, $to];
}

// SALES
$stmt = $pdo->prepare($salesQuery);
$stmt->execute($params);
$totalSales = $stmt->fetch()['total_sales'] ?? 0;

// EXPENSES
$stmt = $pdo->prepare($expenseQuery);
$stmt->execute($params);
$totalExpenses = $stmt->fetch()['total_expenses'] ?? 0;

// PROFIT
$profit = $totalSales - $totalExpenses;

// TABLE DATA
$stmt = $pdo->prepare("
    SELECT 'Sale' as type, created_at as date, total_amount as amount
    FROM sales
    UNION ALL
    SELECT 'Expense' as type, expense_date as date, amount
    FROM expenses
    ORDER BY date DESC
");

$stmt->execute();
$records = $stmt->fetchAll();
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="main-content">

    <h4>Reports Dashboard</h4>

    <!-- FILTER -->
    <form method="GET" class="card p-3 mb-3">
        <div class="row">

            <div class="col-md-4">
                <label>From</label>
                <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($from) ?>">
            </div>

            <div class="col-md-4">
                <label>To</label>
                <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($to) ?>">
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-primary w-100">Filter</button>
            </div>

        </div>
    </form>

    <!-- TOGGLE BUTTONS -->
    <div class="d-flex gap-2 mb-3">

        <button class="btn btn-primary view-btn active" onclick="showView('charts', this)">
            Charts
        </button>

        <button class="btn btn-outline-primary view-btn" onclick="showView('table', this)">
            Table
        </button>

    </div>

    <!-- =======================
         CHART VIEW
    ======================== -->
    <div id="chartsView">

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

        <!-- BAR CHART -->
        <div class="card p-3 mt-3">

            <h6>Performance Overview</h6>

            <canvas id="salesChart" height="100"></canvas>

        </div>

    </div>

    <!-- =======================
         TABLE VIEW
    ======================== -->
    <div id="tableView" style="display:none;">

        <div class="card shadow-sm">

            <div class="card-body">

                <table class="table table-bordered table-hover">

                    <thead class="table-dark">
                        <tr>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Amount</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($records as $r): ?>
                            <tr>

                                <td>
                                    <?php if ($r['type'] == 'Sale'): ?>
                                        <span class="badge bg-success">Sale</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Expense</span>
                                    <?php endif; ?>
                                </td>

                                <td><?= htmlspecialchars($r['date']) ?></td>

                                <td><?= number_format($r['amount'], 2) ?></td>

                            </tr>
                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<!-- JS -->
<script>
function showView(view, btn) {

    document.getElementById('chartsView').style.display =
        (view === 'charts') ? 'block' : 'none';

    document.getElementById('tableView').style.display =
        (view === 'table') ? 'block' : 'none';

    document.querySelectorAll('.view-btn').forEach(b => {
        b.classList.remove('btn-primary');
        b.classList.add('btn-outline-primary');
        b.classList.remove('active');
    });

    btn.classList.add('btn-primary');
    btn.classList.remove('btn-outline-primary');
    btn.classList.add('active');
}

/*
|--------------------------------------------------------------------------
| CHART.JS BAR CHART
|--------------------------------------------------------------------------
*/

const ctx = document.getElementById('salesChart').getContext('2d');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Sales', 'Expenses'],
        datasets: [{
            label: 'Amount',
            data: [<?= $totalSales ?>, <?= $totalExpenses ?>],
            backgroundColor: [
                'rgba(40, 167, 69, 0.7)',
                'rgba(220, 53, 69, 0.7)'
            ],
            borderColor: [
                'rgba(40, 167, 69, 1)',
                'rgba(220, 53, 69, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
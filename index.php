<?php
require_once 'config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

$today = date('Y-m-d');

/* =======================
   TODAY SALES + LITERS
======================= */
$stmt = $pdo->prepare("
    SELECT 
        SUM(total_amount) as today_sales,
        SUM(liters) as today_liters
    FROM sales
    WHERE DATE(created_at) = ?
");
$stmt->execute([$today]);
$salesData = $stmt->fetch();

$todaySales = $salesData['today_sales'] ?? 0;
$todayLiters = $salesData['today_liters'] ?? 0;

/* =======================
   TODAY EXPENSES
======================= */
$stmt = $pdo->prepare("
    SELECT SUM(amount) as today_expenses
    FROM expenses
    WHERE expense_date = ?
");
$stmt->execute([$today]);
$todayExpenses = $stmt->fetch()['today_expenses'] ?? 0;

/* =======================
   PROFIT
======================= */
$todayProfit = $todaySales - $todayExpenses;

/* =======================
   LOW FUEL ALERTS
======================= */
$stmt = $pdo->prepare("
    SELECT * FROM fuel_types
    WHERE current_stock <= 100
    AND status = 1
    ORDER BY current_stock ASC
");
$stmt->execute();
$lowFuel = $stmt->fetchAll();

/* =======================
   SYSTEM COUNTS
======================= */
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalPumps = $pdo->query("SELECT COUNT(*) FROM pumps")->fetchColumn();
$totalSuppliers = $pdo->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();

/* =======================
   WEEKLY DATA (7 DAYS)
======================= */
$weekLabels = [];
$weekSales = [];
$weekExpenses = [];

for ($i = 6; $i >= 0; $i--) {

    $date = date('Y-m-d', strtotime("-$i days"));
    $weekLabels[] = date('D', strtotime($date));

    // SALES
    $stmt = $pdo->prepare("
        SELECT SUM(total_amount) as total
        FROM sales
        WHERE DATE(created_at) = ?
    ");
    $stmt->execute([$date]);
    $weekSales[] = $stmt->fetch()['total'] ?? 0;

    // EXPENSES
    $stmt = $pdo->prepare("
        SELECT SUM(amount) as total
        FROM expenses
        WHERE expense_date = ?
    ");
    $stmt->execute([$date]);
    $weekExpenses[] = $stmt->fetch()['total'] ?? 0;
}
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="page-content">

    <h4 class="mb-3">Dashboard</h4>

    <!-- LOW FUEL ALERT -->
    <?php if (count($lowFuel) > 0): ?>
        <div class="alert alert-danger">
            <strong>⚠ Low Fuel Alert</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($lowFuel as $fuel): ?>
                    <li>
                        <?= htmlspecialchars($fuel['name']) ?>
                        - <?= number_format($fuel['current_stock'],2) ?> L remaining
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- KPI CARDS -->
    <div class="row g-3">

        <div class="col-md-3">
            <div class="card bg-success text-white p-3">
                <h6>Today's Sales</h6>
                <h3><?= number_format($todaySales, 2) ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-primary text-white p-3">
                <h6>Fuel Sold (L)</h6>
                <h3><?= number_format($todayLiters, 2) ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-danger text-white p-3">
                <h6>Today's Expenses</h6>
                <h3><?= number_format($todayExpenses, 2) ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-dark p-3">
                <h6>Today's Profit</h6>
                <h3><?= number_format($todayProfit, 2) ?></h3>
            </div>
        </div>

    </div>

    <!-- SYSTEM STATS -->
    <div class="row g-3 mt-2">

        <div class="col-md-4">
            <div class="card bg-dark text-white p-3">
                <h6>Total Users</h6>
                <h3><?= $totalUsers ?></h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-dark text-white p-3">
                <h6>Total Pumps</h6>
                <h3><?= $totalPumps ?></h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-dark text-white p-3">
                <h6>Total Suppliers</h6>
                <h3><?= $totalSuppliers ?></h3>
            </div>
        </div>

    </div>

    <!-- WEEKLY CHART -->
    <div class="card mt-4 p-3">
        <h5>Weekly Sales vs Expenses</h5>
        <canvas id="weeklyChart"></canvas>
    </div>

</div>

<!-- CHART JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const labels = <?= json_encode($weekLabels) ?>;
const sales = <?= json_encode($weekSales) ?>;
const expenses = <?= json_encode($weekExpenses) ?>;

new Chart(document.getElementById('weeklyChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Sales',
                data: sales,
                backgroundColor: '#198754'
            },
            {
                label: 'Expenses',
                data: expenses,
                backgroundColor: '#dc3545'
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
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
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="page-content">

    <h4>Dashboard</h4>

    <!-- 🚨 LOW FUEL ALERT -->
    <?php if (count($lowFuel) > 0): ?>
        <div class="alert alert-danger">
            <strong>⚠ Low Fuel Alert</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($lowFuel as $fuel): ?>
                    <li>
                        <?= htmlspecialchars($fuel['name']) ?>
                        - <?= $fuel['current_stock'] ?> L remaining
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- SUMMARY CARDS -->
    <div class="row">

        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <h6>Today's Sales</h6>
                <h3><?= number_format($todaySales, 2) ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <h6>Fuel Sold Today (L)</h6>
                <h3><?= number_format($todayLiters, 2) ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <h6>Today's Expenses</h6>
                <h3><?= number_format($todayExpenses, 2) ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <h6>Today's Profit</h6>
                <h3><?= number_format($todayProfit, 2) ?></h3>
            </div>
        </div>

    </div>

    <!-- SECOND ROW -->
    <div class="row mt-3">

        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h6>Total Users</h6>
                <h3><?= $totalUsers ?></h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h6>Total Pumps</h6>
                <h3><?= $totalPumps ?></h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h6>Total Suppliers</h6>
                <h3><?= $totalSuppliers ?></h3>
            </div>
        </div>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
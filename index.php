<?php
require_once 'config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

$today = date('Y-m-d');

/* =========================
   TODAY SALES
========================= */
$stmt = $pdo->prepare("
    SELECT 
        SUM(total_amount) as today_sales,
        SUM(liters) as today_liters
    FROM sales
    WHERE DATE(created_at) = ?
");
$stmt->execute([$today]);
$sales = $stmt->fetch();

$todaySales = $sales['today_sales'] ?? 0;
$todayLiters = $sales['today_liters'] ?? 0;

/* =========================
   TODAY EXPENSES
========================= */
$stmt = $pdo->prepare("
    SELECT SUM(amount) as today_expenses
    FROM expenses
    WHERE expense_date = ?
");
$stmt->execute([$today]);
$todayExpenses = $stmt->fetch()['today_expenses'] ?? 0;

/* =========================
   PROFIT
========================= */
$todayProfit = $todaySales - $todayExpenses;

/* =========================
   LOW FUEL ALERT (SAFE MODE)
   - DOES NOT BREAK IF COLUMN MISSING
========================= */

$lowFuel = [];

try {

    // Try OLD system first
    $stmt = $pdo->query("SELECT * FROM fuel_types");
    $fuels = $stmt->fetchAll();

    foreach ($fuels as $fuel) {

        // safely read stock if exists
        $stock = $fuel['current_stock'] ?? null;

        // if column exists and value is valid
        if ($stock !== null && $stock <= 100) {
            $lowFuel[] = $fuel;
        }
    }

} catch (Exception $e) {
    $fuels = [];
}

/* =========================
   COUNTS
========================= */
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalPumps = $pdo->query("SELECT COUNT(*) FROM pumps")->fetchColumn();
$totalSuppliers = $pdo->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <h4>Dashboard</h4>

    <!-- LOW FUEL ALERT -->
    <?php if (!empty($lowFuel)): ?>
        <div class="alert alert-danger">
            <strong>⚠ Low Fuel Alert</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($lowFuel as $fuel): ?>
                    <li>
                        <?= htmlspecialchars($fuel['name'] ?? 'Fuel') ?> -
                        <?= htmlspecialchars($fuel['current_stock'] ?? 0) ?> L remaining
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- CARDS -->
    <div class="row">

        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <h6>Today's Sales</h6>
                <h3><?= number_format($todaySales, 2) ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <h6>Fuel Sold (L)</h6>
                <h3><?= number_format($todayLiters, 2) ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <h6>Expenses</h6>
                <h3><?= number_format($todayExpenses, 2) ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <h6>Profit</h6>
                <h3><?= number_format($todayProfit, 2) ?></h3>
            </div>
        </div>

    </div>

    <!-- SECOND ROW -->
    <div class="row mt-3">

        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h6>Users</h6>
                <h3><?= $totalUsers ?></h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h6>Pumps</h6>
                <h3><?= $totalPumps ?></h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h6>Suppliers</h6>
                <h3><?= $totalSuppliers ?></h3>
            </div>
        </div>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
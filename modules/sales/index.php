<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

// FETCH SALES
$stmt = $pdo->prepare("
    SELECT sales.*, 
           pumps.pump_name,
           fuel_types.name AS fuel_name
    FROM sales
    JOIN pumps ON sales.pump_id = pumps.id
    JOIN fuel_types ON sales.fuel_type_id = fuel_types.id
    ORDER BY sales.id DESC
");
$stmt->execute();
$sales = $stmt->fetchAll();
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Sales & Receipts</h4>
        <a href="create.php" class="btn btn-primary btn-sm">+ New Sale</a>
    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <table class="table table-bordered table-hover">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Pump</th>
                        <th>Fuel</th>
                        <th>Liters</th>
                        <th>Price/L</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td><?= $sale['id'] ?></td>
                            <td><?= htmlspecialchars($sale['pump_name']) ?></td>
                            <td><?= htmlspecialchars($sale['fuel_name']) ?></td>
                            <td><?= $sale['liters'] ?></td>
                            <td><?= number_format($sale['price_per_liter'], 2) ?></td>
                            <td><?= number_format($sale['total_amount'], 2) ?></td>
                            <td><?= ucfirst($sale['payment_method']) ?></td>
                            <td><?= $sale['created_at'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
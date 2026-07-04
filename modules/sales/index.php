<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

$stmt = $pdo->prepare("
    SELECT 
        sales.*,
        fuel_types.name AS fuel_name,
        pumps.pump_name
    FROM sales
    INNER JOIN fuel_types ON sales.fuel_type_id = fuel_types.id
    LEFT JOIN pumps ON sales.pump_id = pumps.id
    ORDER BY sales.id DESC
");
$stmt->execute();
$sales = $stmt->fetchAll();
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Sales</h4>
        <a href="create.php" class="btn btn-primary">+ New Sale</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-bordered table-hover">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Fuel</th>
                        <th>Pump</th>
                        <th>Liters</th>
                        <th>Price/L</th>
                        <th>Total</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($sales as $s): ?>
                        <tr>
                            <td><?= $s['id'] ?></td>
                            <td><?= htmlspecialchars($s['fuel_name']) ?></td>
                            <td><?= htmlspecialchars($s['pump_name'] ?? '-') ?></td>
                            <td><?= $s['liters'] ?></td>
                            <td><?= number_format($s['price_per_liter'], 2) ?></td>
                            <td><?= number_format($s['total_amount'], 2) ?></td>
                            <td><?= $s['created_at'] ?></td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>
    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
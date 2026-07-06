<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/fuel_functions.php';

checkAuth();

/*
|--------------------------------------------------------------------------
| DELETE SALE (WITH STOCK REVERSAL)
|--------------------------------------------------------------------------
*/

if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    /*
    |--------------------------------------------------------------------------
    | GET SALE INFO
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT sales.*, pumps.tank_id
        FROM sales
        INNER JOIN pumps ON sales.pump_id = pumps.id
        WHERE sales.id = ?
    ");

    $stmt->execute([$id]);
    $sale = $stmt->fetch();

    if ($sale) {

        /*
        |--------------------------------------------------------------------------
        | RESTORE STOCK
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            UPDATE fuel_tanks
            SET current_level = current_level + ?
            WHERE id = ?
        ");

        $stmt->execute([
            $sale['liters'],
            $sale['tank_id']
        ]);

        /*
        |--------------------------------------------------------------------------
        | DELETE SALE
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("DELETE FROM sales WHERE id = ?");
        $stmt->execute([$id]);

        /*
        |--------------------------------------------------------------------------
        | SYNC INVENTORY
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            SELECT fuel_type_id
            FROM fuel_tanks
            WHERE id = ?
        ");

        $stmt->execute([$sale['tank_id']]);
        $fuel_type_id = $stmt->fetchColumn();

        updateFuelStock($pdo, $fuel_type_id);
    }

    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| FETCH SALES
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT sales.*, pumps.pump_name, fuel_types.name AS fuel_name
    FROM sales
    INNER JOIN pumps ON sales.pump_id = pumps.id
    INNER JOIN fuel_types ON sales.fuel_type_id = fuel_types.id
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
        <a href="create.php" class="btn btn-primary btn-sm">+ New Sale</a>
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
                        <th>Price</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($sales as $sale): ?>

                        <tr>
                            <td><?= $sale['id'] ?></td>
                            <td><?= htmlspecialchars($sale['fuel_name']) ?></td>
                            <td><?= htmlspecialchars($sale['pump_name']) ?></td>
                            <td><?= number_format($sale['liters'], 2) ?></td>
                            <td><?= number_format($sale['price_per_liter'], 2) ?></td>
                            <td><?= number_format($sale['total_amount'], 2) ?></td>
                            <td><?= $sale['created_at'] ?></td>

                            <td>

                                <a href="edit.php?id=<?= $sale['id'] ?>"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <a href="index.php?delete=<?= $sale['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Delete this sale? This will restore stock!')">
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
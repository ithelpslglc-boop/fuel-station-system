<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

$stmt = $pdo->prepare("
    SELECT 
        fuel_tanks.*,
        fuel_types.name AS fuel_name
    FROM fuel_tanks
    INNER JOIN fuel_types 
        ON fuel_tanks.fuel_type_id = fuel_types.id
    ORDER BY fuel_tanks.id DESC
");
$stmt->execute();
$tanks = $stmt->fetchAll();
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <h4>Fuel Tanks</h4>

    <a href="create.php" class="btn btn-success mb-3">+ Add Tank</a>

    <?php if (empty($tanks)): ?>
        <div class="alert alert-warning">
            No tanks found. Please add a tank first.
        </div>
    <?php else: ?>

    <div class="row g-3">

        <?php foreach ($tanks as $tank): ?>

            <?php
                $percent = ($tank['capacity'] > 0)
                    ? ($tank['current_level'] / $tank['capacity']) * 100
                    : 0;

                $color = $percent < 20 ? 'danger' : ($percent < 50 ? 'warning' : 'success');
            ?>

            <div class="col-md-4">

                <div class="card p-3 shadow-sm">

                    <h6><?= htmlspecialchars($tank['tank_name']) ?></h6>

                    <small class="text-muted">
                        <?= htmlspecialchars($tank['fuel_name']) ?>
                    </small>

                    <h4 class="text-<?= $color ?>">
                        <?= number_format($tank['current_level'], 2) ?> L
                    </h4>

                    <div class="progress mb-2">
                        <div class="progress-bar bg-<?= $color ?>"
                             style="width: <?= $percent ?>%">
                        </div>
                    </div>

                    <small><?= round($percent) ?>% full</small>

                    <div class="mt-3 d-flex gap-2">

                        <a href="edit.php?id=<?= $tank['id'] ?>"
                           class="btn btn-primary btn-sm">
                            Edit
                        </a>

                        <a href="adjust.php?id=<?= $tank['id'] ?>"
                           class="btn btn-warning btn-sm">
                            Adjust
                        </a>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

    <?php endif; ?>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
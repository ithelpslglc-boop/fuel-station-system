<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

checkAuth();

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

/* FETCH TANK */
$stmt = $pdo->prepare("
    SELECT * FROM fuel_tanks WHERE id = ?
");
$stmt->execute([$id]);
$tank = $stmt->fetch();

if (!$tank) {
    die("Tank not found");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $type = $_POST['type']; // add or reduce
    $amount = (float) $_POST['amount'];
    $note = trim($_POST['note']);

    if ($amount <= 0) {
        $error = "Invalid amount";
    } else {

        if ($type === 'add') {
            $newLevel = $tank['current_level'] + $amount;
        } else {
            $newLevel = $tank['current_level'] - $amount;

            if ($newLevel < 0) {
                $error = "Cannot reduce below zero";
            }
        }

        if (!$error) {

            $stmt = $pdo->prepare("
                UPDATE fuel_tanks
                SET current_level = ?
                WHERE id = ?
            ");

            $stmt->execute([$newLevel, $id]);

            header("Location: index.php");
            exit;
        }
    }
}
?>

<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php include ROOT_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">

    <h4>Adjust Tank</h4>

    <div class="card p-3 shadow-sm mt-3" style="max-width:600px;">

        <p><strong>Tank:</strong> <?= htmlspecialchars($tank['tank_name']) ?></p>
        <p><strong>Current Level:</strong> <?= $tank['current_level'] ?> L</p>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label>Action</label>
                <select name="type" class="form-select">
                    <option value="add">Add Fuel</option>
                    <option value="reduce">Reduce Fuel</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Amount (Litres)</label>
                <input type="number" step="0.01" name="amount" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Note (optional)</label>
                <textarea name="note" class="form-control"></textarea>
            </div>

            <button class="btn btn-warning w-100">
                Apply Adjustment
            </button>

        </form>

    </div>

</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
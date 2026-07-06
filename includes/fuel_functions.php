<?php

/**
 * --------------------------------------------------------
 * Update Fuel Stock
 * --------------------------------------------------------
 * Recalculates the total stock for a fuel type based on
 * all active tanks assigned to that fuel type.
 */

function updateFuelStock(PDO $pdo, int $fuelTypeId): void
{
    // Calculate total fuel in all active tanks
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(current_level), 0) AS total_stock
        FROM fuel_tanks
        WHERE fuel_type_id = ?
          AND status = 1
    ");

    $stmt->execute([$fuelTypeId]);

    $total = $stmt->fetchColumn();

    // Update fuel inventory
    $stmt = $pdo->prepare("
        UPDATE fuel_types
        SET current_stock = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $total,
        $fuelTypeId
    ]);
}
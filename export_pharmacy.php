<?php
require_once 'auth.php';
require_role('admin');

try {
    $stmt = $pdo->query("SELECT * FROM pharmacy_items ORDER BY item_name ASC");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($items)) {
        die("No items to export.");
    }

    $filename = "pharmacy_inventory_" . date('Y-m-d') . ".csv";

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    // Headers
    fputcsv($output, ['ID', 'Item Name', 'Category', 'Stock', 'Price', 'Created At']);

    // Data
    foreach ($items as $item) {
        fputcsv($output, [
            $item['id'],
            $item['item_name'],
            $item['category'],
            $item['stock'],
            $item['price'],
            $item['created_at']
        ]);
    }

    fclose($output);
    log_activity('Export Pharmacy CSV', 'Reporting', "Exported pharmacy inventory to CSV.");
    exit;
} catch (PDOException $e) {
    die("Export failed: " . $e->getMessage());
}
?>
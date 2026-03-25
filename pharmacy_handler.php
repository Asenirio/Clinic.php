<?php
header('Content-Type: application/json');
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

if (!validate_csrf($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Security token invalid.']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    // ---- ADD ----
    if ($action === 'add') {
        $name        = trim($_POST['item_name']   ?? '');
        $category    = trim($_POST['category']    ?? 'Medicine');
        $stock       = (int)($_POST['stock']      ?? 0);
        $price       = (float)($_POST['price']    ?? 0);
        $expiry_date = trim($_POST['expiry_date'] ?? '') ?: null;
        $description = trim($_POST['description'] ?? '') ?: null;

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Item name is required.']);
            exit;
        }

        $stmt = $pdo->prepare(
            "INSERT INTO pharmacy_items (item_name, category, stock, price, expiry_date, description)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$name, $category, $stock, $price, $expiry_date, $description]);
        log_activity('Add Item', 'Pharmacy', "Added '{$name}' ({$stock} units) to inventory.");
        echo json_encode(['success' => true, 'message' => "'{$name}' added to inventory."]);

    // ---- EDIT ----
    } elseif ($action === 'edit') {
        $id          = (int)($_POST['id']         ?? 0);
        $name        = trim($_POST['item_name']   ?? '');
        $category    = trim($_POST['category']    ?? 'Medicine');
        $stock       = (int)($_POST['stock']      ?? 0);
        $price       = (float)($_POST['price']    ?? 0);
        $expiry_date = trim($_POST['expiry_date'] ?? '') ?: null;
        $description = trim($_POST['description'] ?? '') ?: null;

        if ($id <= 0 || empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
            exit;
        }

        $stmt = $pdo->prepare(
            "UPDATE pharmacy_items
             SET item_name = ?, category = ?, stock = ?, price = ?, expiry_date = ?, description = ?
             WHERE id = ?"
        );
        $stmt->execute([$name, $category, $stock, $price, $expiry_date, $description, $id]);
        log_activity('Edit Item', 'Pharmacy', "Updated item ID {$id}: '{$name}'.");
        echo json_encode(['success' => true, 'message' => "'{$name}' updated successfully."]);

    // ---- DELETE ----
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid item ID.']);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM pharmacy_items WHERE id = ?");
        $stmt->execute([$id]);
        log_activity('Delete Item', 'Pharmacy', "Removed item ID {$id} from inventory.");
        echo json_encode(['success' => true, 'message' => 'Item removed from inventory.']);

    } else {
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
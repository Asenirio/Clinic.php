<?php
header('Content-Type: application/json');
require_once 'auth.php';

// Only admins can manage specialties
if (!has_role('admin')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!validate_csrf($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Security token invalid.']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $icon = trim($_POST['icon'] ?? 'fa-stethoscope');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Specialty name is required.']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO specialties (name, icon, description) VALUES (?, ?, ?)");
        $stmt->execute([$name, $icon, $description]);
        
        log_activity('Add Specialty', 'Management', "Created new specialty: {$name}");
        echo json_encode(['success' => true, 'message' => "Specialty '{$name}' created successfully."]);

    } elseif ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $icon = trim($_POST['icon'] ?? 'fa-stethoscope');
        $description = trim($_POST['description'] ?? '');

        if ($id <= 0 || empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE specialties SET name = ?, icon = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $icon, $description, $id]);

        log_activity('Edit Specialty', 'Management', "Updated specialty ID {$id}: {$name}");
        echo json_encode(['success' => true, 'message' => "Specialty updated successfully."]);

    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid specialty ID.']);
            exit;
        }

        // Check if any doctors are assigned to this specialty
        $check = $pdo->prepare("SELECT COUNT(*) FROM doctors WHERE specialty_id = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete specialty: Doctors are still assigned to it.']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM specialties WHERE id = ?");
        $stmt->execute([$id]);

        log_activity('Delete Specialty', 'Management', "Removed specialty ID {$id}");
        echo json_encode(['success' => true, 'message' => 'Specialty removed successfully.']);

    } else {
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

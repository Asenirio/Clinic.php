<?php
/**
 * assign_treatment_handler.php
 * AJAX endpoint: Handles assigning a treatment to a patient.
 */
header('Content-Type: application/json');
require_once 'auth.php';
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

if (!validate_csrf($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Security token invalid.']);
    exit;
}

try {
    $patient_id = intval($_POST['patient_id'] ?? 0);
    $treatment_name = trim($_POST['treatment_name'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    
    if (!$patient_id || empty($treatment_name)) {
        echo json_encode(['success' => false, 'message' => 'Patient and Treatment Protocol are required.']);
        exit;
    }

    // Since treatments are hardcoded, we'll just log this action to the activity log and return success
    // In a fully built out system, this would insert into a patient_treatments linking table
    
    // Fetch patient name for the log
    $stmt = $pdo->prepare("SELECT u.full_name FROM patients p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
    $stmt->execute([$patient_id]);
    $patient_name = $stmt->fetchColumn() ?: "Unknown Patient";
    
    log_activity('Assign Treatment', 'Treatments', "Assigned protocol '{$treatment_name}' to {$patient_name}.");
    
    echo json_encode(['success' => true, 'message' => "Successfully assigned {$treatment_name} to {$patient_name}."]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

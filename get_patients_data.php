<?php
/**
 * get_patients_data.php
 * AJAX endpoint: returns patients for the treatment assignment modal.
 */
header('Content-Type: application/json');
require_once 'auth.php';
require_once 'db.php';

try {
    $patients = $pdo->query("
        SELECT p.id, u.full_name
        FROM patients p
        JOIN users u ON p.user_id = u.id
        ORDER BY u.full_name ASC
    ")->fetchAll();

    echo json_encode([
        'success'  => true,
        'patients' => $patients
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch patients']);
}
?>

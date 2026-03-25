<?php
header('Content-Type: application/json');
require_once 'auth.php';

try {
    $doctors = $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
    $patients = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
    $appointments = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
    $pharmacy = $pdo->query("SELECT COUNT(*) FROM pharmacy_items")->fetchColumn();

    echo json_encode([
        'success' => true,
        'stats' => [
            'doctors' => $doctors,
            'patients' => $patients,
            'appointments' => $appointments,
            'pharmacy' => $pharmacy
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
<?php
/**
 * get_form_data.php
 * AJAX endpoint: returns doctors and treatments for the appointment booking modal.
 */
header('Content-Type: application/json');
require_once 'auth.php';

try {
    $doctors = $pdo->query("
        SELECT d.id, u.full_name, s.name as specialty
        FROM doctors d
        JOIN users u ON d.user_id = u.id
        LEFT JOIN specialties s ON d.specialty_id = s.id
        ORDER BY u.full_name ASC
    ")->fetchAll();

    $treatments = $pdo->query("
        SELECT id, name, cost
        FROM treatments
        ORDER BY name ASC
    ")->fetchAll();

    echo json_encode([
        'success'    => true,
        'doctors'    => $doctors,
        'treatments' => $treatments,
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

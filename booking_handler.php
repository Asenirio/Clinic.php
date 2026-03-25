<?php
/**
 * booking_handler.php
 * Handles appointment booking form submissions via AJAX.
 */
header('Content-Type: application/json');
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// CSRF check
if (!validate_csrf($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Security token expired. Please refresh.']);
    exit;
}

$doctor_id        = intval($_POST['doctor_id'] ?? 0);
$treatment_id     = intval($_POST['treatment_id'] ?? 0);
$appointment_date = $_POST['appointment_date'] ?? '';
$notes            = trim($_POST['notes'] ?? '');
$patient_name     = trim($_POST['patient_name'] ?? '');

if (empty($doctor_id) || empty($appointment_date)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

try {
    // Resolve patient_id:
    // If the logged-in user is a patient, use their patient record.
    // If admin/staff/doctor is booking, find or create a walk-in patient entry.
    $patient_id = null;

    if ($_SESSION['role'] === 'patient') {
        // Find the patient record for the logged-in user
        $stmt = $pdo->prepare("SELECT id FROM patients WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $patient_id = $stmt->fetchColumn();

        if (!$patient_id) {
            // Create a patient record for this user if it doesn't exist yet
            $stmt = $pdo->prepare("INSERT INTO patients (user_id, status) VALUES (?, 'Active')");
            $stmt->execute([$_SESSION['user_id']]);
            $patient_id = $pdo->lastInsertId();
        }
    } else {
        // Admin/staff booking: look up or create a walk-in patient by name
        if (!empty($patient_name)) {
            // Try to find an existing user with this name who is a patient
            $stmt = $pdo->prepare("SELECT p.id FROM patients p JOIN users u ON p.user_id = u.id WHERE u.full_name = ? LIMIT 1");
            $stmt->execute([$patient_name]);
            $patient_id = $stmt->fetchColumn();

            if (!$patient_id) {
                // Create a walk-in user + patient record
                $pdo->beginTransaction();
                $walkin_username = 'walkin_' . time() . '_' . rand(100, 999);
                $walkin_password = password_hash('walkin123', PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, email, role) VALUES (?, ?, ?, ?, 'patient')");
                $stmt->execute([$walkin_username, $walkin_password, $patient_name, $walkin_username . '@walkin.local']);
                $new_user_id = $pdo->lastInsertId();

                $stmt = $pdo->prepare("INSERT INTO patients (user_id, status) VALUES (?, 'Active')");
                $stmt->execute([$new_user_id]);
                $patient_id = $pdo->lastInsertId();
                $pdo->commit();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Patient name is required.']);
            exit;
        }
    }

    // Insert the appointment
    $stmt = $pdo->prepare("
        INSERT INTO appointments (patient_id, doctor_id, treatment_id, appointment_date, notes, status)
        VALUES (?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([$patient_id, $doctor_id, $treatment_id ?: null, $appointment_date, $notes]);

    log_activity('Book Appointment', 'Appointments', "Appointment booked for patient_id={$patient_id}, doctor_id={$doctor_id}.");
    echo json_encode(['success' => true, 'message' => 'Appointment booked successfully!']);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
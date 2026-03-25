<?php
/**
 * doctor_handler.php
 * Handles adding and deleting doctors via AJAX.
 */
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
    if ($action === 'add') {
        $full_name    = trim($_POST['full_name'] ?? '');
        $email        = trim($_POST['email'] ?? '');
        $username     = trim($_POST['username'] ?? '');
        $specialty_id = intval($_POST['specialty_id'] ?? 0);
        $fees         = floatval($_POST['fees'] ?? 0);
        $availability = trim($_POST['availability'] ?? 'Available');

        if (empty($full_name) || empty($email) || empty($username)) {
            echo json_encode(['success' => false, 'message' => 'Full name, email, and username are required.']);
            exit;
        }

        // Check username uniqueness
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Username already taken.']);
            exit;
        }

        $pdo->beginTransaction();

        // 1. Create user record with role=doctor
        $password = password_hash('doctor123', PASSWORD_DEFAULT); // Default password
        $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, email, role) VALUES (?, ?, ?, ?, 'doctor')");
        $stmt->execute([$username, $password, $full_name, $email]);
        $user_id = $pdo->lastInsertId();

        // 2. Create doctor record
        $stmt = $pdo->prepare("INSERT INTO doctors (user_id, specialty_id, fees, availability) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $specialty_id ?: null, $fees, $availability]);

        $pdo->commit();
        log_activity('Add Doctor', 'Doctors', "Added new doctor: {$full_name} ({$username}).");
        echo json_encode(['success' => true, 'message' => "Dr. {$full_name} added successfully! Default password: doctor123"]);

    } elseif ($action === 'delete') {
        $doctor_id = intval($_POST['id'] ?? 0);

        // Get the user_id linked to this doctor
        $stmt = $pdo->prepare("SELECT user_id FROM doctors WHERE id = ?");
        $stmt->execute([$doctor_id]);
        $user_id = $stmt->fetchColumn();

        if ($user_id) {
            // Deleting the user cascades to the doctors record
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            log_activity('Delete Doctor', 'Doctors', "Removed doctor ID: {$doctor_id}.");
            echo json_encode(['success' => true, 'message' => 'Doctor record removed.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Doctor not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

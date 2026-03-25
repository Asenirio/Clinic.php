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
    if ($action === 'add') {
        $full_name = $_POST['full_name'] ?? '';
        $username = $_POST['username'] ?? '';
        $dob = $_POST['dob'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $blood_type = $_POST['blood_type'] ?? '';

        if (empty($full_name) || empty($username)) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
            exit;
        }

        $pdo->beginTransaction();

        // 1. Create User
        $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role, gender, dob) VALUES (?, ?, ?, 'patient', ?, ?)");
        $password = password_hash('patient123', PASSWORD_DEFAULT); // Default password
        $stmt->execute([$username, $password, $full_name, $gender, $dob]);
        $user_id = $pdo->lastInsertId();

        // 2. Create Patient Record
        $stmt = $pdo->prepare("INSERT INTO patients (user_id, blood_type, status) VALUES (?, ?, 'Active')");
        $stmt->execute([$user_id, $blood_type]);

        $pdo->commit();
        log_activity('Register Patient', 'Patients', "Registered new patient: {$full_name} ({$username}).");
        echo json_encode(['success' => true, 'message' => 'Patient registered successfully.']);

    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        // Due to cascade in schema (patient has FK to users), deleting user deletes patient
        // But our patient table has FK patient_id in appointments etc.
        // Let's find the user_id first
        $stmt = $pdo->prepare("SELECT user_id FROM patients WHERE id = ?");
        $stmt->execute([$id]);
        $user_id = $stmt->fetchColumn();

        if ($user_id) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            if ($stmt->execute([$user_id])) {
                log_activity('Delete Patient', 'Patients', "Removed patient record ID: {$id} (User ID: {$user_id}).");
                echo json_encode(['success' => true, 'message' => 'Patient record removed.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Delete failed.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Patient not found.']);
        }
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction())
        $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
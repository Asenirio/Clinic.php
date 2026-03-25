<?php
/**
 * Scheduling Handler
 * Handles shift assignment for both doctors and staff members
 * 
 * @uses validate_csrf() from auth.php
 * @uses log_activity() from auth.php
 */

// Suppress linter warnings - these functions are defined in auth.php
if (false) {
    function validate_csrf($token) {}
    function log_activity($action, $module, $details = null) {}
}

require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        $_SESSION['error_msg'] = "Security validation failed.";
        header("Location: schedules.php");
        exit;
    }

    $user_id = $_POST['user_id'];
    $day_of_week = $_POST['day_of_week'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $shift_type = $_POST['shift_type'] ?? 'regular';

    try {
        // Basic validation: End time must be after start time
        if (strtotime($end_time) <= strtotime($start_time)) {
            $_SESSION['error_msg'] = "Invalid shift times. End time must be after start time.";
            header("Location: schedules.php");
            exit;
        }

        // Check if the user is a doctor or staff
        $role_check = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $role_check->execute([$user_id]);
        $user_role = $role_check->fetchColumn();

        if (!$user_role) {
            $_SESSION['error_msg'] = "User not found.";
            header("Location: schedules.php");
            exit;
        }

        if ($user_role === 'doctor') {
            // Get doctor ID from user ID
            $doctor_stmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id = ?");
            $doctor_stmt->execute([$user_id]);
            $doctor_id = $doctor_stmt->fetchColumn();

            if (!$doctor_id) {
                $_SESSION['error_msg'] = "This user is assigned as a doctor but has no doctor profile record. Please create a doctor profile first.";
                header("Location: schedules.php");
                exit;
            }

            // Optional: Check for duplicate shifts (same user, same day, same time range)
            $dup_stmt = $pdo->prepare("SELECT id FROM doctor_shifts WHERE doctor_id = ? AND day_of_week = ? AND start_time = ? AND end_time = ?");
            $dup_stmt->execute([$doctor_id, $day_of_week, $start_time, $end_time]);
            if ($dup_stmt->fetch()) {
                $_SESSION['error_msg'] = "This shift already exists for this doctor.";
                header("Location: schedules.php");
                exit;
            }

            // Insert into doctor_shifts table
            $stmt = $pdo->prepare("INSERT INTO doctor_shifts (doctor_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
            $stmt->execute([$doctor_id, $day_of_week, $start_time, $end_time]);
            log_activity('Assign Shift', 'Schedules', "Assigned {$day_of_week} shift ({$start_time} - {$end_time}) to Doctor (User ID: {$user_id})");
            $_SESSION['success_msg'] = "Doctor shift assigned successfully!";
        } else {
            // Optional: Check for duplicate shifts
            $dup_stmt = $pdo->prepare("SELECT id FROM staff_shifts WHERE user_id = ? AND day_of_week = ? AND start_time = ? AND end_time = ?");
            $dup_stmt->execute([$user_id, $day_of_week, $start_time, $end_time]);
            if ($dup_stmt->fetch()) {
                $_SESSION['error_msg'] = "This shift already exists for this staff member.";
                header("Location: schedules.php");
                exit;
            }

            // Insert into staff_shifts table for staff/pharmacist
            $stmt = $pdo->prepare("INSERT INTO staff_shifts (user_id, day_of_week, start_time, end_time, shift_type) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $day_of_week, $start_time, $end_time, $shift_type]);
            log_activity('Assign Shift', 'Schedules', "Assigned {$day_of_week} shift ({$start_time} - {$end_time}) to {$user_role} (User ID: {$user_id})");
            $_SESSION['success_msg'] = ucfirst($user_role) . " shift assigned successfully!";
        }
    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Assignment failed: " . $e->getMessage();
    }

    header("Location: schedules.php");
    exit;
}
?>
<?php
require 'db.php';
$_SERVER['REQUEST_METHOD'] = 'POST';
session_start();
$_SESSION['csrf_token'] = 'test';
$_POST['csrf_token'] = 'test';

$_POST['user_id'] = 1; // Assuming 1 is Admin, user_role = admin
$_POST['day_of_week'] = 'Monday';
$_POST['start_time'] = '08:00';
$_POST['end_time'] = '16:00';
$_POST['shift_type'] = 'regular';

ob_start();
include 'scheduling_handler.php';
$output = ob_get_clean();
echo "Output: " . $output . "\n";
echo "Success Msg: " . ($_SESSION['success_msg'] ?? 'None') . "\n";
echo "Error Msg: " . ($_SESSION['error_msg'] ?? 'None') . "\n";
?>

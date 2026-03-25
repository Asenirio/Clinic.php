<?php
header('Content-Type: application/json');
require_once 'auth.php';

try {
    $notifications = [];

    // 1. Low Stock Alerts
    $low_stock = $pdo->query("SELECT id, item_name, stock FROM pharmacy_items WHERE stock < 20 LIMIT 5")->fetchAll();
    foreach ($low_stock as $item) {
        $notifications[] = [
            'type' => 'warning',
            'title' => 'Low Stock Alert',
            'message' => "{$item['item_name']} is low on stock ({$item['stock']} left).",
            'icon' => 'fa-pills',
            'time' => 'Inventory'
        ];
    }

    // 2. Upcoming Appointments (Today)
    $today = date('Y-m-d');
    $upcoming = $pdo->prepare("
        SELECT a.id, u.full_name, a.appointment_date 
        FROM appointments a 
        JOIN patients p ON a.patient_id = p.id 
        JOIN users u ON p.user_id = u.id 
        WHERE DATE(a.appointment_date) = ? AND a.status = 'pending' 
        LIMIT 5
    ");
    $upcoming->execute([$today]);
    $appointments = $upcoming->fetchAll();
    foreach ($appointments as $app) {
        $time = date('H:i', strtotime($app['appointment_date']));
        $notifications[] = [
            'type' => 'info',
            'title' => 'Upcoming Appointment',
            'message' => "{$app['full_name']} at {$time}.",
            'icon' => 'fa-calendar-check',
            'time' => 'Today'
        ];
    }

    // 3. New Enquiries
    $enquiries = $pdo->query("SELECT id, name FROM enquiries WHERE status = 'new' LIMIT 5")->fetchAll();
    foreach ($enquiries as $enq) {
        $notifications[] = [
            'type' => 'success',
            'title' => 'New Enquiry',
            'message' => "From {$enq['name']}.",
            'icon' => 'fa-envelope',
            'time' => 'New'
        ];
    }

    echo json_encode(['success' => true, 'count' => count($notifications), 'notifications' => $notifications]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
<?php
header('Content-Type: application/json');
require_once 'auth.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    if ($action === 'send') {
        if (!validate_csrf($_POST['csrf_token'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Invalid token']);
            exit;
        }
        $receiver_id = $_POST['receiver_id'] ?? 0;
        $message = $_POST['message'] ?? '';
        $sender_id = $_SESSION['user_id'];

        if (empty($message))
            exit;

        $stmt = $pdo->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$sender_id, $receiver_id, $message]);
        echo json_encode(['success' => true]);

    } elseif ($action === 'fetch') {
        $other_id = $_GET['user_id'] ?? 0;
        $my_id = $_SESSION['user_id'];

        $stmt = $pdo->prepare("
            SELECT * FROM chat_messages 
            WHERE (sender_id = ? AND receiver_id = ?) 
               OR (sender_id = ? AND receiver_id = ?) 
            ORDER BY created_at ASC
        ");
        $stmt->execute([$my_id, $other_id, $other_id, $my_id]);
        $messages = $stmt->fetchAll();

        echo json_encode(['success' => true, 'messages' => $messages]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
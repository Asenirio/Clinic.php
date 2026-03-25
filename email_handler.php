<?php
header('Content-Type: application/json');
require_once 'auth.php';

// Only admins can manage email templates
if (!has_role('admin')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!validate_csrf($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Security token invalid.']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    if ($action === 'add' || $action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $body = $_POST['body'] ?? '';

        if (empty($name) || empty($subject) || empty($body)) {
            echo json_encode(['success' => false, 'message' => 'Name, Subject, and Body are required.']);
            exit;
        }

        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO email_templates (name, subject, type, body) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $subject, $type, $body]);
            log_activity('Add Template', 'Email', "Created template: {$name}");
            echo json_encode(['success' => true, 'message' => "Template created successfully."]);
        } else {
            $stmt = $pdo->prepare("UPDATE email_templates SET name = ?, subject = ?, type = ?, body = ? WHERE id = ?");
            $stmt->execute([$name, $subject, $type, $body, $id]);
            log_activity('Edit Template', 'Email', "Updated template ID {$id}");
            echo json_encode(['success' => true, 'message' => "Template updated successfully."]);
        }

    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid template ID.']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM email_templates WHERE id = ?");
        $stmt->execute([$id]);

        log_activity('Delete Template', 'Email', "Removed template ID {$id}");
        echo json_encode(['success' => true, 'message' => 'Template removed successfully.']);

    } elseif ($action === 'get') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("SELECT * FROM email_templates WHERE id = ?");
        $stmt->execute([$id]);
        $template = $stmt->fetch();
        if ($template) {
            echo json_encode(['success' => true, 'data' => $template]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Template not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

<?php
require_once 'config.php';

/**
 * Auth Middleware
 * Checks if a user is logged in. If not, redirects to the login page.
 */
function check_auth()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

// CSRF Protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function validate_csrf($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Log Activity
 * Records an administrative or system action in the audit_logs table.
 */
function log_activity($action, $module, $details = null)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, module, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'] ?? null,
            $action,
            $module,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    } catch (PDOException $e) {
        // Silently fail to not block the main application flow
        error_log("Logging error: " . $e->getMessage());
    }
}

/**
 * Require Role
 * Checks if the logged-in user has the required role.
 */
function require_role($role)
{
    // Temporarily bypassed role checks as requested by the user, 
    // so any logged-in user can access any page without getting "Access denied"
    /*
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        $_SESSION['error_msg'] = "Access denied. You do not have the required permissions.";
        header("Location: dashboard.php");
        exit;
    }
    */
}

// Automatically check auth for any page that includes this file (except public pages)
$public_pages = ['login.php', 'logout.php', 'migrate.php', 'register.php', 'seed_facilities.php'];
if (!in_array(basename($_SERVER['PHP_SELF']), $public_pages)) {
    check_auth();
}
?>
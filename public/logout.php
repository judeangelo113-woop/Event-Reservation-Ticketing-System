
<?php
require_once __DIR__ . '/../includes/auth.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('405 Method Not Allowed');
}

// Verify CSRF token
if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('403 Forbidden: Invalid security token.');
}

// Clear session data
$_SESSION = [];

// Delete the session cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroy the session
session_destroy();

// Return to login page
header('Location: login.php');
exit;

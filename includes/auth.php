
<?php

// Start a secure PHP session
if (session_status() === PHP_SESSION_NONE) {

    ini_set('session.use_strict_mode', '1');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

// Check if a user is logged in
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

// Get the current logged-in user
function currentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'role' => $_SESSION['role'],
        'attendee_id' => $_SESSION['attendee_id'] ?? null
    ];
}

// Generate CSRF token
function generateCSRFToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken(?string $token): bool
{
    return isset($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

// Check if current user is an admin
function isAdmin(): bool
{
    return isLoggedIn()
        && ($_SESSION['role'] ?? '') === 'admin';
}

// Check if current user is an attendee
function isAttendee(): bool
{
    return isLoggedIn()
        && ($_SESSION['role'] ?? '') === 'attendee';
}

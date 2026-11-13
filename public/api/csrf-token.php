<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/auth.php';

if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);

    echo json_encode([
        'success' => false,
        'message' => 'Admin access required.'
    ]);

    exit;
}

echo json_encode([
    'success' => true,
    'csrf_token' => generateCSRFToken()
]);

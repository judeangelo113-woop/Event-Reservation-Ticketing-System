
<?php

require_once __DIR__ . '/auth.php';

// Redirect users who are not logged in
if (!isLoggedIn()) {

    $scriptDir = str_replace(
        '\\',
        '/',
        dirname($_SERVER['SCRIPT_NAME'])
    );

    $loginUrl = str_ends_with($scriptDir, '/admin')
        ? '../public/login.php'
        : 'login.php';

    header("Location: $loginUrl");
    exit;
}

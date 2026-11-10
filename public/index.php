
<?php

require_once __DIR__ . '/../includes/auth.php';

// Redirect users based on their login status and role.
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if (isAdmin()) {
    header('Location: dashboard.php');
    exit;
}

if (isAttendee()) {
    header('Location: attendee-dashboard.php');
    exit;
}

// If the account has an unsupported role, log out.
header('Location: logout.php');
exit;

?>

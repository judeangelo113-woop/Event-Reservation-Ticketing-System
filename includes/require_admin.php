
<?php

require_once __DIR__ . '/require_login.php';

// Allow administrators only
if (!isAdmin()) {

    http_response_code(403);

    exit('403 Forbidden: You do not have permission to access this page.');
}

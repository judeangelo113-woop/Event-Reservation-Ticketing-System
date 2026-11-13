
<?php

require_once __DIR__ . '/../includes/require_login.php';
require_once __DIR__ . '/../config/Database.php';

// Only attendees can access this page
if (!isAttendee()) {
    header('Location: dashboard.php');
    exit;
}

$userId = (int) ($_SESSION['user_id'] ?? 0);

if ($userId <= 0) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

try {

    $database = new Database();
    $pdo = $database->connect();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {

            $error = 'Invalid security token. Please refresh the page and try again.';

        } else {

            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (
                $currentPassword === '' ||
                $newPassword === '' ||
                $confirmPassword === ''
            ) {

                $error = 'Please complete all password fields.';

            } elseif (strlen($newPassword) < 8) {

                $error = 'Your new password must be at least 8 characters long.';

            } elseif ($newPassword !== $confirmPassword) {

                $error = 'Your new passwords do not match.';

            } elseif ($currentPassword === $newPassword) {

                $error = 'Your new password must be different from your current password.';

            } else {

                // Retrieve the logged-in user's password
                $stmt = $pdo->prepare(
                    "SELECT password
                     FROM users
                     WHERE id = :user_id
                     AND role = 'attendee'
                     LIMIT 1"
                );

                $stmt->execute([
                    'user_id' => $userId
                ]);

                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user) {

                    $error = 'Your account could not be found.';

                } elseif (!password_verify($currentPassword, $user['password'])) {

                    $error = 'Your current password is incorrect.';

                } else {

                    // Securely hash the new password
                    $hashedPassword = password_hash(
                        $newPassword,
                        PASSWORD_DEFAULT
                    );

                    // Update only the logged-in attendee's password
                    $updateStmt = $pdo->prepare(
                        "UPDATE users
                         SET password = :password
                         WHERE id = :user_id
                         AND role = 'attendee'"
                    );

                    $updateStmt->execute([
                        'password' => $hashedPassword,
                        'user_id' => $userId
                    ]);

                    if ($updateStmt->rowCount() > 0) {

                        session_regenerate_id(true);

                        $_SESSION['password_success'] =
                            'Your password has been changed successfully.';

                        header('Location: my-account.php');
                        exit;

                    } else {

                        $error = 'Your password could not be updated. Please try again.';

                    }
                }
            }
        }
    }

} catch (PDOException $e) {

    error_log('Change Password Error: ' . $e->getMessage());

    $error = 'Unable to change your password. Please try again later.';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Change Password | EventlyPH</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <main class="change-password-main">

        <div class="change-password-container">

            <!-- Page Header -->
            <div class="change-password-header">

                <div>
                    <span class="change-password-label">ACCOUNT SECURITY</span>

                    <h1>Change Password</h1>

                    <p>
                        Keep your EventlyPH account secure by updating your password.
                    </p>
                </div>

                <a href="my-account.php" class="change-password-back">
                    ← Back to My Account
                </a>

            </div>

            <!-- Password Card -->
            <section class="change-password-card">

                <div class="change-password-card-heading">

                    <div class="change-password-icon">
                        🔒
                    </div>

                    <div>
                        <h2>Update Your Password</h2>
                        <p>Enter your current password and choose a new one.</p>
                    </div>

                </div>

                <?php if ($error !== ''): ?>

                    <div class="change-password-message error">
                        <?= htmlspecialchars($error) ?>
                    </div>

                <?php endif; ?>

                <form method="POST" action="change-password.php">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(generateCSRFToken()) ?>"
                    >

                    <div class="change-password-fields">

                        <div class="change-password-field">
                            <label for="current_password">
                                Current Password
                            </label>

                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                placeholder="Enter your current password"
                                autocomplete="current-password"
                                required
                            >
                        </div>

                        <div class="change-password-field">
                            <label for="new_password">
                                New Password
                            </label>

                            <input
                                type="password"
                                id="new_password"
                                name="new_password"
                                minlength="8"
                                placeholder="Enter your new password"
                                autocomplete="new-password"
                                required
                            >

                            <small>
                                Your password must contain at least 8 characters.
                            </small>
                        </div>

                        <div class="change-password-field">
                            <label for="confirm_password">
                                Confirm New Password
                            </label>

                            <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                minlength="8"
                                placeholder="Re-enter your new password"
                                autocomplete="new-password"
                                required
                            >
                        </div>

                    </div>

                    <div class="change-password-actions">

                        <a href="my-account.php" class="change-password-cancel">
                            Cancel
                        </a>

                        <button type="submit" class="change-password-submit">
                            Save New Password
                        </button>

                    </div>

                </form>

            </section>

            <!-- Security Information -->
            <div class="change-password-security">
                <span>🛡️</span>

                <div>
                    <strong>Your account security matters.</strong>
                    <p>
                        Your password is securely hashed before being saved.
                        Never share your password with anyone.
                    </p>
                </div>
            </div>

        </div>

    </main>

</body>
</html>

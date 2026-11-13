
<?php

require_once __DIR__ . '/../includes/require_login.php';
require_once __DIR__ . '/../config/Database.php';

// Only attendees can access this page
if (!isAttendee()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$success = '';

$attendee = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone' => ''
];

try {

    $database = new Database();
    $pdo = $database->connect();

    $attendeeId = $_SESSION['attendee_id'];

    // Get current attendee information
    $stmt = $pdo->prepare(
        "SELECT first_name, last_name, email, phone
         FROM attendees
         WHERE id = :attendee_id
         LIMIT 1"
    );

    $stmt->execute([
        'attendee_id' => $attendeeId
    ]);

    $currentAttendee = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$currentAttendee) {
        die('Attendee account not found.');
    }

    $attendee = $currentAttendee;

    // Generate CSRF token
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $attendee['first_name'] = trim($_POST['first_name'] ?? '');
        $attendee['last_name'] = trim($_POST['last_name'] ?? '');
        $attendee['email'] = trim($_POST['email'] ?? '');
        $attendee['phone'] = trim($_POST['phone'] ?? '');

        // Check CSRF token
        $submittedToken = $_POST['csrf_token'] ?? '';

        if (
            !is_string($submittedToken) ||
            !hash_equals($_SESSION['csrf_token'], $submittedToken)
        ) {
            $errors[] = 'Invalid form submission. Please refresh the page and try again.';
        }

        // Validate first name
        if ($attendee['first_name'] === '') {
            $errors[] = 'First name is required.';
        } elseif (strlen($attendee['first_name']) > 100) {
            $errors[] = 'First name must not exceed 100 characters.';
        }

        // Validate last name
        if ($attendee['last_name'] === '') {
            $errors[] = 'Last name is required.';
        } elseif (strlen($attendee['last_name']) > 100) {
            $errors[] = 'Last name must not exceed 100 characters.';
        }

        // Validate email
        if ($attendee['email'] === '') {
            $errors[] = 'Email address is required.';
        } elseif (!filter_var($attendee['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        } elseif (strlen($attendee['email']) > 255) {
            $errors[] = 'Email address is too long.';
        }

        // Validate phone
        if (strlen($attendee['phone']) > 30) {
            $errors[] = 'Phone number must not exceed 30 characters.';
        }

        // Check duplicate email
        if (empty($errors)) {

            $emailStmt = $pdo->prepare(
                "SELECT id
                 FROM attendees
                 WHERE email = :email
                   AND id <> :attendee_id
                 LIMIT 1"
            );

            $emailStmt->execute([
                'email' => $attendee['email'],
                'attendee_id' => $attendeeId
            ]);

            if ($emailStmt->fetch()) {
                $errors[] = 'This email address is already being used by another attendee.';
            }

        }

        // Update attendee information
        if (empty($errors)) {

            $updateStmt = $pdo->prepare(
                "UPDATE attendees
                 SET first_name = :first_name,
                     last_name = :last_name,
                     email = :email,
                     phone = :phone
                 WHERE id = :attendee_id"
            );

            $updateStmt->execute([
                'first_name' => $attendee['first_name'],
                'last_name' => $attendee['last_name'],
                'email' => $attendee['email'],
                'phone' => $attendee['phone'] !== ''
                    ? $attendee['phone']
                    : null,
                'attendee_id' => $attendeeId
            ]);

            // Prevent form resubmission
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            $_SESSION['profile_success'] = 'Your profile has been updated successfully.';

            header('Location: my-account.php');
            exit;

        }

    }

} catch (PDOException $e) {

    error_log('Edit Profile error: ' . $e->getMessage());

    $errors[] = 'Unable to update your profile. Please try again later.';

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Profile | EventlyPH</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="edit-profile-page">

    <!-- Page Header -->
    <section class="edit-profile-header">

        <div>
            <span>ACCOUNT SETTINGS</span>
            <h1>Edit Profile</h1>
            <p>Update your personal information below.</p>
        </div>

        <a href="my-account.php">
            ← Back to My Account
        </a>

    </section>

    <?php if (!empty($errors)): ?>

        <div class="edit-profile-errors">

            <strong>Please check the following:</strong>

            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>

        </div>

    <?php endif; ?>

    <!-- Edit Profile Form -->
    <section class="edit-profile-card">

        <div class="edit-profile-card-heading">

            <div class="edit-profile-icon">👤</div>

            <div>
                <h2>Personal Information</h2>
                <p>Make sure your information is correct and up to date.</p>
            </div>

        </div>

        <form method="POST" action="edit-profile.php">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
            >

            <div class="edit-profile-form-grid">

                <div class="edit-profile-form-group">

                    <label for="first_name">First Name</label>

                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        maxlength="100"
                        value="<?= htmlspecialchars($attendee['first_name']) ?>"
                        placeholder="Enter your first name"
                        required
                    >

                </div>

                <div class="edit-profile-form-group">

                    <label for="last_name">Last Name</label>

                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        maxlength="100"
                        value="<?= htmlspecialchars($attendee['last_name']) ?>"
                        placeholder="Enter your last name"
                        required
                    >

                </div>

                <div class="edit-profile-form-group">

                    <label for="email">Email Address</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        maxlength="255"
                        value="<?= htmlspecialchars($attendee['email']) ?>"
                        placeholder="Enter your email address"
                        required
                    >

                </div>

                <div class="edit-profile-form-group">

                    <label for="phone">
                        Phone Number
                        <span>(Optional)</span>
                    </label>

                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        maxlength="30"
                        value="<?= htmlspecialchars($attendee['phone'] ?? '') ?>"
                        placeholder="Enter your phone number"
                    >

                </div>

            </div>

            <div class="edit-profile-note">
                <strong>Note:</strong>
                Your username and account type cannot be changed from this page.
            </div>

            <div class="edit-profile-actions">

                <a href="my-account.php" class="edit-profile-cancel">
                    Cancel
                </a>

                <button type="submit" class="edit-profile-submit">
                    Save Changes
                </button>

            </div>

        </form>

    </section>

</main>

</body>
</html>

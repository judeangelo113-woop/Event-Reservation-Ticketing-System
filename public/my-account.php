
<?php

require_once __DIR__ . '/../includes/require_login.php';
require_once __DIR__ . '/../config/Database.php';

// Only attendees can access this page
if (!isAttendee()) {
    header('Location: dashboard.php');
    exit;
}

$attendee = null;
$error = '';

$attendeeId = (int) ($_SESSION['attendee_id'] ?? 0);

if ($attendeeId <= 0) {
    header('Location: login.php');
    exit;
}

try {

    $database = new Database();
    $pdo = $database->connect();

    $stmt = $pdo->prepare(
        "SELECT first_name, last_name, email, phone
         FROM attendees
         WHERE id = :attendee_id
         LIMIT 1"
    );

    $stmt->execute([
        'attendee_id' => $attendeeId
    ]);

    $attendee = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$attendee) {
        $error = 'Your account information could not be found.';
    }

} catch (PDOException $e) {

    error_log('My Account error: ' . $e->getMessage());

    $error = 'Unable to load your account information. Please try again later.';
}

$firstInitial = strtoupper(substr($attendee['first_name'] ?? 'U', 0, 1));
$lastInitial = strtoupper(substr($attendee['last_name'] ?? '', 0, 1));

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Account | EventlyPH</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="my-account-page">

    <div class="my-account-container">

        <!-- Page Header -->
        <section class="my-account-header">

            <div>
                <span class="my-account-eyebrow">PERSONAL SETTINGS</span>
                <h1>My Account</h1>
                <p>View and manage your personal account information.</p>
            </div>

            <a href="attendee-dashboard.php" class="my-account-back">
                ← Back to Dashboard
            </a>

        </section>

        <?php if ($error !== ''): ?>

            <div class="my-account-error">
                <strong>!</strong>
                <span><?= htmlspecialchars($error) ?></span>
            </div>

        <?php elseif ($attendee): ?>

            <!-- Success Message -->
            <?php if (!empty($_SESSION['profile_success'])): ?>

                <div class="profile-success-message">
                    <span>✓</span>
                    <?= htmlspecialchars($_SESSION['profile_success']) ?>
                </div>

                <?php unset($_SESSION['profile_success']); ?>

            <?php endif; ?>


            <?php if (!empty($_SESSION['password_success'])): ?>

                <div class="profile-success-message">
                    ✓ <?= htmlspecialchars($_SESSION['password_success']) ?>
                </div>

                <?php unset($_SESSION['password_success']); ?>

            <?php endif; ?>


            <!-- Profile Overview -->
            <section class="my-account-profile">

                <div class="my-account-avatar">
                    <?= htmlspecialchars($firstInitial . $lastInitial) ?>
                </div>

                <div class="my-account-profile-info">

                    <span class="my-account-profile-label">
                        ATTENDEE PROFILE
                    </span>

                    <h2>
                        <?= htmlspecialchars(
                            $attendee['first_name'] . ' ' . $attendee['last_name']
                        ) ?>
                    </h2>

                    <p>
                        <?= htmlspecialchars($attendee['email']) ?>
                    </p>

                    <span class="my-account-role">
                        <span></span>
                        Attendee Account
                    </span>

                </div>

                <a href="edit-profile.php" class="my-account-profile-edit">
                    ✏️ Edit Profile
                </a>

            </section>

            <!-- Personal Information -->
            <section class="my-account-section">

                <div class="my-account-section-heading">
                    <div>
                        <h2>Personal Information</h2>
                        <p>Your registered personal details.</p>
                    </div>
                </div>

                <div class="my-account-information-card">

                    <div class="my-account-field">
                        <div class="my-account-field-icon">👤</div>

                        <div class="my-account-field-content">
                            <span>First Name</span>
                            <strong>
                                <?= htmlspecialchars($attendee['first_name']) ?>
                            </strong>
                        </div>
                    </div>

                    <div class="my-account-field">
                        <div class="my-account-field-icon">👤</div>

                        <div class="my-account-field-content">
                            <span>Last Name</span>
                            <strong>
                                <?= htmlspecialchars($attendee['last_name']) ?>
                            </strong>
                        </div>
                    </div>

                    <div class="my-account-field">
                        <div class="my-account-field-icon">✉️</div>

                        <div class="my-account-field-content">
                            <span>Email Address</span>
                            <strong>
                                <?= htmlspecialchars($attendee['email']) ?>
                            </strong>
                        </div>
                    </div>

                    <div class="my-account-field">
                        <div class="my-account-field-icon">📱</div>

                        <div class="my-account-field-content">
                            <span>Phone Number</span>
                            <strong>
                                <?= htmlspecialchars(
                                    !empty($attendee['phone'])
                                        ? $attendee['phone']
                                        : 'Not provided'
                                ) ?>
                            </strong>
                        </div>
                    </div>

                </div>

            </section>


            <div class="my-account-edit-action">

                <div>
                    <h3>Account Security</h3>
                    <p>Change your password to help keep your account secure.</p>
                </div>

                <a href="change-password.php">
                    🔒 Change Password
                </a>

            </div>


            <!-- Account Information -->
            <section class="my-account-section">

                <div class="my-account-section-heading">
                    <div>
                        <h2>Account Information</h2>
                        <p>Your EventlyPH account details.</p>
                    </div>
                </div>

                <div class="my-account-information-card">

                    <div class="my-account-field">
                        <div class="my-account-field-icon">🔑</div>

                        <div class="my-account-field-content">
                            <span>Username</span>
                            <strong>
                                <?= htmlspecialchars(
                                    $_SESSION['username'] ?? 'Not available'
                                ) ?>
                            </strong>
                        </div>
                    </div>

                    <div class="my-account-field">
                        <div class="my-account-field-icon">🎟️</div>

                        <div class="my-account-field-content">
                            <span>Account Type</span>
                            <strong>Attendee</strong>
                        </div>
                    </div>

                </div>

            </section>

            <!-- Edit Profile Action -->
            <section class="my-account-edit-action">

                <div>
                    <h3>Keep your information up to date</h3>
                    <p>Update your name, email address, or phone number.</p>
                </div>

                <a href="edit-profile.php">
                    ✏️ Edit Profile
                </a>

            </section>

            <!-- Account Navigation -->
            <section class="my-account-footer">

                <div>
                    <h3>Ready for your next event?</h3>
                    <p>Explore available events and reserve your tickets.</p>
                </div>

                <a href="browse-events.php">
                    Browse Events →
                </a>

            </section>

        <?php endif; ?>

    </div>

</main>

</body>
</html>

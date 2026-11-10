
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
        'attendee_id' => $_SESSION['attendee_id']
    ]);

    $attendee = $stmt->fetch();

    if (!$attendee) {
        $error = 'Attendee information could not be found.';
    }

} catch (PDOException $e) {
    $error = 'Unable to load your account information.';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendee Dashboard - Event Ticketing System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="attendee-dashboard">

    <h2>Attendee Dashboard</h2>

    <?php if ($error !== ''): ?>

        <p class="dashboard-error">
            <?= htmlspecialchars($error) ?>
        </p>

    <?php elseif ($attendee): ?>

        <h3>
            Welcome,
            <?= htmlspecialchars($attendee['first_name']) ?>!
        </h3>

        <div class="attendee-profile">

            <h3>My Account Information</h3>

            <p>
                <strong>Full Name:</strong>
                <?= htmlspecialchars($attendee['first_name'] . ' ' . $attendee['last_name']) ?>
            </p>

            <p>
                <strong>Username:</strong>
                <?= htmlspecialchars($_SESSION['username']) ?>
            </p>

            <p>
                <strong>Email:</strong>
                <?= htmlspecialchars($attendee['email']) ?>
            </p>

            <p>
                <strong>Phone:</strong>
                <?= htmlspecialchars($attendee['phone'] ?? 'Not provided') ?>
            </p>

        </div>

    <?php endif; ?>

</div>

</body>
</html>

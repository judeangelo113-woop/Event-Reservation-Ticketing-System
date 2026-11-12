
<?php

require_once __DIR__ . '/../includes/require_login.php';
require_once __DIR__ . '/../config/Database.php';

// Only attendees can access this page
if (!isAttendee()) {
    header('Location: dashboard.php');
    exit;
}

$attendee = null;
$announcements = [];
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

    // Load the latest published event announcements
    $announcementStmt = $pdo->prepare(
        "SELECT
            event_announcements.title,
            event_announcements.message,
            event_announcements.published_at,
            events.title AS event_title
        FROM event_announcements
        INNER JOIN events
            ON event_announcements.event_id = events.id
        WHERE event_announcements.published_at IS NOT NULL
        ORDER BY event_announcements.published_at DESC
        LIMIT 5"
    );

    $announcementStmt->execute();
    $announcements = $announcementStmt->fetchAll();

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

        <?php if ($error !== ''): ?>

            <p class="dashboard-error">
                <?= htmlspecialchars($error) ?>
            </p>

        <?php elseif ($attendee): ?>

            <!-- Welcome Banner -->
            <div class="dashboard-welcome">
                <h2>Welcome back, <?= htmlspecialchars($attendee['first_name']) ?>!</h2>
                <p>Manage your event reservations and stay updated with the latest announcements.</p>
            </div>

            <!-- Quick Access Cards -->
            <div class="dashboard-shortcuts">

                <a href="browse-events.php" class="shortcut-card">
                    <h3>🎫 Browse Events</h3>
                    <p>Explore upcoming events and reserve your tickets.</p>
                </a>

                <a href="my-reservations.php" class="shortcut-card">
                    <h3>📋 My Reservations</h3>
                    <p>View and manage your event reservations.</p>
                </a>

                <a href="my-tickets.php" class="shortcut-card">
                    <h3>🎟️ My Tickets</h3>
                    <p>Check your issued tickets and ticket numbers.</p>
                </a>

            </div>

            <!-- Account Information -->
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

            <!-- Latest Announcements -->
            <div class="attendee-announcements">

                <h3>Latest Event Announcements</h3>

                <?php if (empty($announcements)): ?>

                    <p>No announcements available at the moment.</p>

                <?php else: ?>

                    <?php foreach ($announcements as $announcement): ?>

                        <div class="announcement-card">

                            <h4><?= htmlspecialchars($announcement['title']) ?></h4>

                            <p>
                                <strong>Event:</strong>
                                <?= htmlspecialchars($announcement['event_title']) ?>
                            </p>

                            <p>
                                <?= nl2br(htmlspecialchars($announcement['message'])) ?>
                            </p>

                            <small>
                                Published:
                                <?= htmlspecialchars(date(
                                    'F d, Y h:i A',
                                    strtotime($announcement['published_at'])
                                )) ?>
                            </small>

                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        <?php endif; ?>

    </div>



    

</body>
</html>

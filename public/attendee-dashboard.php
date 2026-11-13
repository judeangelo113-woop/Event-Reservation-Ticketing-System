
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
$recentReservations = [];

$totalReservations = 0;
$totalTickets = 0;
$upcomingEvents = 0;

$error = '';

try {

    $database = new Database();
    $pdo = $database->connect();

    $attendeeId = $_SESSION['attendee_id'];

    // =====================================
    // GET ATTENDEE INFORMATION
    // =====================================

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
        $error = 'Attendee information could not be found.';
    }

    if ($attendee) {

        // =====================================
        // TOTAL RESERVATIONS
        // =====================================

        $reservationCountStmt = $pdo->prepare(
            "SELECT COUNT(*)
             FROM reservations
             WHERE attendee_id = :attendee_id"
        );

        $reservationCountStmt->execute([
            'attendee_id' => $attendeeId
        ]);

        $totalReservations = (int) $reservationCountStmt->fetchColumn();


        // =====================================
        // TOTAL ISSUED TICKETS
        // =====================================

        $ticketCountStmt = $pdo->prepare(
            "SELECT COUNT(*)
             FROM tickets t
             INNER JOIN reservations r
                ON t.reservation_id = r.id
             WHERE r.attendee_id = :attendee_id"
        );

        $ticketCountStmt->execute([
            'attendee_id' => $attendeeId
        ]);

        $totalTickets = (int) $ticketCountStmt->fetchColumn();


        // =====================================
        // UPCOMING RESERVED EVENTS
        // =====================================

        $upcomingStmt = $pdo->prepare(
            "SELECT COUNT(DISTINCT e.id)
             FROM reservations r
             INNER JOIN events e
                ON r.event_id = e.id
             WHERE r.attendee_id = :attendee_id
                AND e.event_date >= CURDATE()
                AND e.status <> 'CANCELLED'"
        );

        $upcomingStmt->execute([
            'attendee_id' => $attendeeId
        ]);

        $upcomingEvents = (int) $upcomingStmt->fetchColumn();


        // =====================================
        // RECENT RESERVATIONS
        // =====================================

        $recentStmt = $pdo->prepare(
            "SELECT
                r.id,
                r.reservation_date,
                r.quantity,
                r.status,
                e.title AS event_title,
                e.event_date
             FROM reservations r
             INNER JOIN events e
                ON r.event_id = e.id
             WHERE r.attendee_id = :attendee_id
             ORDER BY r.reservation_date DESC
             LIMIT 5"
        );

        $recentStmt->execute([
            'attendee_id' => $attendeeId
        ]);

        $recentReservations = $recentStmt->fetchAll(PDO::FETCH_ASSOC);

    }


    // =====================================
    // LATEST PUBLISHED ANNOUNCEMENTS
    // =====================================

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

    $announcements = $announcementStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    error_log('Attendee dashboard error: ' . $e->getMessage());

    $error = 'Unable to load your dashboard information. Please try again later.';

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Attendee Dashboard | EventlyPH</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="attendee-dashboard">

    <?php if ($error !== ''): ?>

        <div class="attendee-error">
            <strong>!</strong>
            <p><?= htmlspecialchars($error) ?></p>
        </div>

    <?php elseif ($attendee): ?>

        <!-- =================================
             WELCOME BANNER
        ================================== -->

        <section class="attendee-welcome">

            <div class="welcome-content">

                <span class="welcome-label">
                    ATTENDEE DASHBOARD
                </span>

                <h1>
                    Welcome back,
                    <?= htmlspecialchars($attendee['first_name']) ?>!
                </h1>

                <p>
                    Keep track of your reservations, access your tickets,
                    and discover upcoming events with EventlyPH.
                </p>

                <a href="browse-events.php" class="welcome-button">
                    Explore Events <span>→</span>
                </a>

            </div>

            <div class="welcome-decoration">
                <div class="welcome-circle">🎟️</div>
            </div>

        </section>


        <!-- =================================
             DASHBOARD STATISTICS
        ================================== -->

        <section class="attendee-section">

            <div class="attendee-section-heading">
                <div>
                    <span class="attendee-eyebrow">OVERVIEW</span>
                    <h2>Your Event Summary</h2>
                    <p>A quick look at your event activities.</p>
                </div>
            </div>

            <div class="attendee-stat-grid">

                <div class="attendee-stat-card">
                    <div class="attendee-stat-icon reservation-stat-icon">
                        📋
                    </div>

                    <div>
                        <span>Total Reservations</span>
                        <h3><?= number_format($totalReservations) ?></h3>
                        <small>All your reservations</small>
                    </div>
                </div>

                <div class="attendee-stat-card">
                    <div class="attendee-stat-icon ticket-stat-icon">
                        🎟️
                    </div>

                    <div>
                        <span>Issued Tickets</span>
                        <h3><?= number_format($totalTickets) ?></h3>
                        <small>Tickets linked to your reservations</small>
                    </div>
                </div>

                <div class="attendee-stat-card">
                    <div class="attendee-stat-icon upcoming-stat-icon">
                        📅
                    </div>

                    <div>
                        <span>Upcoming Events</span>
                        <h3><?= number_format($upcomingEvents) ?></h3>
                        <small>Events you have reserved</small>
                    </div>
                </div>

            </div>

        </section>


        <!-- =================================
             QUICK ACCESS
        ================================== -->

        <section class="attendee-section">

            <div class="attendee-section-heading">
                <div>
                    <span class="attendee-eyebrow">QUICK ACCESS</span>
                    <h2>Your Event Activities</h2>
                    <p>Choose what you would like to do.</p>
                </div>
            </div>

            <div class="attendee-shortcuts">

                <a href="browse-events.php" class="attendee-shortcut">

                    <div class="shortcut-icon browse-icon">🎫</div>

                    <div class="shortcut-content">
                        <h3>Browse Events</h3>
                        <p>Discover events and reserve your tickets.</p>
                    </div>

                    <span class="shortcut-arrow">→</span>

                </a>

                <a href="my-reservations.php" class="attendee-shortcut">

                    <div class="shortcut-icon reservation-icon">📋</div>

                    <div class="shortcut-content">
                        <h3>My Reservations</h3>
                        <p>View your reservation details and status.</p>
                    </div>

                    <span class="shortcut-arrow">→</span>

                </a>

                <a href="my-tickets.php" class="attendee-shortcut">

                    <div class="shortcut-icon ticket-icon">🎟️</div>

                    <div class="shortcut-content">
                        <h3>My Tickets</h3>
                        <p>Access your issued tickets and ticket numbers.</p>
                    </div>

                    <span class="shortcut-arrow">→</span>

                </a>

            </div>

        </section>


        <!-- =================================
             RECENT RESERVATIONS
        ================================== -->

        <section class="attendee-section">

            <div class="attendee-section-heading attendee-heading-row">

                <div>
                    <span class="attendee-eyebrow">ACTIVITY</span>
                    <h2>Recent Reservations</h2>
                    <p>Your latest event reservation records.</p>
                </div>

                <a href="my-reservations.php" class="attendee-view-all">
                    View All →
                </a>

            </div>

            <?php if (empty($recentReservations)): ?>

                <div class="attendee-empty-state">
                    <div>📋</div>
                    <h3>No Reservations Yet</h3>
                    <p>You haven't made any event reservations.</p>

                    <a href="browse-events.php">
                        Browse Available Events
                    </a>
                </div>

            <?php else: ?>

                <div class="attendee-reservation-list">

                    <?php foreach ($recentReservations as $reservation): ?>

                        <article class="attendee-reservation-card">

                            <div class="reservation-main">

                                <div class="reservation-list-icon">
                                    🎫
                                </div>

                                <div class="reservation-details">
                                    <h3>
                                        <?= htmlspecialchars($reservation['event_title']) ?>
                                    </h3>

                                    <p>
                                        Event Date:
                                        <?= htmlspecialchars(date(
                                            'F d, Y',
                                            strtotime($reservation['event_date'])
                                        )) ?>
                                    </p>

                                    <small>
                                        Reserved:
                                        <?= htmlspecialchars(date(
                                            'M d, Y',
                                            strtotime($reservation['reservation_date'])
                                        )) ?>
                                    </small>
                                </div>

                            </div>

                            <div class="reservation-meta">

                                <span class="reservation-quantity">
                                    <?= (int) $reservation['quantity'] ?>
                                    <?= (int) $reservation['quantity'] === 1 ? 'Ticket' : 'Tickets' ?>
                                </span>

                                <span class="reservation-status">
                                    <?= htmlspecialchars($reservation['status']) ?>
                                </span>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>


        <!-- =================================
             LATEST ANNOUNCEMENTS
        ================================== -->

        <section class="attendee-section attendee-announcements-section">

            <div class="attendee-section-heading">

                <div>
                    <span class="attendee-eyebrow">STAY UPDATED</span>
                    <h2>Latest Announcements</h2>
                    <p>Important updates from event organizers.</p>
                </div>

            </div>

            <?php if (empty($announcements)): ?>

                <div class="attendee-empty-state">
                    <div>📢</div>
                    <h3>No Announcements Yet</h3>
                    <p>There are currently no published announcements.</p>
                </div>

            <?php else: ?>

                <div class="attendee-announcement-list">

                    <?php foreach ($announcements as $announcement): ?>

                        <article class="attendee-announcement-card">

                            <div class="announcement-card-top">

                                <div class="announcement-card-icon">
                                    📢
                                </div>

                                <div class="announcement-card-title">

                                    <h3>
                                        <?= htmlspecialchars($announcement['title']) ?>
                                    </h3>

                                    <span class="announcement-event-label">
                                        <?= htmlspecialchars($announcement['event_title']) ?>
                                    </span>

                                </div>

                            </div>

                            <div class="announcement-card-message">
                                <?= nl2br(htmlspecialchars($announcement['message'])) ?>
                            </div>

                            <div class="announcement-card-footer">

                                Published:
                                <?= htmlspecialchars(date(
                                    'M d, Y - h:i A',
                                    strtotime($announcement['published_at'])
                                )) ?>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>

    <?php endif; ?>

</main>

</body>
</html>

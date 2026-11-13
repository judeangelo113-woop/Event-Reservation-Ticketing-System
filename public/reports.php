
<?php

require_once __DIR__ . '/../includes/require_admin.php';
require_once __DIR__ . '/../config/Database.php';

$error = '';

$totalReservations = 0;
$totalAttendees = 0;
$totalTickets = 0;
$reservationSummary = [];
$eventReports = [];

try {

    $database = new Database();
    $db = $database->connect();

    // Total reservations
    $stmt = $db->query(
        "SELECT COUNT(*) AS total FROM reservations"
    );
    $totalReservations = (int) $stmt->fetch()['total'];

    // Total attendees
    $stmt = $db->query(
        "SELECT COUNT(*) AS total FROM attendees"
    );
    $totalAttendees = (int) $stmt->fetch()['total'];

    // Total tickets
    $stmt = $db->query(
        "SELECT COUNT(*) AS total FROM tickets"
    );
    $totalTickets = (int) $stmt->fetch()['total'];

    // Reservation status summary
    $stmt = $db->query(
        "SELECT status, COUNT(*) AS total
         FROM reservations
         GROUP BY status"
    );
    $reservationSummary = $stmt->fetchAll();

    // Event reservation report
    $stmt = $db->query(
        "SELECT
            e.title,
            e.event_date,
            COUNT(DISTINCT r.id) AS total_reservations,
            COUNT(DISTINCT t.id) AS total_tickets
         FROM events e
         LEFT JOIN reservations r ON e.id = r.event_id
         LEFT JOIN tickets t ON r.id = t.reservation_id
         GROUP BY e.id, e.title, e.event_date
         ORDER BY e.event_date DESC"
    );
    $eventReports = $stmt->fetchAll();

} catch (Exception $e) {

    $error = $e->getMessage();

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/style.css">

    <title>Reports | EventlyPH</title>
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container reports-page">

    <!-- Page Header -->
    <div class="reports-heading">

        <div>
            <span class="reports-eyebrow">ANALYTICS AND OVERVIEW</span>

            <h1>Reports</h1>

            <p>
                View reservation statistics, attendee records, and ticket
                distribution across your events.
            </p>
        </div>

        <a href="export-reports-pdf.php" class="reports-export-button">
            <span>↓</span>
            Download PDF Report
        </a>

    </div>

    <!-- Error Message -->
    <?php if ($error !== ''): ?>
        <div class="reports-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Statistics -->
    <section class="reports-statistics">

        <div class="reports-stat-card">
            <div class="reports-stat-icon reports-reservation-icon">
                <span>R</span>
            </div>

            <div>
                <p>Total Reservations</p>
                <h2><?= number_format($totalReservations) ?></h2>
                <span>Recorded reservations</span>
            </div>
        </div>

        <div class="reports-stat-card">
            <div class="reports-stat-icon reports-attendee-icon">
                <span>A</span>
            </div>

            <div>
                <p>Total Attendees</p>
                <h2><?= number_format($totalAttendees) ?></h2>
                <span>Registered attendees</span>
            </div>
        </div>

        <div class="reports-stat-card">
            <div class="reports-stat-icon reports-ticket-icon">
                <span>T</span>
            </div>

            <div>
                <p>Total Tickets</p>
                <h2><?= number_format($totalTickets) ?></h2>
                <span>Tickets issued</span>
            </div>
        </div>

    </section>

    <!-- Reservation Status Summary -->
    <section class="reports-card">

        <div class="reports-card-heading">
            <div>
                <h2>Reservation Status Summary</h2>
                <p>Overview of reservations grouped by their current status.</p>
            </div>
        </div>

        <div class="reports-table-wrapper">

            <table class="reports-table">

                <thead>
                    <tr>
                        <th>Reservation Status</th>
                        <th>Total Reservations</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($reservationSummary)): ?>

                        <tr>
                            <td colspan="2" class="reports-empty">
                                No reservation records found.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($reservationSummary as $summary): ?>

                            <tr>
                                <td>
                                    <span class="reports-status">
                                        <?= htmlspecialchars($summary['status']) ?>
                                    </span>
                                </td>

                                <td class="reports-number">
                                    <?= number_format((int) $summary['total']) ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

    <!-- Event Reservation Report -->
    <section class="reports-card">

        <div class="reports-card-heading">
            <div>
                <h2>Event Reservation Report</h2>
                <p>Reservation and ticket totals for each recorded event.</p>
            </div>
        </div>

        <div class="reports-table-wrapper">

            <table class="reports-table">

                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Event Date</th>
                        <th>Total Reservations</th>
                        <th>Tickets Issued</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($eventReports)): ?>

                        <tr>
                            <td colspan="4" class="reports-empty">
                                No event records found.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($eventReports as $report): ?>

                            <tr>
                                <td class="reports-event-title">
                                    <?= htmlspecialchars($report['title']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($report['event_date']) ?>
                                </td>

                                <td class="reports-number">
                                    <?= number_format((int) $report['total_reservations']) ?>
                                </td>

                                <td class="reports-number">
                                    <?= number_format((int) $report['total_tickets']) ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>

</body>
</html>

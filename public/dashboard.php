
<?php

require_once __DIR__ . '/../includes/require_admin.php';
require_once __DIR__ . '/../config/Database.php';

$database = new Database();
$db = $database->connect();

try {

    $stmt = $db->query("SELECT COUNT(*) AS total FROM events");
    $totalEvents = (int) $stmt->fetch()['total'];

    $stmt = $db->query(
        "SELECT COUNT(*) AS total FROM events WHERE status = 'OPEN'"
    );
    $openEvents = (int) $stmt->fetch()['total'];

    $stmt = $db->query("SELECT COUNT(*) AS total FROM attendees");
    $totalAttendees = (int) $stmt->fetch()['total'];

    $stmt = $db->query("SELECT COUNT(*) AS total FROM reservations");
    $totalReservations = (int) $stmt->fetch()['total'];

    $stmt = $db->query("SELECT COUNT(*) AS total FROM tickets");
    $totalTickets = (int) $stmt->fetch()['total'];

    $stmt = $db->query(
        "SELECT COUNT(*) AS total FROM tickets WHERE status = 'VALID'"
    );
    $validTickets = (int) $stmt->fetch()['total'];

    $stmt = $db->query(
        "SELECT COUNT(*) AS total FROM tickets WHERE status = 'CANCELLED'"
    );
    $cancelledTickets = (int) $stmt->fetch()['total'];

    $stmt = $db->query(
        "SELECT COALESCE(SUM(available_slots), 0) AS total
        FROM events WHERE status = 'OPEN'"
    );
    $totalAvailableSlots = (int) $stmt->fetch()['total'];

    $stmt = $db->query(
        "SELECT id, title, event_date, event_time, venue,
        available_slots, capacity
        FROM events
        WHERE event_date >= CURDATE()
        AND status = 'OPEN'
        ORDER BY event_date ASC, event_time ASC
        LIMIT 5"
    );
    $upcomingEvents = $stmt->fetchAll();

    $stmt = $db->query(
        "SELECT status, COUNT(*) AS total,
        ROUND(
            COUNT(*) * 100.0 /
            NULLIF((SELECT COUNT(*) FROM reservations), 0), 2
        ) AS percentage
        FROM reservations
        GROUP BY status"
    );
    $reservationSummary = $stmt->fetchAll();

    $stmt = $db->query(
        "SELECT
            e.id,
            e.title,
            e.event_date,
            e.capacity,
            e.available_slots,
            COALESCE(r.total_reservations, 0) AS total_reservations,
            COALESCE(t.total_tickets, 0) AS total_tickets
        FROM events e
        LEFT JOIN (
            SELECT event_id, COUNT(*) AS total_reservations
            FROM reservations
            GROUP BY event_id
        ) r ON e.id = r.event_id
        LEFT JOIN (
            SELECT r.event_id, COUNT(t.id) AS total_tickets
            FROM tickets t
            INNER JOIN reservations r ON t.reservation_id = r.id
            GROUP BY r.event_id
        ) t ON e.id = t.event_id
        ORDER BY total_reservations DESC"
    );
    $eventPerformance = $stmt->fetchAll();

    $stmt = $db->query(
        "SELECT status, COUNT(*) AS total,
        ROUND(
            COUNT(*) * 100.0 /
            NULLIF((SELECT COUNT(*) FROM tickets), 0), 2
        ) AS percentage
        FROM tickets
        GROUP BY status"
    );
    $ticketSummary = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Dashboard error: " . htmlspecialchars($e->getMessage()));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EventlyPH</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <main class="container admin-dashboard">

        <div class="dashboard-heading">
            <div>
                <span class="dashboard-eyebrow">ADMINISTRATION</span>
                <h1>Dashboard Overview</h1>
                <p>Monitor your events, attendees, reservations, and tickets.</p>
            </div>

            <div class="dashboard-date">
                <span>Today</span>
                <strong><?= date('F d, Y') ?></strong>
            </div>
        </div>

        <!-- SUMMARY CARDS -->

        <section class="dashboard-section">

            <div class="dashboard-section-heading">
                <h2>System Overview</h2>
                <p>Summary of your event ticketing system.</p>
            </div>

            <div class="dashboard-grid">

                <div class="dashboard-stat-card stat-blue">
                    <div class="stat-icon">◫</div>
                    <div class="stat-label">Total Events</div>
                    <div class="stat-number"><?= $totalEvents ?></div>
                    <div class="stat-note">All registered events</div>
                </div>

                <div class="dashboard-stat-card stat-green">
                    <div class="stat-icon">✓</div>
                    <div class="stat-label">Open Events</div>
                    <div class="stat-number"><?= $openEvents ?></div>
                    <div class="stat-note">Currently open for reservations</div>
                </div>

                <div class="dashboard-stat-card stat-purple">
                    <div class="stat-icon">♙</div>
                    <div class="stat-label">Total Attendees</div>
                    <div class="stat-number"><?= $totalAttendees ?></div>
                    <div class="stat-note">Registered attendees</div>
                </div>

                <div class="dashboard-stat-card stat-orange">
                    <div class="stat-icon">▤</div>
                    <div class="stat-label">Reservations</div>
                    <div class="stat-number"><?= $totalReservations ?></div>
                    <div class="stat-note">Total reservation records</div>
                </div>

                <div class="dashboard-stat-card stat-cyan">
                    <div class="stat-icon">▣</div>
                    <div class="stat-label">Total Tickets</div>
                    <div class="stat-number"><?= $totalTickets ?></div>
                    <div class="stat-note">All issued tickets</div>
                </div>

                <div class="dashboard-stat-card stat-green">
                    <div class="stat-icon">✓</div>
                    <div class="stat-label">Valid Tickets</div>
                    <div class="stat-number"><?= $validTickets ?></div>
                    <div class="stat-note">Currently valid tickets</div>
                </div>

                <div class="dashboard-stat-card stat-red">
                    <div class="stat-icon">×</div>
                    <div class="stat-label">Cancelled Tickets</div>
                    <div class="stat-number"><?= $cancelledTickets ?></div>
                    <div class="stat-note">Cancelled ticket records</div>
                </div>

                <div class="dashboard-stat-card stat-indigo">
                    <div class="stat-icon">▦</div>
                    <div class="stat-label">Available Slots</div>
                    <div class="stat-number"><?= $totalAvailableSlots ?></div>
                    <div class="stat-note">Slots in open events</div>
                </div>

            </div>

        </section>

        <!-- UPCOMING EVENTS -->

        <section class="dashboard-panel">

            <div class="dashboard-panel-heading">
                <div>
                    <h2>Upcoming Events</h2>
                    <p>The next available events open for reservation.</p>
                </div>
                <span class="panel-count"><?= count($upcomingEvents) ?> events</span>
            </div>

            <?php if (empty($upcomingEvents)): ?>

                <div class="dashboard-empty">No upcoming events found.</div>

            <?php else: ?>

                <div class="table-container">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Venue</th>
                                <th>Available Slots</th>
                                <th>Capacity</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($upcomingEvents as $event): ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <?= htmlspecialchars($event['title']) ?>
                                        </strong>
                                    </td>
                                    <td><?= htmlspecialchars($event['event_date']) ?></td>
                                    <td><?= htmlspecialchars($event['event_time']) ?></td>
                                    <td><?= htmlspecialchars($event['venue']) ?></td>
                                    <td>
                                        <span class="table-number">
                                            <?= (int) $event['available_slots'] ?>
                                        </span>
                                    </td>
                                    <td><?= (int) $event['capacity'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>

        </section>

        <!-- RESERVATION SUMMARY -->

        <section class="dashboard-panel">

            <div class="dashboard-panel-heading">
                <div>
                    <h2>Reservation Summary</h2>
                    <p>Distribution of reservations by status.</p>
                </div>
            </div>

            <?php if (empty($reservationSummary)): ?>

                <div class="dashboard-empty">No reservations found.</div>

            <?php else: ?>

                <div class="table-container">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Total Reservations</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($reservationSummary as $summary): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $status = $summary['status'];
                                        $badgeClass = 'badge-default';

                                        if ($status === 'CONFIRMED') {
                                            $badgeClass = 'badge-success';
                                        } elseif ($status === 'CANCELLED') {
                                            $badgeClass = 'badge-danger';
                                        }
                                        ?>

                                        <span class="dashboard-badge <?= $badgeClass ?>">
                                            <?= htmlspecialchars($status) ?>
                                        </span>
                                    </td>

                                    <td><?= (int) $summary['total'] ?></td>
                                    <td><?= number_format((float) $summary['percentage'], 2) ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>

        </section>

        <!-- EVENT PERFORMANCE -->

        <section class="dashboard-panel">

            <div class="dashboard-panel-heading">
                <div>
                    <h2>Event Performance Analytics</h2>
                    <p>Reservations, tickets issued, and available slots for each event.</p>
                </div>
            </div>

            <?php if (empty($eventPerformance)): ?>

                <div class="dashboard-empty">No event performance data available.</div>

            <?php else: ?>

                <div class="table-container">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Date</th>
                                <th>Reservations</th>
                                <th>Tickets Issued</th>
                                <th>Capacity</th>
                                <th>Available Slots</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($eventPerformance as $event): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($event['title']) ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($event['event_date']) ?></td>
                                    <td><?= (int) $event['total_reservations'] ?></td>
                                    <td><?= (int) $event['total_tickets'] ?></td>
                                    <td><?= (int) $event['capacity'] ?></td>
                                    <td><?= (int) $event['available_slots'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>

        </section>

        <!-- TICKET STATUS -->

        <section class="dashboard-panel">

            <div class="dashboard-panel-heading">
                <div>
                    <h2>Ticket Status Summary</h2>
                    <p>Overview of tickets based on their current status.</p>
                </div>
            </div>

            <?php if (empty($ticketSummary)): ?>

                <div class="dashboard-empty">No ticket records found.</div>

            <?php else: ?>

                <div class="table-container">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Total Tickets</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($ticketSummary as $summary): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $status = $summary['status'];
                                        $badgeClass = 'badge-default';

                                        if ($status === 'VALID') {
                                            $badgeClass = 'badge-success';
                                        } elseif ($status === 'CANCELLED') {
                                            $badgeClass = 'badge-danger';
                                        }
                                        ?>

                                        <span class="dashboard-badge <?= $badgeClass ?>">
                                            <?= htmlspecialchars($status) ?>
                                        </span>
                                    </td>

                                    <td><?= (int) $summary['total'] ?></td>
                                    <td><?= number_format((float) $summary['percentage'], 2) ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>

        </section>

    </main>

    <script src="../assets/js/navigation.js"></script>

</body>
</html>

<?php

require_once __DIR__ . '/../config/Database.php';

$database = new Database();
$db = $database->connect();

try {

    /*
     * Total events
     */
    $stmt = $db->query(
        "SELECT COUNT(*) AS total
         FROM events"
    );

    $totalEvents = (int) $stmt->fetch()['total'];


    /*
     * Open events
     */
    $stmt = $db->query(
        "SELECT COUNT(*) AS total
         FROM events
         WHERE status = 'OPEN'"
    );

    $openEvents = (int) $stmt->fetch()['total'];


    /*
     * Total attendees
     */
    $stmt = $db->query(
        "SELECT COUNT(*) AS total
         FROM attendees"
    );

    $totalAttendees = (int) $stmt->fetch()['total'];


    /*
     * Total reservations
     */
    $stmt = $db->query(
        "SELECT COUNT(*) AS total
         FROM reservations"
    );

    $totalReservations = (int) $stmt->fetch()['total'];


    /*
     * Total tickets
     */
    $stmt = $db->query(
        "SELECT COUNT(*) AS total
         FROM tickets"
    );

    $totalTickets = (int) $stmt->fetch()['total'];


    /*
     * Total available slots
     */
    $stmt = $db->query(
        "SELECT COALESCE(SUM(available_slots), 0) AS total
         FROM events
         WHERE status = 'OPEN'"
    );

    $totalAvailableSlots = (int) $stmt->fetch()['total'];


    /*
     * Upcoming events
     */
    $stmt = $db->query(
        "SELECT
            id,
            title,
            event_date,
            event_time,
            venue,
            available_slots,
            capacity
         FROM events
         WHERE event_date >= CURDATE()
           AND status = 'OPEN'
         ORDER BY event_date ASC, event_time ASC
         LIMIT 5"
    );

    $upcomingEvents = $stmt->fetchAll();


    /*
     * Reservation summary
     */
    $stmt = $db->query(
        "SELECT
            status,
            COUNT(*) AS total
         FROM reservations
         GROUP BY status"
    );

    $reservationSummary = $stmt->fetchAll();


} catch (PDOException $e) {

    die(
        "Dashboard error: " .
        htmlspecialchars($e->getMessage())
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <?php
        require_once __DIR__ . '/../includes/navbar.php';
    ?>

    <main class="container">
        <div class="page-header">

            <div>
                <h1>Dashboard</h1>

                <p class="page-description">
                    Overview of events, attendees, reservations, tickets, and available slots.
                </p>
            </div>

        </div>



        <div class="dashboard-grid">

            <div class="card">

                <h3>Total Events</h3>

                <div class="number">
                    <?= $totalEvents ?>
                </div>

            </div>


            <div class="card">

                <h3>Open Events</h3>

                <div class="number">
                    <?= $openEvents ?>
                </div>

            </div>


            <div class="card">

                <h3>Total Attendees</h3>

                <div class="number">
                    <?= $totalAttendees ?>
                </div>

            </div>


            <div class="card">

                <h3>Total Reservations</h3>

                <div class="number">
                    <?= $totalReservations ?>
                </div>

            </div>


            <div class="card">

                <h3>Total Tickets</h3>

                <div class="number">
                    <?= $totalTickets ?>
                </div>

            </div>


            <div class="card">

                <h3>Available Slots</h3>

                <div class="number">
                    <?= $totalAvailableSlots ?>
                </div>

            </div>

        </div>

        <div class="card">

            <h2>Upcoming Events</h2>

            <p class="page-description">
                The next available events currently open for reservation.
            </p>

            <?php if (empty($upcomingEvents)): ?>

                <p>No upcoming events found.</p>

            <?php else: ?>

                <div class="table-container">

                    <table>

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
                                        <?= htmlspecialchars($event['title']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($event['event_date']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($event['event_time']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($event['venue']) ?>
                                    </td>

                                    <td>
                                        <?= (int) $event['available_slots'] ?>
                                    </td>

                                    <td>
                                        <?= (int) $event['capacity'] ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>


        <br>


        <!-- Reservation Summary -->

        <div class="card">

            <h2>Reservation Summary</h2>

            <?php if (empty($reservationSummary)): ?>

                <p>No reservations found.</p>

            <?php else: ?>

                <div class="table-container">

                    <table>

                        <thead>

                            <tr>

                                <th>Status</th>
                                <th>Total</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($reservationSummary as $summary): ?>

                                <tr>

                                    <td>

                                        <?php if ($summary['status'] === 'CONFIRMED'): ?>

                                            <span class="status-badge status-open">
                                                CONFIRMED
                                            </span>

                                        <?php elseif ($summary['status'] === 'CANCELLED'): ?>

                                            <span class="status-badge status-cancelled">
                                                CANCELLED
                                            </span>

                                        <?php else: ?>

                                            <span class="status-badge status-closed">
                                                <?= htmlspecialchars($summary['status']) ?>
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td>
                                        <?= (int) $summary['total'] ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>

    </main>

    <script src="../assets/js/navigation.js"></script>

</body>

</html>
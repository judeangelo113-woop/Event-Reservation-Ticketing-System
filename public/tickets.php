<?php

require_once __DIR__ . '/../classes/Ticket.php';

$ticketModel = new Ticket();

$error = '';

try {

    $tickets = $ticketModel->getAllTickets();

} catch (Exception $e) {

    $tickets = [];
    $error = "Unable to load tickets.";
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

    <title>Ticket Management</title>

</head>

<body>

    <h1>Ticket Management</h1>

    <p>
        <a href="events.php">Events</a>
        |
        <a href="attendees.php">Attendees</a>
        |
        <a href="reservations.php">Reservations</a>
    </p>

    <hr>

    <?php if ($error !== ''): ?>

        <p>
            <strong>Error:</strong>
            <?php echo htmlspecialchars($error); ?>
        </p>

    <?php elseif (empty($tickets)): ?>

        <p>No tickets have been issued yet.</p>

    <?php else: ?>

        <table border="1" cellpadding="8" cellspacing="0">

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Ticket Number</th>
                    <th>Event</th>
                    <th>Attendee</th>
                    <th>Email</th>
                    <th>Quantity</th>
                    <th>Issued At</th>
                    <th>Status</th>
                    <th>Actions</th>

                </tr>

            </thead>

            <tbody>

                <?php foreach ($tickets as $ticket): ?>

                    <tr>

                        <td>
                            <?php echo $ticket['id']; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars(
                                $ticket['ticket_number']
                            ); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars(
                                $ticket['event_title']
                            ); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars(
                                $ticket['attendee_name']
                            ); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars(
                                $ticket['attendee_email']
                            ); ?>
                        </td>

                        <td>
                            <?php echo $ticket['quantity']; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars(
                                $ticket['issued_at']
                            ); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars(
                                $ticket['status']
                            ); ?>
                        </td>

                        <td>

                            <a href="update-ticket.php?id=<?php echo $ticket['id']; ?>">
                                Edit
                            </a>

                            |

                            <a href="delete-ticket.php?id=<?php echo $ticket['id']; ?>">
                                Delete
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

</body>

</html>
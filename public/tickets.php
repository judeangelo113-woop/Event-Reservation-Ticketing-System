<?php

require_once __DIR__ . '/../classes/Ticket.php';

$ticketModel = new Ticket();

$error = '';

try {
    $tickets = $ticketModel->getAllTickets();
} catch (Exception $e) {
    $tickets = [];
    $error = 'Unable to load tickets.';
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

    <link rel="stylesheet" href="../assets/css/style.css">

    <title>Ticket Management</title>

</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">

    <div class="page-header">

        <div>

            <h1>Ticket Management</h1>

            <p class="page-description">
                View and manage issued tickets and their current status.
            </p>

        </div>

    </div>

    <?php if ($error !== ''): ?>

        <div class="error">
            <strong>Error:</strong>
            <?= htmlspecialchars($error) ?>
        </div>

    <?php elseif (empty($tickets)): ?>

        <div class="card">

            <h3>No Tickets Yet</h3>

            <p>
                No tickets have been issued yet.
            </p>

            <a href="events.php" class="btn btn-primary">
                View Events
            </a>

        </div>

    <?php else: ?>

        <div class="table-container">

            <table>

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
                                <?= (int) $ticket['id'] ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($ticket['ticket_number']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($ticket['event_title']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($ticket['attendee_name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($ticket['attendee_email']) ?>
                            </td>

                            <td>
                                <?= (int) $ticket['quantity'] ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($ticket['issued_at']) ?>
                            </td>

                            <td>

                                <?php if ($ticket['status'] === 'VALID'): ?>

                                    <span class="status-badge status-open">
                                        VALID
                                    </span>

                                <?php elseif ($ticket['status'] === 'CANCELLED'): ?>

                                    <span class="status-badge status-cancelled">
                                        CANCELLED
                                    </span>

                                <?php else: ?>

                                    <span class="status-badge status-closed">
                                        <?= htmlspecialchars($ticket['status']) ?>
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <a
                                    href="update-ticket.php?id=<?= (int) $ticket['id'] ?>"
                                    class="btn btn-secondary"
                                >
                                    Edit
                                </a>

                                <a
                                    href="delete-ticket.php?id=<?= (int) $ticket['id'] ?>"
                                    class="btn btn-danger"
                                >
                                    Delete
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</div>

<script src="../assets/js/navigation.js"></script>

</body>

</html>
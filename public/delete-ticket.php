<?php

require_once __DIR__ . '/../classes/Ticket.php';

$ticketModel = new Ticket();

$ticketId = (int) ($_GET['id'] ?? 0);

if ($ticketId <= 0) {
    die("Invalid ticket ID.");
}

$ticket = $ticketModel->getTicketById($ticketId);

if ($ticket === null) {
    die("Ticket not found.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $ticketModel->setId($ticketId);

        if ($ticketModel->delete()) {

            header("Location: tickets.php?message=deleted");
            exit;

        } else {

            $error = "Unable to delete ticket.";
        }

    } catch (Exception $e) {

        $error = $e->getMessage();
    }
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

    <title>Delete Ticket</title>

</head>

<body>

    <h1>Delete Ticket</h1>

    <?php if ($error !== ''): ?>

        <p>
            <strong>Error:</strong>
            <?php echo htmlspecialchars($error); ?>
        </p>

    <?php endif; ?>

    <p>
        Are you sure you want to delete this ticket?
    </p>

    <p>
        <strong>Ticket Number:</strong>
        <?php echo htmlspecialchars($ticket['ticket_number']); ?>
    </p>

    <p>
        <strong>Event:</strong>
        <?php echo htmlspecialchars($ticket['event_title']); ?>
    </p>

    <p>
        <strong>Attendee:</strong>
        <?php echo htmlspecialchars($ticket['attendee_name']); ?>
    </p>

    <form method="POST">

        <button type="submit">
            Yes, Delete Ticket
        </button>

        <a href="tickets.php">
            Cancel
        </a>

    </form>

</body>

</html>
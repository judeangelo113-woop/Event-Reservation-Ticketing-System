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

<?php

require_once __DIR__ . '/../classes/Ticket.php';

$ticketModel = new Ticket();

$ticketId = (int) ($_GET['id'] ?? 0);

if ($ticketId <= 0) {
    die('Invalid ticket ID.');
}

$ticket = $ticketModel->getTicketById($ticketId);

if ($ticket === null) {
    die('Ticket not found.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $ticketModel->setId($ticketId);

        if ($ticketModel->delete()) {

            header(
                'Location: tickets.php?message=' .
                urlencode('Ticket deleted successfully')
            );

            exit;

        } else {

            $error = 'Unable to delete ticket.';
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

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container">

    <div class="form-header">

        <h1>Delete Ticket</h1>

        <p>
            Review the ticket details before deleting it.
        </p>

    </div>

    <a
        href="tickets.php"
        class="btn btn-secondary back-button"
    >
        ← Back to Ticket Management
    </a>

    <?php if ($error !== ''): ?>

        <div class="error">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>

    <div class="form-container">

        <h2>Confirm Deletion</h2>

        <p>
            Are you sure you want to delete this ticket?
        </p>

        <div class="delete-details">

            <p>
                <strong>Ticket Number:</strong>
                <?= htmlspecialchars($ticket['ticket_number']) ?>
            </p>

            <p>
                <strong>Event:</strong>
                <?= htmlspecialchars($ticket['event_title']) ?>
            </p>

            <p>
                <strong>Attendee:</strong>
                <?= htmlspecialchars($ticket['attendee_name']) ?>
            </p>

            <p>
                <strong>Email:</strong>
                <?= htmlspecialchars($ticket['attendee_email']) ?>
            </p>

            <p>
                <strong>Status:</strong>
                <?= htmlspecialchars($ticket['status']) ?>
            </p>

            <p>
                <strong>Issued At:</strong>
                <?= htmlspecialchars($ticket['issued_at']) ?>
            </p>

        </div>

        <form method="POST">

            <button
                type="submit"
                class="btn btn-danger"
            >
                Yes, Delete Ticket
            </button>

            <a
                href="tickets.php"
                class="btn btn-secondary"
            >
                Cancel
            </a>

        </form>

    </div>

</main>

<script src="../assets/js/navigation.js"></script>

</body>

</html>
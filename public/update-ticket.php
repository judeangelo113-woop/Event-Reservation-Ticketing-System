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

    $ticketNumber = trim($_POST['ticket_number'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if ($ticketNumber === '') {

        $error = "Ticket number is required.";

    } elseif (!in_array(
        $status,
        ['VALID', 'CANCELLED'],
        true
    )) {

        $error = "Invalid ticket status.";

    } else {

        try {

            $ticketModel->setId($ticketId);
            $ticketModel->setTicketNumber($ticketNumber);
            $ticketModel->setStatus($status);

            if ($ticketModel->update()) {

                header("Location: tickets.php?message=updated");
                exit;

            } else {

                $error = "Unable to update ticket.";
            }

        } catch (Exception $e) {

            $error = $e->getMessage();
        }
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

    <title>Update Ticket</title>

</head>

<body>

    <h1>Update Ticket</h1>

    <?php if ($error !== ''): ?>

        <p>
            <strong>Error:</strong>
            <?php echo htmlspecialchars($error); ?>
        </p>

    <?php endif; ?>

    <p>
        <strong>Event:</strong>
        <?php echo htmlspecialchars($ticket['event_title']); ?>
    </p>

    <p>
        <strong>Attendee:</strong>
        <?php echo htmlspecialchars($ticket['attendee_name']); ?>
    </p>

    <form method="POST">

        <div>

            <label for="ticket_number">
                Ticket Number:
            </label>

            <br>

            <input
                type="text"
                id="ticket_number"
                name="ticket_number"
                value="<?php echo htmlspecialchars(
                    $_POST['ticket_number']
                    ?? $ticket['ticket_number']
                ); ?>"
                required
            >

        </div>

        <br>

        <div>

            <label for="status">
                Status:
            </label>

            <br>

            <select id="status" name="status">

                <option
                    value="VALID"
                    <?php
                    $currentStatus =
                        $_POST['status']
                        ?? $ticket['status'];

                    echo $currentStatus === 'VALID'
                        ? 'selected'
                        : '';
                    ?>
                >
                    VALID
                </option>

                <option
                    value="CANCELLED"
                    <?php
                    echo $currentStatus === 'CANCELLED'
                        ? 'selected'
                        : '';
                    ?>
                >
                    CANCELLED
                </option>

            </select>

        </div>

        <br>

        <button type="submit">
            Update Ticket
        </button>

    </form>

    <br>

    <a href="tickets.php">
        Back to Tickets
    </a>

</body>

</html>
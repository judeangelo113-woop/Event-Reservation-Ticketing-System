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

$ticketNumber = $ticket['ticket_number'];
$status = $ticket['status'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ticketNumber = trim($_POST['ticket_number'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if ($ticketNumber === '') {

        $error = 'Ticket number is required.';

    } elseif (!in_array(
        $status,
        ['VALID', 'CANCELLED'],
        true
    )) {

        $error = 'Invalid ticket status.';

    } else {

        try {

            $ticketModel->setId($ticketId);
            $ticketModel->setTicketNumber($ticketNumber);
            $ticketModel->setStatus($status);

            if ($ticketModel->update()) {

                header(
                    'Location: tickets.php?message=' .
                    urlencode('Ticket updated successfully')
                );

                exit;

            } else {

                $error = 'Unable to update ticket.';
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

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

    <title>Update Ticket</title>

</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">

    <div class="form-header">

        <h1>Update Ticket</h1>

        <p>
            Update the ticket number or ticket status.
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

        <div class="delete-details">

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
                <strong>Issued At:</strong>
                <?= htmlspecialchars($ticket['issued_at']) ?>
            </p>

        </div>

        <form method="POST">

            <div class="form-group">

                <label for="ticket_number">
                    Ticket Number
                </label>

                <input
                    type="text"
                    id="ticket_number"
                    name="ticket_number"
                    value="<?= htmlspecialchars($ticketNumber) ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label for="status">
                    Status
                </label>

                <select
                    id="status"
                    name="status"
                    required
                >

                    <option
                        value="VALID"
                        <?= $status === 'VALID' ? 'selected' : '' ?>
                    >
                        VALID
                    </option>

                    <option
                        value="CANCELLED"
                        <?= $status === 'CANCELLED' ? 'selected' : '' ?>
                    >
                        CANCELLED
                    </option>

                </select>

            </div>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Update Ticket
            </button>

            <a
                href="tickets.php"
                class="btn btn-secondary"
            >
                Cancel
            </a>

        </form>

    </div>

</div>

<script src="../assets/js/navigation.js"></script>

</body>

</html>
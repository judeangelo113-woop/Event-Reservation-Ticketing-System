
<?php

require_once __DIR__ . '/../includes/require_admin.php';
require_once __DIR__ . '/../classes/Ticket.php';

$ticketModel = new Ticket();

$ticketId = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

if ($ticketId <= 0) {
    die('Invalid ticket ID.');
}

$ticket = $ticketModel->getTicketById($ticketId);

if ($ticket === null) {
    die('Ticket not found.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $postedId = (int) ($_POST['id'] ?? 0);

    if ($postedId !== $ticketId) {

        $error = 'Invalid ticket ID.';

    } elseif (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {

        $error = 'Invalid or expired request. Please refresh the page and try again.';

    } else {

        try {

            $ticketModel->setId($ticketId);

            if ($ticketModel->delete()) {

                header(
                    'Location: ../public/tickets.php?message=' .
                    urlencode('Ticket deleted successfully')
                );

                exit;

            } else {

                $error = 'Unable to delete ticket. Please try again.';
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Delete Ticket | EventlyPH</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container ticket-delete-page">

    <div class="ticket-delete-header">

        <span class="ticket-delete-label">
            TICKET MANAGEMENT
        </span>

        <h1>Delete Ticket</h1>

        <p>
            Review the ticket information before confirming deletion.
        </p>

    </div>

    <a href="../public/tickets.php" class="ticket-delete-back">
        &larr; Back to Ticket Management
    </a>

    <?php if ($error !== ''): ?>

        <div class="ticket-delete-error">

            <strong>Unable to delete ticket:</strong>

            <p><?= htmlspecialchars($error) ?></p>

        </div>

    <?php endif; ?>

    <div class="ticket-delete-card">

        <div class="ticket-delete-icon">!</div>

        <h2>Confirm Ticket Deletion</h2>

        <p class="ticket-delete-description">
            Are you sure you want to delete this ticket?
            Review the details below before proceeding.
        </p>

        <div class="ticket-delete-details">

            <div class="ticket-delete-detail">
                <span>Ticket ID</span>
                <strong>#<?= (int) $ticketId ?></strong>
            </div>

            <div class="ticket-delete-detail">
                <span>Ticket Number</span>
                <strong>
                    <?= htmlspecialchars($ticket['ticket_number']) ?>
                </strong>
            </div>

            <div class="ticket-delete-detail">
                <span>Event</span>
                <strong>
                    <?= htmlspecialchars($ticket['event_title']) ?>
                </strong>
            </div>

            <div class="ticket-delete-detail">
                <span>Attendee</span>
                <strong>
                    <?= htmlspecialchars($ticket['attendee_name']) ?>
                </strong>
            </div>

            <div class="ticket-delete-detail">
                <span>Email Address</span>
                <strong>
                    <?= htmlspecialchars($ticket['attendee_email']) ?>
                </strong>
            </div>

            <div class="ticket-delete-detail">
                <span>Ticket Status</span>
                <strong>
                    <span class="ticket-delete-status <?=
                        $ticket['status'] === 'VALID'
                            ? 'is-valid'
                            : 'is-cancelled'
                    ?>">
                        <?= htmlspecialchars($ticket['status']) ?>
                    </span>
                </strong>
            </div>

            <div class="ticket-delete-detail">
                <span>Issued At</span>
                <strong>
                    <?= htmlspecialchars($ticket['issued_at']) ?>
                </strong>
            </div>

        </div>

        <div class="ticket-delete-warning">
            <strong>Warning:</strong>
            Deleting this ticket removes its ticket record.
            The associated reservation is not intended to be deleted
            by this action.
        </div>

        <form method="POST" action="">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(generateCSRFToken()) ?>"
            >

            <input
                type="hidden"
                name="id"
                value="<?= (int) $ticketId ?>"
            >

            <div class="ticket-delete-actions">

                <a href="../public/tickets.php"
                class="ticket-delete-cancel">
                    Cancel
                </a>

                <button type="submit" class="ticket-delete-confirm">
                    Yes, Delete Ticket
                </button>

            </div>

        </form>

    </div>

</main>

<script src="../assets/js/navigation.js"></script>

</body>
</html>

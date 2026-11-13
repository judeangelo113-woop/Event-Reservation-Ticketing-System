
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

$ticketNumber = $ticket['ticket_number'];
$status = $ticket['status'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $postedId = (int) ($_POST['id'] ?? 0);

    if ($postedId !== $ticketId) {

        $error = 'Invalid ticket ID.';

    } elseif (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {

        $error = 'Invalid or expired request. Please refresh the page and try again.';

    } else {

        $ticketNumber = trim($_POST['ticket_number'] ?? '');
        $status = trim($_POST['status'] ?? '');

        if ($ticketNumber === '') {

            $error = 'Ticket number is required.';

        } elseif (!in_array($status, ['VALID', 'CANCELLED'], true)) {

            $error = 'Invalid ticket status.';

        } else {

            try {

                $ticketModel->setId($ticketId);
                $ticketModel->setTicketNumber($ticketNumber);
                $ticketModel->setStatus($status);

                if ($ticketModel->update()) {

                    header(
                        'Location: ../public/tickets.php?message=' .
                        urlencode('Ticket updated successfully')
                    );

                    exit;

                } else {

                    $error = 'Unable to update ticket. Please try again.';
                }

            } catch (Exception $e) {

                $error = $e->getMessage();
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Update Ticket | EventlyPH</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container ticket-form-page">

    <div class="ticket-form-header">

        <span class="ticket-form-label">
            TICKET MANAGEMENT
        </span>

        <h1>Update Ticket</h1>

        <p>
            Modify the ticket number and status information.
        </p>

    </div>

    <a href="../public/tickets.php" class="ticket-form-back">
        &larr; Back to Ticket Management
    </a>

    <?php if ($error !== ''): ?>

        <div class="ticket-form-error">
            <strong>Unable to update ticket:</strong>
            <p><?= htmlspecialchars($error) ?></p>
        </div>

    <?php endif; ?>

    <div class="ticket-form-card">

        <div class="ticket-form-card-header">
            <div>
                <h2>Edit Ticket Information</h2>
                <p>Ticket ID: #<?= (int) $ticketId ?></p>
            </div>
        </div>

        <div class="ticket-form-information">

            <h3>Ticket Details</h3>

            <div class="ticket-form-info-grid">

                <div class="ticket-form-info-item">
                    <span>Event</span>
                    <strong>
                        <?= htmlspecialchars($ticket['event_title']) ?>
                    </strong>
                </div>

                <div class="ticket-form-info-item">
                    <span>Attendee</span>
                    <strong>
                        <?= htmlspecialchars($ticket['attendee_name']) ?>
                    </strong>
                </div>

                <div class="ticket-form-info-item">
                    <span>Email Address</span>
                    <strong>
                        <?= htmlspecialchars($ticket['attendee_email']) ?>
                    </strong>
                </div>

                <div class="ticket-form-info-item">
                    <span>Issued At</span>
                    <strong>
                        <?= htmlspecialchars($ticket['issued_at']) ?>
                    </strong>
                </div>

            </div>

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

            <div class="ticket-form-group">

                <label for="ticket_number">
                    Ticket Number
                </label>

                <input
                    type="text"
                    id="ticket_number"
                    name="ticket_number"
                    value="<?= htmlspecialchars($ticketNumber) ?>"
                    placeholder="Enter ticket number"
                    required
                >

                <small>
                    Enter the ticket number assigned to this ticket.
                </small>

            </div>

            <div class="ticket-form-group">

                <label for="status">
                    Ticket Status
                </label>

                <select id="status" name="status" required>

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

                <small>
                    Select whether the ticket is valid or cancelled.
                </small>

            </div>

            <div class="ticket-form-actions">

                <a href="../public/tickets.php" class="ticket-form-cancel">
                    Cancel
                </a>

                <button type="submit" class="ticket-form-submit">
                    Update Ticket
                </button>

            </div>

        </form>

    </div>

</main>

<script src="../assets/js/navigation.js"></script>

</body>
</html>

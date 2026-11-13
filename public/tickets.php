
<?php

require_once __DIR__ . '/../includes/require_admin.php';
require_once __DIR__ . '/../classes/Ticket.php';

$tickets = [];
$error = null;

$totalTickets = 0;
$perPage = 5;
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$totalPages = 1;
$offset = 0;

try {

    $ticketModel = new Ticket();

    // Count all tickets
    $totalTickets = $ticketModel->countAllTickets();

    // Calculate pagination
    $totalPages = max(1, (int) ceil($totalTickets / $perPage));
    $currentPage = min($currentPage, $totalPages);
    $offset = ($currentPage - 1) * $perPage;

    // Retrieve tickets
    $tickets = $ticketModel->getAllTickets($perPage, $offset);

} catch (Exception $e) {

    $error = 'Unable to load tickets. Please try again later.';

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/style.css">

    <title>Ticket Management | EventlyPH</title>
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container tickets-page">

    <!-- PAGE HEADER -->
    <section class="tickets-heading">

        <div>
            <span class="tickets-eyebrow">TICKET MANAGEMENT</span>
            <h1>Ticket Management</h1>
            <p>View and manage issued tickets, attendee information, and ticket status.</p>
        </div>

    </section>

    <!-- SUMMARY -->
    <section class="tickets-summary">

        <div class="tickets-summary-icon">🎫</div>

        <div>
            <span>Total Issued Tickets</span>
            <h2><?= number_format($totalTickets) ?></h2>
        </div>

    </section>

    <!-- ERROR -->
    <?php if ($error !== null): ?>

        <div class="tickets-error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <!-- TICKET RECORDS -->
    <section class="tickets-content-card">

        <div class="tickets-card-heading">
            <div>
                <h2>Issued Ticket Records</h2>
                <p>Review ticket numbers, events, attendees, and validity.</p>
            </div>
        </div>

        <?php if ($error === null && empty($tickets)): ?>

            <div class="tickets-empty">
                <div>🎟️</div>
                <h3>No Tickets Yet</h3>
                <p>No tickets have been issued yet.</p>

                <a href="events.php" class="tickets-view-events-button">
                    View Events
                </a>
            </div>

        <?php elseif ($error === null): ?>

            <div class="tickets-table-wrapper">

                <table class="tickets-table">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ticket Number</th>
                            <th>Event</th>
                            <th>Attendee</th>
                            <th>Email Address</th>
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
                                    <span class="tickets-id">
                                        #<?= (int) $ticket['id'] ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="tickets-number">
                                        <?= htmlspecialchars($ticket['ticket_number']) ?>
                                    </span>
                                </td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($ticket['event_title']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars($ticket['attendee_name']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($ticket['attendee_email']) ?>
                                </td>

                                <td>
                                    <span class="tickets-quantity">
                                        <?= (int) $ticket['quantity'] ?>
                                    </span>
                                </td>

                                <td>
                                    <?= htmlspecialchars($ticket['issued_at']) ?>
                                </td>

                                <td>

                                    <?php if ($ticket['status'] === 'VALID'): ?>

                                        <span class="tickets-status tickets-valid">
                                            VALID
                                        </span>

                                    <?php elseif ($ticket['status'] === 'CANCELLED'): ?>

                                        <span class="tickets-status tickets-cancelled">
                                            CANCELLED
                                        </span>

                                    <?php else: ?>

                                        <span class="tickets-status tickets-other">
                                            <?= htmlspecialchars($ticket['status']) ?>
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>
                                    <div class="tickets-actions">

                                        <a
                                            href="update-ticket.php?id=<?= (int) $ticket['id'] ?>"
                                            class="tickets-edit-button"
                                        >
                                            Edit
                                        </a>

                                        <a
                                            href="delete-ticket.php?id=<?= (int) $ticket['id'] ?>"
                                            class="tickets-delete-button"
                                        >
                                            Delete
                                        </a>

                                    </div>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

        <!-- PAGINATION -->
        <?php if ($error === null && $totalTickets > 0): ?>

            <div class="tickets-pagination-footer">

                <div class="tickets-pagination-info">
                    Showing
                    <strong><?= $offset + 1 ?></strong>
                    to
                    <strong><?= min($offset + $perPage, $totalTickets) ?></strong>
                    of
                    <strong><?= $totalTickets ?></strong>
                    tickets
                </div>

                <?php if ($totalPages > 1): ?>

                    <div class="tickets-pagination">

                        <?php if ($currentPage > 1): ?>

                            <a href="?page=<?= $currentPage - 1 ?>">
                                Previous
                            </a>

                        <?php endif; ?>

                        <?php for ($page = 1; $page <= $totalPages; $page++): ?>

                            <a
                                href="?page=<?= $page ?>"
                                class="<?= $page === $currentPage ? 'active' : '' ?>"
                            >
                                <?= $page ?>
                            </a>

                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>

                            <a href="?page=<?= $currentPage + 1 ?>">
                                Next
                            </a>

                        <?php endif; ?>

                    </div>

                <?php endif; ?>

            </div>

        <?php endif; ?>

    </section>

</main>

<script src="../assets/js/navigation.js"></script>

</body>
</html>


<?php

require_once __DIR__ . '/../includes/require_admin.php';
require_once __DIR__ . '/../classes/Reservation.php';

$reservations = [];
$totalReservations = 0;
$totalPages = 1;
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 5;
$offset = 0;
$error = null;

$message = trim($_GET['message'] ?? '');

try {

    $reservationModel = new Reservation();

    // Count all reservations
    $totalReservations = $reservationModel->countAllReservations();

    // Calculate pagination
    $totalPages = max(1, (int) ceil($totalReservations / $perPage));
    $currentPage = min($currentPage, $totalPages);
    $offset = ($currentPage - 1) * $perPage;

    // Retrieve reservations for current page
    $reservations = $reservationModel->getAllReservations(
        $perPage,
        $offset
    );

} catch (Exception $e) {

    $error = $e->getMessage();

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/style.css">

    <title>Reservation Management | EventlyPH</title>
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container reservations-page">

    <!-- PAGE HEADER -->
    <section class="reservations-heading">

        <div>
            <span class="reservations-eyebrow">BOOKING MANAGEMENT</span>
            <h1>Reservation Management</h1>
            <p>View and manage event reservations, attendees, quantities, and statuses.</p>
        </div>

    </section>

    <!-- SUMMARY CARD -->
    <section class="reservations-summary">

        <div class="reservations-summary-icon">🎟️</div>

        <div>
            <span>Total Reservations</span>
            <h2><?= number_format($totalReservations) ?></h2>
        </div>

    </section>

    <!-- SUCCESS MESSAGE -->
    <?php if ($message !== ''): ?>

        <div class="reservations-success">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>

    <!-- ERROR MESSAGE -->
    <?php if ($error !== null): ?>

        <div class="reservations-error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <!-- RESERVATION RECORDS -->
    <section class="reservations-content-card">

        <div class="reservations-card-heading">
            <div>
                <h2>Reservation Records</h2>
                <p>Review attendee bookings and manage reservation details.</p>
            </div>
        </div>

        <?php if ($error === null && empty($reservations)): ?>

            <div class="reservations-empty">
                <div>📋</div>
                <h3>No reservations found</h3>
                <p>There are currently no reservation records available.</p>
            </div>

        <?php elseif ($error === null): ?>

            <div class="reservations-table-wrapper">

                <table class="reservations-table">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Event</th>
                            <th>Attendee</th>
                            <th>Email Address</th>
                            <th>Quantity</th>
                            <th>Reservation Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($reservations as $reservation): ?>

                            <tr>

                                <td>
                                    <span class="reservations-id">
                                        #<?= (int) $reservation['id'] ?>
                                    </span>
                                </td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($reservation['event_title']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars($reservation['attendee_name']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($reservation['attendee_email']) ?>
                                </td>

                                <td>
                                    <span class="reservations-quantity">
                                        <?= (int) $reservation['quantity'] ?>
                                    </span>
                                </td>

                                <td>
                                    <?= htmlspecialchars($reservation['reservation_date']) ?>
                                </td>

                                <td>

                                    <?php if ($reservation['status'] === 'CONFIRMED'): ?>

                                        <span class="reservations-status reservations-confirmed">
                                            CONFIRMED
                                        </span>

                                    <?php elseif ($reservation['status'] === 'CANCELLED'): ?>

                                        <span class="reservations-status reservations-cancelled">
                                            CANCELLED
                                        </span>

                                    <?php else: ?>

                                        <span class="reservations-status reservations-other">
                                            <?= htmlspecialchars($reservation['status']) ?>
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>
                                    <div class="reservations-actions">

                                        <a
                                            href="update-reservation.php?id=<?= (int) $reservation['id'] ?>"
                                            class="reservations-edit-button"
                                        >
                                            Edit
                                        </a>

                                        <a
                                            href="delete-reservation.php?id=<?= (int) $reservation['id'] ?>"
                                            class="reservations-delete-button"
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
        <?php if ($error === null && $totalReservations > 0): ?>

            <div class="reservations-pagination-footer">

                <div class="reservations-pagination-info">
                    Showing
                    <strong><?= $offset + 1 ?></strong>
                    to
                    <strong><?= min($offset + $perPage, $totalReservations) ?></strong>
                    of
                    <strong><?= $totalReservations ?></strong>
                    reservations
                </div>

                <?php if ($totalPages > 1): ?>

                    <div class="reservations-pagination">

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

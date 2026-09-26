<?php

require_once __DIR__ . '/../classes/Reservation.php';

$reservationModel = new Reservation();

try {
    $reservations = $reservationModel->getAllReservations();
} catch (Exception $e) {
    $error = $e->getMessage();
    $reservations = [];
}

$message = $_GET['message'] ?? '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/style.css">

    <title>Reservations</title>

</head>

    <body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">

    <div class="page-header">

        <div>
            <h1>Reservation Management</h1>

            <p class="page-description">
                Manage event reservations, attendees, quantities, and reservation status.
            </p>
        </div>

    </div>

    <?php if ($message !== ''): ?>

        <div class="success">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>

    <?php if (isset($error)): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <div class="dashboard-card">

        <h3>Total Reservations</h3>

        <p class="dashboard-number">
            <?= count($reservations) ?>
        </p>

    </div>

    <br>

    <?php if (empty($reservations)): ?>

        <p>No reservations found.</p>

    <?php else: ?>

        <div class="table-container">

            <table>

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Event</th>
                        <th>Attendee</th>
                        <th>Email</th>
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
                                <?= (int) $reservation['id'] ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($reservation['event_title']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($reservation['attendee_name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($reservation['attendee_email']) ?>
                            </td>

                            <td>
                                <?= (int) $reservation['quantity'] ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($reservation['reservation_date']) ?>
                            </td>

                            <td>

                                <?php if ($reservation['status'] === 'CONFIRMED'): ?>

                                    <span class="status-badge status-open">
                                        CONFIRMED
                                    </span>

                                <?php elseif ($reservation['status'] === 'CANCELLED'): ?>

                                    <span class="status-badge status-cancelled">
                                        CANCELLED
                                    </span>

                                <?php else: ?>

                                    <span class="status-badge status-closed">
                                        <?= htmlspecialchars($reservation['status']) ?>
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <a
                                    href="update-reservation.php?id=<?= (int) $reservation['id'] ?>"
                                    class="btn btn-secondary"
                                >
                                    Edit
                                </a>

                                <a
                                    href="delete-reservation.php?id=<?= (int) $reservation['id'] ?>"
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
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
    <title>Reservations</title>
</head>

<body>

    <h1>Reservations</h1>

    <?php if ($message !== ''): ?>
        <p>
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <p>
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <p>
        Total Reservations:
        <strong><?= count($reservations) ?></strong>
    </p>

    <?php if (empty($reservations)): ?>

        <p>No reservations found.</p>

    <?php else: ?>

        <table border="1" cellpadding="8" cellspacing="0">

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
                            <?= htmlspecialchars($reservation['status']) ?>
                        </td>

                        <td>
                            <a href="update-reservation.php?id=<?= (int) $reservation['id'] ?>">
                                Edit
                            </a>

                            |

                            <a href="delete-reservation.php?id=<?= (int) $reservation['id'] ?>">
                                Delete
                            </a>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

</body>
</html>
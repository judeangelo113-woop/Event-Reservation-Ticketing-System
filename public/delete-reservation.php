<?php

require_once __DIR__ . '/../classes/Reservation.php';

$reservationModel = new Reservation();

$reservationId = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

if ($reservationId <= 0) {
    die("Invalid reservation.");
}

$reservation = $reservationModel->getReservationById($reservationId);

if ($reservation === null) {
    die("Reservation not found.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $reservationModel->setId($reservationId);

        $reservationModel->delete();

        header(
            'Location: reservations.php?message=' .
            urlencode('Reservation deleted successfully')
        );

        exit;

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

    <title>Delete Reservation</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container">

    <div class="form-header">

        <h1>Delete Reservation</h1>

        <p>
            Review the reservation details before deleting it.
        </p>

    </div>

    <a
        href="reservations.php"
        class="btn btn-secondary back-button"
    >
        ← Back to Reservation Management
    </a>

    <?php if ($error !== ''): ?>

        <div class="error">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>

    <div class="form-container">

        <h2>Confirm Deletion</h2>

        <p>
            Are you sure you want to delete this reservation?
        </p>

        <div class="delete-details">

            <p>
                <strong>Event:</strong>
                <?= htmlspecialchars($reservation['event_title']) ?>
            </p>

            <p>
                <strong>Attendee:</strong>
                <?= htmlspecialchars($reservation['attendee_name']) ?>
            </p>

            <p>
                <strong>Quantity:</strong>
                <?= (int) $reservation['quantity'] ?>
            </p>

            <p>
                <strong>Status:</strong>
                <?= htmlspecialchars($reservation['status']) ?>
            </p>

        </div>

        <form method="POST">

            <input
                type="hidden"
                name="id"
                value="<?= (int) $reservationId ?>"
            >

            <button
                type="submit"
                class="btn btn-danger"
            >
                Yes, Delete Reservation
            </button>

            <a
                href="reservations.php"
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
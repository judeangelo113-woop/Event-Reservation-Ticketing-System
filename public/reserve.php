
<?php

require_once __DIR__ . '/../includes/require_login.php';
require_once __DIR__ . '/../classes/Event.php';
require_once __DIR__ . '/../classes/Reservation.php';
require_once __DIR__ . '/../config/Database.php';

// Only attendees can reserve tickets.
if (!isAttendee()) {
    header('Location: dashboard.php');
    exit;
}

$eventModel = new Event();

$eventId = (int) ($_GET['event_id'] ?? 0);

if ($eventId <= 0) {
    die('Invalid event.');
}

$event = $eventModel->getEventById($eventId);

if ($event === null) {
    die('Event not found.');
}

$error = '';
$attendee = null;

try {

    $database = new Database();
    $pdo = $database->connect();

    $stmt = $pdo->prepare(
        "SELECT first_name, last_name, email, phone
         FROM attendees
         WHERE id = :attendee_id
         LIMIT 1"
    );

    $stmt->execute([
        ':attendee_id' => $_SESSION['attendee_id']
    ]);

    $attendee = $stmt->fetch();

    if (!$attendee) {
        die('Attendee account not found.');
    }

} catch (PDOException $e) {

    die('Unable to load attendee information.');

}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {

        $error = 'Invalid request. Please refresh the page and try again.';

    } else {

        $quantity = (int) ($_POST['quantity'] ?? 0);

        if ((int) $event['available_slots'] <= 0) {

            $error = 'Sorry, this event is already sold out.';

        } elseif ($quantity <= 0) {

            $error = 'Please enter a valid ticket quantity.';

        } elseif ($quantity > (int) $event['available_slots']) {

            $error = 'Not enough available slots. Please reduce your ticket quantity.';

        } else {

            try {

                $reservation = new Reservation(
                    $eventId,
                    (int) $_SESSION['attendee_id'],
                    $quantity,
                    '',
                    'PENDING'
                );

                $reservationId = $reservation->createReservationTransaction(
                    $attendee['first_name'],
                    $attendee['last_name'],
                    $attendee['email'],
                    $attendee['phone'] ?? '',
                    (int) $_SESSION['attendee_id']
                );

                header('Location: reservation-success.php?id=' . $reservationId);
                exit;

            } catch (Exception $e) {

                $error = $e->getMessage();

            }
        }
    }
}

$availableSlots = (int) $event['available_slots'];
$isSoldOut = $availableSlots <= 0;

$eventDate = !empty($event['event_date'])
    ? strtotime($event['event_date'])
    : false;

$eventTime = !empty($event['event_time'])
    ? strtotime($event['event_time'])
    : false;

$postedQuantity = (int) ($_POST['quantity'] ?? 1);

if ($postedQuantity < 1) {
    $postedQuantity = 1;
}

if (!$isSoldOut && $postedQuantity > $availableSlots) {
    $postedQuantity = $availableSlots;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Make Reservation - EventlyPH</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="reservation-page-container">

    <!-- Page Header -->
    <div class="reservation-page-header">

        <div>
            <span class="reservation-page-label">EVENT REGISTRATION</span>

            <h1>Make a Reservation</h1>

            <p>
                Complete your reservation details below to secure
                your slots for this event.
            </p>
        </div>

        <div class="reservation-header-icon">
            🎟
        </div>

    </div>

    <!-- Main Reservation Layout -->
    <div class="reservation-layout">

        <!-- Event Summary -->
        <div class="reservation-event-card">

            <div class="reservation-event-banner">
                <span>EVENT DETAILS</span>

                <div class="reservation-event-banner-icon">
                    🎉
                </div>
            </div>

            <div class="reservation-event-content">

                <h2>
                    <?= htmlspecialchars($event['title']) ?>
                </h2>

                <p class="reservation-event-description">
                    <?= htmlspecialchars($event['description']) ?>
                </p>

                <div class="reservation-event-information">

                    <div class="reservation-info-item">
                        <span class="reservation-info-icon">📅</span>

                        <div>
                            <small>Event Date</small>
                            <strong>
                                <?= $eventDate !== false
                                    ? date('F d, Y', $eventDate)
                                    : 'Not specified' ?>
                            </strong>
                        </div>
                    </div>

                    <div class="reservation-info-item">
                        <span class="reservation-info-icon">🕒</span>

                        <div>
                            <small>Event Time</small>
                            <strong>
                                <?= $eventTime !== false
                                    ? date('h:i A', $eventTime)
                                    : 'Not specified' ?>
                            </strong>
                        </div>
                    </div>

                    <div class="reservation-info-item">
                        <span class="reservation-info-icon">📍</span>

                        <div>
                            <small>Venue</small>
                            <strong>
                                <?= htmlspecialchars($event['venue']) ?>
                            </strong>
                        </div>
                    </div>

                </div>

                <div class="reservation-availability">

                    <div>
                        <span class="availability-indicator <?= $isSoldOut ? 'unavailable' : '' ?>"></span>

                        <span>
                            <?= $isSoldOut ? 'Sold Out' : 'Available Slots' ?>
                        </span>
                    </div>

                    <strong><?= $availableSlots ?></strong>

                </div>

            </div>

        </div>

        <!-- Reservation Form -->
        <div class="reservation-form-card">

            <div class="reservation-form-heading">

                <span class="reservation-form-step">YOUR RESERVATION</span>

                <h2>Reservation Information</h2>

                <p>
                    Please review your account information and select
                    the number of slots you want to reserve.
                </p>

            </div>

            <?php if ($error !== ''): ?>

                <div class="reservation-error">
                    <span>!</span>
                    <p><?= htmlspecialchars($error) ?></p>
                </div>

            <?php endif; ?>

            <!-- Attendee Information -->
            <div class="reservation-attendee-section">

                <h3>Attendee Details</h3>

                <div class="reservation-attendee-grid">

                    <div class="reservation-input-group">
                        <label>First Name</label>
                        <div class="reservation-readonly-field">
                            <?= htmlspecialchars($attendee['first_name']) ?>
                        </div>
                    </div>

                    <div class="reservation-input-group">
                        <label>Last Name</label>
                        <div class="reservation-readonly-field">
                            <?= htmlspecialchars($attendee['last_name']) ?>
                        </div>
                    </div>

                    <div class="reservation-input-group">
                        <label>Email Address</label>
                        <div class="reservation-readonly-field">
                            <?= htmlspecialchars($attendee['email']) ?>
                        </div>
                    </div>

                    <div class="reservation-input-group">
                        <label>Phone Number</label>
                        <div class="reservation-readonly-field">
                            <?= htmlspecialchars($attendee['phone'] ?? 'Not provided') ?>
                        </div>
                    </div>

                </div>

                <a href="edit-profile.php" class="reservation-edit-profile">
                    Edit Profile Information →
                </a>

            </div>

            <div class="reservation-divider"></div>

            <!-- Ticket Quantity and Confirmation -->
            <form method="POST" action="" id="reservationForm">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars(generateCSRFToken()) ?>"
                >

                <div class="reservation-quantity-section">

                    <label for="quantity">
                        Number of Slots
                    </label>

                    <p>
                        Choose how many slots you want to reserve.
                    </p>

                    <input
                        type="number"
                        id="quantity"
                        name="quantity"
                        min="1"
                        max="<?= $availableSlots ?>"
                        value="<?= $postedQuantity ?>"
                        <?= $isSoldOut ? 'disabled' : 'required' ?>
                    >

                    <small>
                        Maximum available slots: <?= $availableSlots ?>
                    </small>

                </div>

                <div class="reservation-form-actions">

                    <a href="browse-events.php" class="reservation-back-button">
                        ← Back to Events
                    </a>

                    <button
                        type="submit"
                        class="reservation-submit-button"
                        id="confirmReservationButton"
                        <?= $isSoldOut ? 'disabled' : '' ?>
                    >
                        <?= $isSoldOut ? 'Event Sold Out' : 'Confirm Reservation →' ?>
                    </button>

                </div>

                <!-- Confirmation Modal -->
                <div
                    class="reservation-confirm-overlay"
                    id="reservationConfirmModal"
                    aria-hidden="true"
                >

                    <div
                        class="reservation-confirm-modal"
                        role="dialog"
                        aria-modal="true"
                        aria-labelledby="reservationConfirmTitle"
                    >

                        <div class="reservation-confirm-icon">
                            🎟
                        </div>

                        <h2 id="reservationConfirmTitle">
                            Confirm Your Reservation
                        </h2>

                        <p class="reservation-confirm-description">
                            Please review your reservation details before proceeding.
                        </p>

                        <div class="reservation-confirm-details">

                            <div>
                                <span>Event</span>
                                <strong>
                                    <?= htmlspecialchars($event['title']) ?>
                                </strong>
                            </div>

                            <div>
                                <span>Attendee</span>
                                <strong>
                                    <?= htmlspecialchars($attendee['first_name'] . ' ' . $attendee['last_name']) ?>
                                </strong>
                            </div>

                            <div>
                                <span>Number of Slots</span>
                                <strong id="modalQuantity">
                                    <?= $postedQuantity ?>
                                </strong>
                            </div>

                            <div>
                                <span>Reservation Status</span>
                                <strong class="reservation-pending-label">
                                    PENDING
                                </strong>
                            </div>

                        </div>

                        <p class="reservation-confirm-notice">
                            Are you sure you want to proceed with this reservation?
                        </p>

                        <div class="reservation-confirm-actions">

                            <button
                                type="button"
                                class="reservation-cancel-confirm"
                                id="cancelReservationButton"
                            >
                                Go Back
                            </button>

                            <button
                                type="button"
                                class="reservation-proceed-confirm"
                                id="proceedReservationButton"
                            >
                                Yes, Proceed
                            </button>

                        </div>

                    </div>

                </div>

            </form>

            <div class="reservation-note">
                <span>ℹ</span>
                <p>
                    Please double-check your reservation quantity before
                    submitting. Your reservation will be processed as pending.
                </p>
            </div>

        </div>

    </div>

</div>

<script src="../assets/js/navigation.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('reservationForm');
    const quantityInput = document.getElementById('quantity');

    const modal = document.getElementById('reservationConfirmModal');
    const modalQuantity = document.getElementById('modalQuantity');

    const cancelButton = document.getElementById('cancelReservationButton');
    const proceedButton = document.getElementById('proceedReservationButton');

    let reservationConfirmed = false;

    function openConfirmation() {

        modalQuantity.textContent = quantityInput.value;

        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');

        proceedButton.focus();
    }

    function closeConfirmation() {

        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');

        document.getElementById('confirmReservationButton').focus();
    }

    // Intercept form submission and show confirmation first.
    form.addEventListener('submit', function (event) {

        if (!reservationConfirmed) {

            event.preventDefault();

            if (!form.reportValidity()) {
                return;
            }

            openConfirmation();
        }

    });

    // Update the quantity displayed in the confirmation dialog.
    quantityInput.addEventListener('input', function () {
        modalQuantity.textContent = quantityInput.value;
    });

    // Cancel the reservation confirmation.
    cancelButton.addEventListener('click', function () {
        closeConfirmation();
    });

    // Proceed with the actual reservation submission.
    proceedButton.addEventListener('click', function () {

        reservationConfirmed = true;

        form.requestSubmit();

    });

    // Close the modal when clicking outside its content.
    modal.addEventListener('click', function (event) {

        if (event.target === modal) {
            closeConfirmation();
        }

    });

    // Allow the Escape key to close the dialog.
    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape' && modal.classList.contains('active')) {
            closeConfirmation();
        }

    });

});
</script>

</body>
</html>

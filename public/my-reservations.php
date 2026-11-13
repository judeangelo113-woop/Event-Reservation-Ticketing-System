
<?php

require_once __DIR__ . '/../includes/require_login.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Reservation.php';

// Only attendees can access this page.
if (!isAttendee() || empty($_SESSION['attendee_id'])) {
    header('Location: dashboard.php');
    exit;
}

$attendeeId = (int) $_SESSION['attendee_id'];
$reservationId = (int) ($_GET['id'] ?? 0);

$reservations = [];
$error = '';

// Generate cancellation CSRF token.
if (empty($_SESSION['reservation_cancel_csrf'])) {
    $_SESSION['reservation_cancel_csrf'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['reservation_cancel_csrf'];

// Handle reservation cancellation.
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    ($_POST['action'] ?? '') === 'cancel_reservation'
) {

    $postedToken = $_POST['csrf_token'] ?? '';
    $cancelId = (int) ($_POST['reservation_id'] ?? 0);

    if (
        !is_string($postedToken) ||
        empty($postedToken) ||
        !hash_equals($_SESSION['reservation_cancel_csrf'], $postedToken)
    ) {

        $_SESSION['reservation_cancel_message'] = [
            'type' => 'error',
            'text' => 'Invalid request. Please try again.'
        ];

    } elseif ($cancelId <= 0) {

        $_SESSION['reservation_cancel_message'] = [
            'type' => 'error',
            'text' => 'Invalid reservation.'
        ];

    } else {

        try {

            // Cancel through the Reservation model.
            $reservationModel = new Reservation();

            $reservationModel->cancelReservationInDatabase(
                $cancelId,
                $attendeeId
            );

            $_SESSION['reservation_cancel_message'] = [
                'type' => 'success',
                'text' => 'Your reservation has been successfully cancelled. The event slots have been released.'
            ];

        } catch (Throwable $e) {

            error_log(
                'Reservation cancellation error: ' . $e->getMessage()
            );

            $_SESSION['reservation_cancel_message'] = [
                'type' => 'error',
                'text' => 'Unable to cancel your reservation. It may already be cancelled or no longer eligible.'
            ];
        }
    }

    // Rotate CSRF token after processing.
    $_SESSION['reservation_cancel_csrf'] = bin2hex(random_bytes(32));

    // Prevent form resubmission.
    $redirectUrl = 'my-reservations.php';

    if ($reservationId > 0) {
        $redirectUrl .= '?id=' . $reservationId;
    }

    header('Location: ' . $redirectUrl);
    exit;
}

// Retrieve cancellation feedback.
$cancelMessage = $_SESSION['reservation_cancel_message'] ?? null;
unset($_SESSION['reservation_cancel_message']);

// Retrieve attendee reservations.
try {

    $database = new Database();
    $db = $database->connect();

    $sql = "
        SELECT
            r.id,
            r.reservation_date,
            r.quantity,
            r.status,
            e.title AS event_title,
            e.event_date,
            e.event_time,
            e.venue
        FROM reservations r
        INNER JOIN events e
            ON r.event_id = e.id
        WHERE r.attendee_id = :attendee_id
    ";

    $params = [
        ':attendee_id' => $attendeeId
    ];

    if ($reservationId > 0) {
        $sql .= " AND r.id = :reservation_id";
        $params[':reservation_id'] = $reservationId;
    }

    $sql .= " ORDER BY r.id DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {

    error_log('My reservations error: ' . $e->getMessage());

    $error = 'Unable to load your reservations. Please try again later.';
}

$reservationCount = count($reservations);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Reservations - EventlyPH</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="my-reservations-container">

    <!-- Page Header -->
    <div class="my-reservations-header">

        <div>

            <span class="my-reservations-label">
                YOUR EVENT ACTIVITY
            </span>

            <h1>My Reservations</h1>

            <p>
                Keep track of your event reservations and check
                their current status.
            </p>

        </div>

        <div class="my-reservations-header-icon">
            🎟
        </div>

    </div>

    <!-- Cancellation Feedback -->
    <?php if ($cancelMessage): ?>

        <div class="reservation-feedback reservation-feedback-<?= htmlspecialchars($cancelMessage['type'], ENT_QUOTES, 'UTF-8') ?>">

            <?= htmlspecialchars($cancelMessage['text'], ENT_QUOTES, 'UTF-8') ?>

        </div>

    <?php endif; ?>

    <!-- Loading Error -->
    <?php if ($error): ?>

        <div class="reservation-feedback reservation-feedback-error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>

    <?php else: ?>

        <!-- Reservation Summary -->
        <?php if (!empty($reservations)): ?>

            <div class="my-reservations-summary">

                <div class="my-reservations-summary-icon">
                    📋
                </div>

                <div>

                    <strong><?= $reservationCount ?></strong>

                    <span>
                        <?= $reservationCount === 1
                            ? 'Reservation Found'
                            : 'Reservations Found' ?>
                    </span>

                </div>

            </div>

        <?php endif; ?>

        <!-- Reservation List -->
        <?php if (empty($reservations)): ?>

            <div class="my-reservations-empty">

                <div class="my-reservations-empty-icon">
                    🎫
                </div>

                <h2>No Reservations Found</h2>

                <p>

                    <?php if ($reservationId > 0): ?>

                        We couldn't find that reservation in your account.

                    <?php else: ?>

                        You haven't made any event reservations yet.
                        Browse available events and reserve your slots.

                    <?php endif; ?>

                </p>

                <a
                    href="browse-events.php"
                    class="my-reservations-browse-button">

                    Browse Events
                    <span>→</span>

                </a>

            </div>

        <?php else: ?>

            <div class="my-reservations-list">

                <?php foreach ($reservations as $reservation): ?>

                    <?php

                    $status = strtoupper(
                        trim($reservation['status'] ?? 'PENDING')
                    );

                    $statusClass = match ($status) {

                        'CONFIRMED' => 'reservation-status-confirmed',

                        'CANCELLED' => 'reservation-status-cancelled',

                        'PENDING' => 'reservation-status-pending',

                        default => 'reservation-status-default'

                    };

                    $canCancel = in_array(
                        $status,
                        ['PENDING', 'CONFIRMED'],
                        true
                    );

                    $eventDate = !empty($reservation['event_date'])
                        ? strtotime($reservation['event_date'])
                        : false;

                    $eventTime = !empty($reservation['event_time'])
                        ? strtotime($reservation['event_time'])
                        : false;

                    $reservationDate = !empty($reservation['reservation_date'])
                        ? strtotime($reservation['reservation_date'])
                        : false;

                    $eventTitle = $reservation['event_title'] ?? 'Event';

                    ?>

                    <div class="my-reservation-card">

                        <!-- Card Header -->
                        <div class="my-reservation-card-header">

                            <div class="my-reservation-event-icon">
                                🎉
                            </div>

                            <div class="my-reservation-title-area">

                                <span class="my-reservation-reference">

                                    RESERVATION #<?= (int) $reservation['id'] ?>

                                </span>

                                <h2>

                                    <?= htmlspecialchars(
                                        $eventTitle,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </h2>

                            </div>

                            <span class="reservation-status <?= $statusClass ?>">

                                <?= htmlspecialchars(
                                    $status,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </span>

                        </div>

                        <!-- Card Details -->
                        <div class="my-reservation-details">

                            <div class="my-reservation-detail">

                                <span class="my-reservation-detail-icon">
                                    📅
                                </span>

                                <div>

                                    <small>Event Date</small>

                                    <strong>

                                        <?= $eventDate !== false
                                            ? date('F d, Y', $eventDate)
                                            : 'Not specified' ?>

                                    </strong>

                                </div>

                            </div>

                            <div class="my-reservation-detail">

                                <span class="my-reservation-detail-icon">
                                    🕒
                                </span>

                                <div>

                                    <small>Event Time</small>

                                    <strong>

                                        <?= $eventTime !== false
                                            ? date('h:i A', $eventTime)
                                            : 'Not specified' ?>

                                    </strong>

                                </div>

                            </div>

                            <div class="my-reservation-detail">

                                <span class="my-reservation-detail-icon">
                                    📍
                                </span>

                                <div>

                                    <small>Venue</small>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $reservation['venue'] ?? 'Not specified',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </strong>

                                </div>

                            </div>

                            <div class="my-reservation-detail">

                                <span class="my-reservation-detail-icon">
                                    🎟
                                </span>

                                <div>

                                    <small>Reserved Slots</small>

                                    <strong>

                                        <?= (int) $reservation['quantity'] ?>

                                        <?= (int) $reservation['quantity'] === 1
                                            ? 'Slot'
                                            : 'Slots' ?>

                                    </strong>

                                </div>

                            </div>

                        </div>

                        <!-- Card Footer -->
                        <div class="my-reservation-card-footer">

                            <div class="my-reservation-date">

                                <small>Reservation Submitted</small>

                                <strong>

                                    <?= $reservationDate !== false
                                        ? date('M d, Y - h:i A', $reservationDate)
                                        : 'Not available' ?>

                                </strong>

                            </div>

                            <div class="my-reservation-actions">

                                <a
                                    href="my-tickets.php?reservation_id=<?= (int) $reservation['id'] ?>"
                                    class="my-reservation-ticket-button">

                                    View Tickets
                                    <span>→</span>

                                </a>

                                <?php if ($canCancel): ?>

                                    <form
                                        method="POST"
                                        action="my-reservations.php<?= $reservationId > 0
                                            ? '?id=' . $reservationId
                                            : '' ?>"
                                        class="cancel-reservation-form">

                                        <input
                                            type="hidden"
                                            name="action"
                                            value="cancel_reservation">

                                        <input
                                            type="hidden"
                                            name="reservation_id"
                                            value="<?= (int) $reservation['id'] ?>">

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= htmlspecialchars(
                                                $csrfToken,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>">

                                        <button
                                            type="button"
                                            class="my-reservation-cancel-button"
                                            onclick="openCancelModal(
                                                this,
                                                <?= (int) $reservation['id'] ?>,
                                                <?= htmlspecialchars(
                                                    json_encode(
                                                        $eventTitle,
                                                        JSON_HEX_APOS |
                                                        JSON_HEX_QUOT |
                                                        JSON_HEX_TAG |
                                                        JSON_HEX_AMP
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            )">

                                            Cancel Reservation

                                        </button>

                                    </form>

                                <?php else: ?>

                                    <?php if ($status === 'CANCELLED'): ?>

                                        <span class="reservation-already-cancelled">
                                            Reservation Cancelled
                                        </span>

                                    <?php endif; ?>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    <?php endif; ?>

</div>


<!-- Cancellation Confirmation Modal -->
<div class="cancel-modal-overlay" id="cancelModal">

    <div class="cancel-modal">

        <button
            type="button"
            class="cancel-modal-close"
            onclick="closeCancelModal()">

            &times;

        </button>

        <div class="cancel-modal-icon">
            !
        </div>

        <span class="cancel-modal-label">
            RESERVATION CANCELLATION
        </span>

        <h2>Cancel Reservation?</h2>

        <p class="cancel-modal-description">

            Are you sure you want to cancel this reservation?
            Please review the details before proceeding.

        </p>

        <div class="cancel-modal-details">

            <div>

                <small>Reservation Number</small>

                <strong id="cancelReservationId">
                    #000
                </strong>

            </div>

            <div>

                <small>Event</small>

                <strong id="cancelEventTitle">
                    Event Name
                </strong>

            </div>

        </div>

        <div class="cancel-modal-warning">

            <span>⚠</span>

            <p>

                This action cannot be undone. Your reservation and
                associated tickets will be marked as cancelled.

            </p>

        </div>

        <div class="cancel-modal-actions">

            <button
                type="button"
                class="cancel-modal-keep-button"
                onclick="closeCancelModal()">

                Keep Reservation

            </button>

            <button
                type="button"
                class="cancel-modal-confirm-button"
                id="confirmCancelButton"
                onclick="confirmCancellation()">

                Yes, Cancel Reservation

            </button>

        </div>

    </div>

</div>


<script>

    let selectedCancelForm = null;

    function openCancelModal(button, reservationId, eventTitle) {

        selectedCancelForm = button.closest('form');

        document.getElementById('cancelReservationId').textContent =
            '#' + reservationId;

        document.getElementById('cancelEventTitle').textContent =
            eventTitle;

        document.getElementById('cancelModal').classList.add('active');

        document.body.classList.add('cancel-modal-open');
    }

    function closeCancelModal() {

        document.getElementById('cancelModal').classList.remove('active');

        document.body.classList.remove('cancel-modal-open');

        selectedCancelForm = null;
    }

    function confirmCancellation() {

        if (selectedCancelForm) {

            selectedCancelForm.submit();

        }

    }

    // Close when clicking the overlay.
    document.getElementById('cancelModal').addEventListener(
        'click',
        function(event) {

            if (event.target === this) {
                closeCancelModal();
            }

        }
    );

    // Close when pressing Escape.
    document.addEventListener('keydown', function(event) {

        if (event.key === 'Escape') {
            closeCancelModal();
        }

    });

</script>

<script src="../assets/js/navigation.js"></script>

</body>
</html>

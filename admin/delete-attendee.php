
<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/require_admin.php';
require_once __DIR__ . '/../classes/Attendee.php';

$errors = [];

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id <= 0) {
    die('Invalid attendee ID.');
}

try {

    $attendeeModel = new Attendee();

    $attendee = $attendeeModel->getAttendeeById($id);

    if ($attendee === null) {
        die('Attendee not found.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $postedId = filter_input(
            INPUT_POST,
            'id',
            FILTER_VALIDATE_INT
        );

        // VERIFY ATTENDEE ID

        if (
            $postedId === false ||
            $postedId === null ||
            $postedId !== $id
        ) {

            $errors[] = 'Invalid attendee ID.';

        // VERIFY CSRF TOKEN

        } elseif (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {

            $errors[] =
                'Invalid or expired request. Please refresh the page and try again.';

        } else {

            $attendeeModel->setId($id);

            if ($attendeeModel->delete()) {

                header('Location: ../public/attendees.php');
                exit;

            } else {

                $errors[] =
                    'Failed to delete the attendee. They may have existing records in the system.';
            }
        }
    }

} catch (Exception $e) {

    $errors[] =
        'An error occurred while deleting the attendee.';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Delete Attendee | EventlyPH</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container attendee-delete-page">

    <div class="attendee-delete-header">
        <span class="attendee-delete-label">ATTENDEE MANAGEMENT</span>
        <h1>Delete Attendee</h1>
        <p>Review the attendee information before confirming deletion.</p>
    </div>

    <a href="../public/attendees.php" class="attendee-delete-back">
        &larr; Back to Attendees
    </a>

    <?php if (!empty($errors)): ?>

        <div class="attendee-delete-error">
            <strong>Unable to delete attendee:</strong>

            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

    <?php endif; ?>

    <div class="attendee-delete-card">

        <div class="attendee-delete-icon">!</div>

        <h2>Confirm Attendee Deletion</h2>

        <p class="attendee-delete-description">
            You are about to delete this attendee from the system.
            Please review the details below.
        </p>

        <div class="attendee-delete-details">

            <div class="attendee-delete-detail">
                <span>Attendee ID</span>
                <strong>#<?= (int) $id ?></strong>
            </div>

            <div class="attendee-delete-detail">
                <span>Full Name</span>
                <strong>
                    <?= htmlspecialchars(
                        $attendee['first_name'] . ' ' .
                        $attendee['last_name']
                    ) ?>
                </strong>
            </div>

            <div class="attendee-delete-detail">
                <span>Email Address</span>
                <strong><?= htmlspecialchars($attendee['email']) ?></strong>
            </div>

            <div class="attendee-delete-detail">
                <span>Phone Number</span>
                <strong>
                    <?= htmlspecialchars($attendee['phone'] ?? 'Not provided') ?>
                </strong>
            </div>

        </div>

        <div class="attendee-delete-warning">
            <strong>Warning:</strong>
            This action cannot be undone. Existing reservations or tickets
            associated with this attendee may also prevent deletion.
        </div>

        <form method="POST" action="">

            <input
                type="hidden"
                name="id"
                value="<?= (int) $id ?>"
            >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(generateCSRFToken()) ?>"
            >

            <div class="attendee-delete-actions">

                <a href="../public/attendees.php"
                class="attendee-delete-cancel">
                    Cancel
                </a>

                <button type="submit" class="attendee-delete-confirm">
                    Yes, Delete Attendee
                </button>

            </div>

        </form>

    </div>

</main>

<script src="../assets/js/navigation.js"></script>

</body>
</html>

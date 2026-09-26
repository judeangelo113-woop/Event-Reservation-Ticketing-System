<?php

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

        if (
            $postedId === false ||
            $postedId === null ||
            $postedId !== $id
        ) {

            $errors[] = 'Invalid attendee ID.';

        } else {

            $attendeeModel->setId($id);

            if ($attendeeModel->delete()) {

                header('Location: ../public/attendees.php');
                exit;

            } else {

                $errors[] = 'Failed to delete the attendee.';
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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Delete Attendee</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container">

    <div class="form-header">

        <h1>Delete Attendee</h1>

        <p>
            Confirm that you want to delete this attendee.
        </p>

    </div>

    <a
        href="../public/attendees.php"
        class="btn btn-secondary back-button"
    >
        ← Back to Attendee Management
    </a>

    <?php if (!empty($errors)): ?>

        <div class="error">

            <strong>Please fix the following errors:</strong>

            <ul>

                <?php foreach ($errors as $error): ?>

                    <li>
                        <?= htmlspecialchars($error) ?>
                    </li>

                <?php endforeach; ?>

            </ul>

        </div>

    <?php endif; ?>

    <div class="form-container">

        <h3>Attendee Information</h3>

        <div class="delete-details">

            <p>
                <strong>Name:</strong>
                <?= htmlspecialchars(
                    $attendee['first_name'] . ' ' .
                    $attendee['last_name']
                ) ?>
            </p>

            <p>
                <strong>Email:</strong>
                <?= htmlspecialchars($attendee['email']) ?>
            </p>

            <p>
                <strong>Phone:</strong>
                <?= htmlspecialchars($attendee['phone'] ?? '') ?>
            </p>

        </div>

        <p>
            Are you sure you want to delete this attendee?
            This action cannot be undone.
        </p>

        <form method="POST" action="">

            <input
                type="hidden"
                name="id"
                value="<?= (int) $id ?>"
            >

            <button
                type="submit"
                class="btn btn-danger"
            >
                Yes, Delete Attendee
            </button>

            <a
                href="../public/attendees.php"
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
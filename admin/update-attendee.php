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

    $firstName = $attendee['first_name'];
    $lastName = $attendee['last_name'];
    $email = $attendee['email'];
    $phone = $attendee['phone'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($firstName === '') {
            $errors[] = 'First name is required.';
        }

        if ($lastName === '') {
            $errors[] = 'Last name is required.';
        }

        if ($email === '') {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (empty($errors)) {

            $existingAttendee = $attendeeModel->getAttendeeByEmail($email);

            if (
                $existingAttendee !== null &&
                (int) $existingAttendee['id'] !== $id
            ) {
                $errors[] =
                    'Another attendee is already using this email address.';
            }
        }

        if (empty($errors)) {

            $attendeeModel->setId($id);
            $attendeeModel->setFirstName($firstName);
            $attendeeModel->setLastName($lastName);
            $attendeeModel->setEmail($email);
            $attendeeModel->setPhone($phone);

            if ($attendeeModel->update()) {

                header('Location: ../public/attendees.php');
                exit;

            } else {

                $errors[] = 'Failed to update the attendee.';
            }
        }
    }

} catch (Exception $e) {

    $errors[] = 'An error occurred while updating the attendee.';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Attendee</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container">

    <div class="form-header">

        <h1>Edit Attendee</h1>

        <p>
            Update the attendee's information.
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

        <form method="POST" action="">

            <div class="form-group">

                <label for="first_name">
                    First Name
                </label>

                <input
                    type="text"
                    id="first_name"
                    name="first_name"
                    value="<?= htmlspecialchars($firstName) ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label for="last_name">
                    Last Name
                </label>

                <input
                    type="text"
                    id="last_name"
                    name="last_name"
                    value="<?= htmlspecialchars($lastName) ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($email) ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label for="phone">
                    Phone
                </label>

                <input
                    type="text"
                    id="phone"
                    name="phone"
                    value="<?= htmlspecialchars($phone) ?>"
                >

            </div>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Update Attendee
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
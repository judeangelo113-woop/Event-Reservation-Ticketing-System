
<?php

require_once __DIR__ . '/../classes/Attendee.php';
require_once __DIR__ . '/../includes/require_admin.php';

$errors = [];

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id <= 0) {
    die('Invalid attendee ID.');
}

$firstName = '';
$lastName = '';
$email = '';
$phone = '';

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

        // SERVER-SIDE VALIDATION

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

        // CHECK FOR DUPLICATE EMAIL

        if (empty($errors)) {

            $existingAttendee =
                $attendeeModel->getAttendeeByEmail($email);

            if (
                $existingAttendee !== null &&
                (int) $existingAttendee['id'] !== $id
            ) {
                $errors[] =
                    'Another attendee is already using this email address.';
            }
        }

        // UPDATE ATTENDEE

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

    <title>Edit Attendee | EventlyPH</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container attendee-form-page">

    <div class="attendee-form-header">
        <div>
            <span class="attendee-form-label">ATTENDEE MANAGEMENT</span>
            <h1>Edit Attendee</h1>
            <p>Update the attendee's personal and contact information.</p>
        </div>
    </div>

    <a href="../public/attendees.php"
       class="attendee-form-back">
        <span>&larr;</span> Back to Attendees
    </a>

    <?php if (!empty($errors)): ?>

        <div class="attendee-form-error">
            <strong>Please fix the following errors:</strong>

            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

    <?php endif; ?>

    <div class="attendee-form-card">

        <div class="attendee-form-card-header">

            <div class="attendee-form-icon">&#9998;</div>

            <div>
                <h2>Attendee Information</h2>
                <p>Modify the information in the fields below.</p>
            </div>

        </div>

        <form method="POST" action="">

            <div class="attendee-form-grid">

                <div class="attendee-form-group">
                    <label for="first_name">First Name <span>*</span></label>

                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        placeholder="Enter first name"
                        value="<?= htmlspecialchars($firstName) ?>"
                        required
                    >
                </div>

                <div class="attendee-form-group">
                    <label for="last_name">Last Name <span>*</span></label>

                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        placeholder="Enter last name"
                        value="<?= htmlspecialchars($lastName) ?>"
                        required
                    >
                </div>

                <div class="attendee-form-group">
                    <label for="email">Email Address <span>*</span></label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="example@email.com"
                        value="<?= htmlspecialchars($email) ?>"
                        required
                    >
                </div>

                <div class="attendee-form-group">
                    <label for="phone">Phone Number</label>

                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        placeholder="Enter phone number"
                        value="<?= htmlspecialchars($phone) ?>"
                    >
                </div>

            </div>

            <p class="attendee-form-note">
                Fields marked with <span>*</span> are required.
            </p>

            <div class="attendee-form-actions">

                <a href="../public/attendees.php"
                   class="attendee-form-cancel">
                    Cancel
                </a>

                <button type="submit" class="attendee-form-submit">
                    Update Attendee
                </button>

            </div>

        </form>

    </div>

</main>

<script src="../assets/js/navigation.js"></script>

</body>
</html>

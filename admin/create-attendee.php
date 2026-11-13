
<?php

require_once __DIR__ . '/../classes/Attendee.php';
require_once __DIR__ . '/../includes/require_admin.php';

$errors = [];

$firstName = '';
$lastName = '';
$email = '';
$phone = '';

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

    // CREATE ATTENDEE

    if (empty($errors)) {

        try {

            $attendeeModel = new Attendee(
                $firstName,
                $lastName,
                $email,
                $phone
            );

            if (!$attendeeModel->isEmailValid()) {

                $errors[] = 'Please enter a valid email address.';

            } else {

                $existingAttendee =
                    $attendeeModel->getAttendeeByEmail($email);

                if ($existingAttendee !== null) {

                    $errors[] =
                        'An attendee with this email already exists.';

                } else {

                    if ($attendeeModel->create()) {
                        header('Location: ../public/attendees.php');
                        exit;
                    } else {
                        $errors[] = 'Failed to create the attendee.';
                    }
                }
            }

        } catch (Exception $e) {
            $errors[] =
                'An error occurred while creating the attendee.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Attendee | EventlyPH</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container">

    <div class="attendee-form-page">

        <div class="attendee-form-heading">
            <div>
                <span class="attendee-form-eyebrow">ATTENDEE MANAGEMENT</span>
                <h1>Add New Attendee</h1>
                <p>Add a new attendee to your EventlyPH records.</p>
            </div>

            <a href="../public/attendees.php"
               class="attendee-form-back">
                &larr; Back to Attendees
            </a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="attendee-form-errors">
                <strong>Please fix the following errors:</strong>

                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <section class="attendee-form-card">

            <div class="attendee-form-card-heading">
                <div class="attendee-form-icon">+</div>

                <div>
                    <h2>Attendee Information</h2>
                    <p>Enter the attendee's personal details below.</p>
                </div>
            </div>

            <form method="POST" action="">

                <div class="attendee-form-grid">

                    <div class="attendee-form-group">
                        <label for="first_name">First Name</label>

                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            value="<?= htmlspecialchars($firstName) ?>"
                            placeholder="Enter first name"
                            required
                        >
                    </div>

                    <div class="attendee-form-group">
                        <label for="last_name">Last Name</label>

                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            value="<?= htmlspecialchars($lastName) ?>"
                            placeholder="Enter last name"
                            required
                        >
                    </div>

                    <div class="attendee-form-group">
                        <label for="email">Email Address</label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars($email) ?>"
                            placeholder="example@email.com"
                            required
                        >
                    </div>

                    <div class="attendee-form-group">
                        <label for="phone">Phone Number</label>

                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="<?= htmlspecialchars($phone) ?>"
                            placeholder="Enter phone number"
                        >
                    </div>

                </div>

                <div class="attendee-form-note">
                    Fields marked with * are required.
                </div>

                <div class="attendee-form-actions">
                    <a href="../public/attendees.php"
                       class="attendee-form-cancel">
                        Cancel
                    </a>

                    <button type="submit"
                            class="attendee-form-submit">
                        Add Attendee
                    </button>
                </div>

            </form>

        </section>

    </div>

</main>

<script src="../assets/js/navigation.js"></script>

</body>
</html>

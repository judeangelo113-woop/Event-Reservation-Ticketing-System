
<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/Database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid security token. Please refresh the page and try again.';
    } else {

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate required fields
        if (
            $firstName === '' ||
            $lastName === '' ||
            $email === '' ||
            $username === '' ||
            $password === '' ||
            $confirmPassword === ''
        ) {
            $error = 'Please fill in all required fields.';

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';

        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $error = 'Username must be between 3 and 50 characters.';

        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';

        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';

        } else {

            try {

                $database = new Database();
                $pdo = $database->connect();

                // Check if email already exists
                $checkEmail = $pdo->prepare(
                    "SELECT id FROM attendees
                    WHERE email = :email
                    LIMIT 1"
                );

                $checkEmail->execute(['email' => $email]);

                // Check if username already exists
                $checkUsername = $pdo->prepare(
                    "SELECT id FROM users
                    WHERE username = :username
                    LIMIT 1"
                );

                $checkUsername->execute(['username' => $username]);

                if ($checkEmail->fetch()) {

                    $error = 'This email address is already registered.';

                } elseif ($checkUsername->fetch()) {

                    $error = 'This username is already taken.';

                } else {

                    // Securely hash the password
                    $hashedPassword = password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                    // Save attendee and user account together
                    $pdo->beginTransaction();

                    // Insert attendee information
                    $attendeeStmt = $pdo->prepare(
                        "INSERT INTO attendees
                        (first_name, last_name, email, phone)
                        VALUES
                        (:first_name, :last_name, :email, :phone)"
                    );

                    $attendeeStmt->execute([
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $email,
                        'phone' => $phone !== '' ? $phone : null
                    ]);

                    $attendeeId = $pdo->lastInsertId();

                    // Insert login account
                    $userStmt = $pdo->prepare(
                        "INSERT INTO users
                        (username, password, role, attendee_id)
                        VALUES
                        (:username, :password, 'attendee', :attendee_id)"
                    );

                    $userStmt->execute([
                        'username' => $username,
                        'password' => $hashedPassword,
                        'attendee_id' => $attendeeId
                    ]);

                    $pdo->commit();

                    $success = 'Registration successful! You can now log in.';
                }

            } catch (PDOException $e) {

                if (isset($pdo) && $pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register | EventlyPH</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="register-page">

    <div class="register-wrapper">

        <div class="register-card">

            <!-- Brand -->
            <a href="index.php" class="register-brand">
                Evently<span>PH</span>
            </a>

            <!-- Heading -->
            <div class="register-heading">
                <h1>Create Your Account</h1>
                <p>Join EventlyPH and start exploring events.</p>
            </div>

            <!-- Error Message -->
            <?php if ($error !== ''): ?>
                <p class="error">
                    <?= htmlspecialchars($error) ?>
                </p>
            <?php endif; ?>

            <!-- Registration Success -->
            <?php if ($success !== ''): ?>

                <p class="success">
                    <?= htmlspecialchars($success) ?>
                </p>

                <div class="register-success-link">
                    <a href="login.php" class="register-submit">
                        Proceed to Login
                    </a>
                </div>

            <?php else: ?>

                <!-- Registration Form -->
                <form method="POST" action="register.php">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(generateCSRFToken()) ?>"
                    >

                    <div class="register-form-grid">

                        <div class="register-field">
                            <label for="first_name">First Name *</label>

                            <input
                                type="text"
                                id="first_name"
                                name="first_name"
                                maxlength="100"
                                placeholder="Enter first name"
                                value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>"
                                required
                            >
                        </div>

                        <div class="register-field">
                            <label for="last_name">Last Name *</label>

                            <input
                                type="text"
                                id="last_name"
                                name="last_name"
                                maxlength="100"
                                placeholder="Enter last name"
                                value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>"
                                required
                            >
                        </div>

                        <div class="register-field register-full">
                            <label for="email">Email Address *</label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                maxlength="150"
                                placeholder="example@email.com"
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                required
                            >
                        </div>

                        <div class="register-field register-full">
                            <label for="phone">Phone Number</label>

                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                maxlength="30"
                                placeholder="Enter phone number"
                                value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                            >
                        </div>

                        <div class="register-field register-full">
                            <label for="username">Username *</label>

                            <input
                                type="text"
                                id="username"
                                name="username"
                                minlength="3"
                                maxlength="50"
                                placeholder="Choose a username"
                                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                required
                            >
                        </div>

                        <div class="register-field">
                            <label for="password">Password *</label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                minlength="8"
                                placeholder="Minimum 8 characters"
                                required
                            >
                        </div>

                        <div class="register-field">
                            <label for="confirm_password">Confirm Password *</label>

                            <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                minlength="8"
                                placeholder="Re-enter password"
                                required
                            >
                        </div>

                    </div>

                    <button type="submit" class="register-submit">
                        Create Account
                    </button>

                </form>

                <div class="register-login-link">
                    Already have an account?
                    <a href="login.php">Login here</a>
                </div>

            <?php endif; ?>

            <!-- Back to Home -->
            <div class="register-back-link">
                <a href="index.php">← Back to Home</a>
            </div>

        </div>

    </div>

</body>
</html>

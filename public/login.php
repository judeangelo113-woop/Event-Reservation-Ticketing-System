
<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/Database.php';

if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: dashboard.php');
    } elseif (isAttendee()) {
        header('Location: attendee-dashboard.php');
    } else {
        header('Location: logout.php');
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid request. Please try again.';
    } else {

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = 'Please enter your username and password.';
        } else {
            try {
                $database = new Database();
                $pdo = $database->connect();

                $stmt = $pdo->prepare(
                    "SELECT id, username, password, role, attendee_id
                    FROM users
                    WHERE username = :username
                    LIMIT 1"
                );

                $stmt->execute(['username' => $username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {

                    session_regenerate_id(true);

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['attendee_id'] = $user['attendee_id'];

                    if ($user['role'] === 'admin') {
                        header('Location: dashboard.php');
                    } elseif ($user['role'] === 'attendee') {
                        header('Location: attendee-dashboard.php');
                    } else {
                        header('Location: logout.php');
                    }

                    exit;

                } else {
                    $error = 'Incorrect username or password.';
                }

            } catch (PDOException $e) {
                $error = 'Unable to log in. Please try again later.';
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
    <title>Login - EventlyPH</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="login-page">

    <div class="login-wrapper">

        <div class="login-card">

            <div class="login-brand">
                <h1>EventlyPH</h1>
                <p>Event Reservation and Ticketing System</p>
            </div>

            <div class="login-heading">
                <h2>Welcome Back!</h2>
                <p>Sign in to continue to your account.</p>
            </div>

            <?php if ($error !== ''): ?>
                <div class="login-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="login-form">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars(generateCSRFToken()) ?>"
                >

                <div class="login-field">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Enter your username"
                        autocomplete="username"
                        required
                    >
                </div>

                <div class="login-field">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="login-submit">
                    Login to Your Account
                </button>

            </form>

            <div class="login-register-link">
                Don't have an account?
                <a href="register.php">Create an account</a>
            </div>

            <div class="login-back-link">
                <a href="index.php">← Back to Home</a>
            </div>

        </div>

    </div>

</body>
</html>


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
                } else {
                    header('Location: attendee-dashboard.php');
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
    <title>Login - Event Ticketing System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="login-container">

    <h2>Event Ticketing System</h2>
    <h3>Login</h3>

    <?php if ($error !== ''): ?>
        <p class="login-error">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="login.php">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars(generateCSRFToken()) ?>"
        >

        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>

    </form>

    <p class="register-link">
        Don't have an account?
        <a href="register.php">Register here</a>
    </p>

</div>

</body>
</html>

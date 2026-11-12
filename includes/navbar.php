
<?php
require_once __DIR__ . '/auth.php';
?>

<nav class="navbar">

    <div class="container navbar-container">

        <a href="../public/dashboard.php" class="navbar-brand">
            Event Ticketing
        </a>

        <button
            class="menu-toggle"
            id="menuToggle"
            type="button"
            aria-label="Toggle navigation"
            aria-expanded="false"
        >
            ☰
        </button>

                <div class="nav-links" id="navLinks">

            <?php if (isAdmin()): ?>

                <a href="../public/dashboard.php">Dashboard</a>
                <a href="../public/events.php">Events</a>
                <a href="../public/attendees.php">Attendees</a>
                <a href="../public/reservations.php">Reservations</a>
                <a href="../public/tickets.php">Tickets</a>
                <a href="http://localhost:8000/events" target="_blank">
                    Laravel Events Module
                </a>

            <?php elseif (isAttendee()): ?>

                <a href="../public/attendee-dashboard.php">Dashboard</a>
                <a href="../public/browse-events.php">Browse Events</a>
                <a href="../public/my-reservations.php">My Reservations</a>
                <a href="../public/my-tickets.php">My Tickets</a>


            <?php endif; ?>

            <form action="../public/logout.php" method="POST" style="display: inline;">
                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars(generateCSRFToken()) ?>">

                <button type="submit">
                    Logout (<?= htmlspecialchars($_SESSION['username']) ?>)
                </button>
            </form>

        </div>

    </div>

</nav>

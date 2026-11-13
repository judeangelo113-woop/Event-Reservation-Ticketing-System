
<?php
require_once __DIR__ . '/auth.php';
?>

<aside class="app-sidebar" id="appSidebar">

    <div class="sidebar-brand">
        <a href="<?= isAdmin() ? '../public/dashboard.php' : '../public/attendee-dashboard.php' ?>">
            <span class="brand-icon">E</span>
            <span>
                <strong>EventlyPH</strong>
                <small>Event Reservation System</small>
            </span>
        </a>
    </div>

    <div class="sidebar-section-label">MAIN MENU</div>

    <nav class="sidebar-links">

        <?php if (isAdmin()): ?>

            <a href="../public/dashboard.php" class="sidebar-link">
                <span>▦</span> Dashboard
            </a>

            <a href="../public/events.php" class="sidebar-link">
                <span>◫</span> Events
            </a>

            <a href="../public/attendees.php" class="sidebar-link">
                <span>♙</span> Attendees
            </a>

            <a href="../public/reservations.php" class="sidebar-link">
                <span>▤</span> Reservations
            </a>

            <a href="../public/tickets.php" class="sidebar-link">
                <span>▣</span> Tickets
            </a>

            <a href="../public/reports.php" class="sidebar-link">
                <span>▥</span> Reports
            </a>

            <a href="http://localhost:8000/events" class="sidebar-link">
                <span>◉</span> Announcements
            </a>

        <?php elseif (isAttendee()): ?>

            <a href="../public/attendee-dashboard.php" class="sidebar-link">
                <span>▦</span> Dashboard
            </a>

            <a href="../public/browse-events.php" class="sidebar-link">
                <span>◫</span> Browse Events
            </a>

            <a href="../public/my-reservations.php" class="sidebar-link">
                <span>▤</span> My Reservations
            </a>

            <a href="../public/my-tickets.php" class="sidebar-link">
                <span>▣</span> My Tickets
            </a>

            <a href="../public/my-account.php" class="sidebar-link">
                <span>♙</span> My Account
            </a>

        <?php endif; ?>

    </nav>

    <div class="sidebar-bottom">

        <?php if (isAttendee()): ?>

            <a href="../public/my-account.php" class="sidebar-user sidebar-user-link">
                <div class="user-avatar">
                    <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
                </div>

                <div class="user-details">
                    <strong>
                        <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
                    </strong>
                    <small>Attendee</small>
                </div>
            </a>

        <?php else: ?>

            <div class="sidebar-user">
                <div class="user-avatar">
                    <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
                </div>

                <div class="user-details">
                    <strong>
                        <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
                    </strong>
                    <small>Administrator</small>
                </div>
            </div>

        <?php endif; ?>

        <form action="../public/logout.php" method="POST">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(generateCSRFToken()) ?>"
            >

            <button type="submit" class="sidebar-logout">
                <span>↪</span> Logout
            </button>
        </form>

    </div>

</aside>

<button
    type="button"
    class="sidebar-mobile-toggle"
    id="menuToggle"
    aria-label="Toggle navigation"
    aria-expanded="false"
>
    ☰
</button>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<?php
require_once __DIR__ . '/../includes/auth.php';

// Redirect users who are already logged in.
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>EventlyPH | Discover Events</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="landing-page">

    <!-- Navigation -->
    <header class="landing-navbar">
        <div class="landing-nav-container">

            <a href="index.php" class="landing-brand">
                Evently<span>PH</span>
            </a>

            <nav class="landing-nav-links">
                <a href="#home">Home</a>
                <a href="#features">Features</a>
                <a href="#about">About</a>
            </nav>

            <div class="landing-nav-actions">
                <a href="login.php" class="landing-login">Login</a>
                <a href="register.php" class="landing-register">Get Started</a>
            </div>

        </div>
    </header>

    <!-- Hero Section -->
    <main>

        <section class="landing-hero" id="home">

            <div class="landing-hero-content">

                <span class="landing-tag">
                    YOUR EVENTS, MADE EASIER
                </span>

                <h1>
                    Discover Events.<br>
                    Reserve Your <span>Spot.</span>
                </h1>

                <p>
                    Find exciting events, make reservations, and keep
                    your tickets organized with EventlyPH.
                    Your next experience starts here.
                </p>



                <div class="landing-trust-text">
                    Simple reservations. Convenient ticket access.
                </div>

            </div>

            <div class="landing-hero-visual">

                <div class="landing-ticket-card">

                    <div class="landing-ticket-icon">
                        <span>✦</span>
                    </div>

                    <p class="ticket-label">YOUR NEXT EXPERIENCE</p>

                    <h2>Make Every Event Memorable.</h2>

                    <div class="ticket-divider"></div>

                    <div class="ticket-details">
                        <div>
                            <small>RESERVATION</small>
                            <strong>Easy and Convenient</strong>
                        </div>


                    </div>

                    <div class="ticket-decoration">
                        EVENTLYPH
                    </div>

                </div>

            </div>

        </section>

        <!-- Features Section -->
        <section class="landing-features" id="features">

            <div class="landing-section-heading">
                <span>WHAT WE OFFER</span>
                <h2>Everything You Need for Events</h2>
                <p>
                    Enjoy a convenient way to discover events and manage
                    your reservations.
                </p>
            </div>

            <div class="landing-feature-grid">

                <div class="landing-feature-card">
                    <div class="feature-icon">📅</div>
                    <h3>Discover Events</h3>
                    <p>
                        Explore available events and find activities
                        that interest you.
                    </p>
                </div>

                <div class="landing-feature-card">
                    <div class="feature-icon">🎟️</div>
                    <h3>Easy Reservations</h3>
                    <p>
                        Reserve your slots through a simple and
                        convenient reservation process.
                    </p>
                </div>

                <div class="landing-feature-card">
                    <div class="feature-icon">📋</div>
                    <h3>Reservation Tracking</h3>
                    <p>
                        Monitor your reservations and easily check their
                        current status through your attendee account.
                    </p>
                </div>

            </div>

        </section>

        <!-- About Section -->
        <section class="landing-about" id="about">

            <div>
                <span>ABOUT EVENTLYPH</span>
                <h2>Making Event Reservations Simple.</h2>
            </div>

            <p>
                EventlyPH is an event reservation and ticketing platform
                designed to make event participation more convenient.
                It helps users reserve event slots and access their
                tickets while providing administrators with tools to
                manage events and reservations.
            </p>

        </section>

        <!-- Call to Action -->
        <section class="landing-cta">

            <h2>Ready for Your Next Event?</h2>

            <p>
                Create your account and start exploring EventlyPH.
            </p>

            <a href="register.php" class="landing-primary-btn">
                Create an Account
            </a>

        </section>

    </main>

    <!-- Footer -->
    <footer class="landing-footer">
        <p>
            &copy; <?= date('Y') ?> EventlyPH.
            All rights reserved.
        </p>
    </footer>

</body>
</html>

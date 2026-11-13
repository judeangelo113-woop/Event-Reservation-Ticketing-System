
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'EventlyPH')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body>

    <!-- Navigation Bar -->
    <nav class="evently-navbar">

        <div class="evently-nav-container">

            <!-- Brand -->
            <a href="{{ route('events.index') }}" class="evently-brand">

                <div class="evently-brand-icon">
                    E
                </div>

                <div class="evently-brand-text">
                    <span class="evently-brand-name">EventlyPH</span>
                    <span class="evently-brand-subtitle">
                        EVENT MANAGEMENT
                    </span>
                </div>

            </a>

            <!-- Desktop Navigation -->
            <div class="evently-nav-links">

                <a href="{{ route('events.index') }}"
                class="evently-nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}">
                    Events
                </a>

                <a href="{{ route('announcements.index') }}"
                class="evently-nav-link {{ request()->routeIs('announcements.*') ? 'active' : '' }}">
                    Announcements
                </a>

            </div>

            <!-- Main Dashboard -->
            <div class="evently-nav-right">

                <a href="http://localhost/EventTicketing/public/dashboard.php"
                class="evently-dashboard-link">
                    <span class="evently-dashboard-icon">↗</span>
                    Main Dashboard
                </a>

            </div>

            <!-- Mobile Menu -->
            <details class="evently-mobile-menu">

                <summary>
                    <span class="evently-menu-icon">☰</span>
                    Menu
                </summary>

                <div class="evently-mobile-links">

                    <a href="{{ route('events.index') }}"
                    class="{{ request()->routeIs('events.*') ? 'active' : '' }}">
                        Events
                    </a>

                    <a href="{{ route('announcements.index') }}"
                    class="{{ request()->routeIs('announcements.*') ? 'active' : '' }}">
                        Announcements
                    </a>

                    <a href="http://localhost/EventTicketing/public/dashboard.php">
                        Main Dashboard
                    </a>

                </div>

            </details>

        </div>

    </nav>

    <!-- Main Content -->
    <main class="evently-main-container">

        @yield('content')

    </main>

</body>

</html>

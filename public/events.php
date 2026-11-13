<?php

require_once __DIR__ . '/../includes/require_admin.php';
require_once __DIR__ . '/../classes/Event.php';

$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');
$sort = trim($_GET['sort'] ?? 'date_asc');

$perPage = 5;
$currentPage = max(1, (int) ($_GET['page'] ?? 1));

$events = [];
$totalEvents = 0;
$totalPages = 1;
$offset = 0;
$error = null;

try {

    $eventModel = new Event();

    // Count filtered events
    $totalEvents = $eventModel->countAllEvents($search, $status);

    // Calculate pagination
    $totalPages = max(1, (int) ceil($totalEvents / $perPage));
    $currentPage = min($currentPage, $totalPages);
    $offset = ($currentPage - 1) * $perPage;

    // Retrieve events
    $events = $eventModel->getAllEvents(
        $search,
        $status,
        $sort,
        $perPage,
        $offset
    );

} catch (Exception $e) {

    $error = $e->getMessage();

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/style.css">

    <title>Event Management | EventlyPH</title>
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container events-page">

    <!-- PAGE HEADER -->
    <section class="events-heading">

        <div>
            <span class="events-eyebrow">EVENT MANAGEMENT</span>

            <h1>Events</h1>

            <p>
                Manage your events, schedules, venues, and available slots.
            </p>
        </div>

        <a href="create-event.php" class="events-add-button">
            <span>+</span> Add New Event
        </a>

    </section>

    <!-- EVENT SUMMARY -->
    <?php if ($error === null): ?>

        <section class="events-summary">

            <div class="events-summary-icon">◫</div>

            <div>
                <span>Total Events</span>
                <strong><?= number_format($totalEvents) ?></strong>
            </div>

        </section>

    <?php endif; ?>

    <!-- SEARCH AND FILTER -->
    <section class="events-filter-panel">

        <div class="events-filter-heading">
            <div>
                <h2>Event List</h2>
                <p>Search and organize your events.</p>
            </div>
        </div>

        <form method="GET" action="events.php" class="events-filter-form">

            <div class="events-search-field">
                <label for="eventSearch">Search Events</label>

                <input
                    type="text"
                    id="eventSearch"
                    name="search"
                    placeholder="Search title or venue..."
                    value="<?= htmlspecialchars($search) ?>"
                >
            </div>

            <div class="events-select-field">
                <label for="eventStatus">Status</label>

                <select id="eventStatus" name="status">

                    <option value="">All Statuses</option>

                    <option value="OPEN"
                        <?= $status === 'OPEN' ? 'selected' : '' ?>>
                        Open
                    </option>

                    <option value="CLOSED"
                        <?= $status === 'CLOSED' ? 'selected' : '' ?>>
                        Closed
                    </option>

                    <option value="CANCELLED"
                        <?= $status === 'CANCELLED' ? 'selected' : '' ?>>
                        Cancelled
                    </option>

                </select>
            </div>

            <div class="events-select-field">
                <label for="eventSort">Sort By</label>

                <select id="eventSort" name="sort">

                    <option value="date_asc"
                        <?= $sort === 'date_asc' ? 'selected' : '' ?>>
                        Earliest Date
                    </option>

                    <option value="date_desc"
                        <?= $sort === 'date_desc' ? 'selected' : '' ?>>
                        Latest Date
                    </option>

                    <option value="title_asc"
                        <?= $sort === 'title_asc' ? 'selected' : '' ?>>
                        Title: A to Z
                    </option>

                    <option value="title_desc"
                        <?= $sort === 'title_desc' ? 'selected' : '' ?>>
                        Title: Z to A
                    </option>

                    <option value="capacity_asc"
                        <?= $sort === 'capacity_asc' ? 'selected' : '' ?>>
                        Lowest Capacity
                    </option>

                    <option value="capacity_desc"
                        <?= $sort === 'capacity_desc' ? 'selected' : '' ?>>
                        Highest Capacity
                    </option>

                </select>
            </div>

            <div class="events-filter-actions">

                <button type="submit" class="events-search-button">
                    Search
                </button>

                <?php if (
                    $search !== '' ||
                    $status !== '' ||
                    $sort !== 'date_asc'
                ): ?>

                    <a href="events.php" class="events-clear-button">
                        Clear
                    </a>

                <?php endif; ?>

            </div>

        </form>

    </section>

    <!-- EVENTS TABLE -->
    <section class="events-table-panel">

        <?php if ($error !== null): ?>

            <div class="events-error">
                <strong>Unable to load events</strong>
                <p><?= htmlspecialchars($error) ?></p>
            </div>

        <?php elseif (empty($events)): ?>

            <div class="events-empty-state">

                <div class="events-empty-icon">◫</div>

                <h3>No Events Found</h3>

                <p>
                    There are no events matching your search or filter.
                </p>

                <?php if (
                    $search !== '' ||
                    $status !== '' ||
                    $sort !== 'date_asc'
                ): ?>

                    <a href="events.php">Clear Filters</a>

                <?php else: ?>

                    <a href="create-event.php">Create Your First Event</a>

                <?php endif; ?>

            </div>

        <?php else: ?>

            <div class="events-table-wrapper">

                <table class="events-modern-table">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Event Details</th>
                            <th>Date and Time</th>
                            <th>Venue</th>
                            <th>Capacity</th>
                            <th>Available</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($events as $event): ?>

                            <tr>

                                <td>
                                    <span class="events-id">
                                        #<?= (int) $event['id'] ?>
                                    </span>
                                </td>

                                <td>
                                    <strong class="events-title">
                                        <?= htmlspecialchars($event['title']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <span class="events-date">
                                        <?= htmlspecialchars($event['event_date']) ?>
                                    </span>

                                    <small class="events-time">
                                        <?= htmlspecialchars($event['event_time']) ?>
                                    </small>
                                </td>

                                <td>
                                    <?= htmlspecialchars($event['venue']) ?>
                                </td>

                                <td>
                                    <?= number_format((int) $event['capacity']) ?>
                                </td>

                                <td>
                                    <strong class="events-available">
                                        <?= number_format((int) $event['available_slots']) ?>
                                    </strong>
                                </td>

                                <td>

                                    <?php if ($event['status'] === 'OPEN'): ?>

                                        <span class="events-status status-open">
                                            OPEN
                                        </span>

                                    <?php elseif ($event['status'] === 'CLOSED'): ?>

                                        <span class="events-status status-closed">
                                            CLOSED
                                        </span>

                                    <?php elseif ($event['status'] === 'CANCELLED'): ?>

                                        <span class="events-status status-cancelled">
                                            CANCELLED
                                        </span>

                                    <?php else: ?>

                                        <span class="events-status status-unknown">
                                            <?= htmlspecialchars($event['status']) ?>
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>
                                    <div class="events-action-group">

                                        <a
                                            href="update-event.php?id=<?= (int) $event['id'] ?>"
                                            class="events-action-button events-edit-button"
                                        >
                                            Edit
                                        </a>

                                        <a
                                            href="delete-event.php?id=<?= (int) $event['id'] ?>"
                                            class="events-action-button events-delete-button"
                                        >
                                            Delete
                                        </a>

                                        <?php if (
                                            $event['status'] === 'OPEN' &&
                                            (int) $event['available_slots'] > 0
                                        ): ?>



                                        <?php endif; ?>

                                    </div>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <!-- PAGINATION INFORMATION -->
            <div class="events-pagination-footer">

                <div class="events-pagination-info">

                    Showing
                    <strong><?= $offset + 1 ?></strong>
                    to
                    <strong><?= min($offset + $perPage, $totalEvents) ?></strong>
                    of
                    <strong><?= number_format($totalEvents) ?></strong>
                    events

                </div>

                <!-- PAGINATION -->
                <?php if ($totalPages > 1): ?>

                    <div class="events-pagination">

                        <?php
                        $queryParams = [
                            'search' => $search,
                            'status' => $status,
                            'sort' => $sort
                        ];
                        ?>

                        <?php if ($currentPage > 1): ?>

                            <?php
                            $queryParams['page'] = $currentPage - 1;
                            ?>

                            <a href="?<?= htmlspecialchars(http_build_query($queryParams)) ?>">
                                Previous
                            </a>

                        <?php endif; ?>

                        <?php for ($page = 1; $page <= $totalPages; $page++): ?>

                            <?php
                            $queryParams['page'] = $page;
                            ?>

                            <a
                                href="?<?= htmlspecialchars(http_build_query($queryParams)) ?>"
                                class="<?= $page === $currentPage ? 'active' : '' ?>"
                            >
                                <?= $page ?>
                            </a>

                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>

                            <?php
                            $queryParams['page'] = $currentPage + 1;
                            ?>

                            <a href="?<?= htmlspecialchars(http_build_query($queryParams)) ?>">
                                Next
                            </a>

                        <?php endif; ?>

                    </div>

                <?php endif; ?>

            </div>

        <?php endif; ?>

    </section>

</main>

<script src="../assets/js/navigation.js"></script>

</body>
</html>

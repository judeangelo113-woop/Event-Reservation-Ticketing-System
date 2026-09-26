<?php

require_once __DIR__ . '/../classes/Event.php';


try {
    $eventModel = new Event();

    $search = trim($_GET['search'] ?? '');
    $status = trim($_GET['status'] ?? '');
    $sort = trim($_GET['sort'] ?? 'date_asc');

    $events = $eventModel->getAllEvents($search, $status, $sort);

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

    <title>Event Management</title>
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">

    <div class="page-header">

        <div>
            <h1>Event Management</h1>
            <p class="page-description">
                Manage your events, schedules, capacity, and reservations.
            </p>
        </div>

        <a href="create-event.php" class="btn btn-primary">
            + Add New Event
        </a>

    </div>

    <form method="GET" action="" class = "filter-form">

        <input
            type="text"
            name="search"
            placeholder="Search event title or venue..."
            value="<?= htmlspecialchars($search) ?>"
        >

        <select name="status">

            <option value="">All Statuses</option>

            <option
                value="OPEN"
                <?= $status === 'OPEN' ? 'selected' : '' ?>
            >
                OPEN
            </option>

            <option
                value="CLOSED"
                <?= $status === 'CLOSED' ? 'selected' : '' ?>
            >
                CLOSED
            </option>

            <option
                value="CANCELLED"
                <?= $status === 'CANCELLED' ? 'selected' : '' ?>
            >
                CANCELLED
            </option>

        </select>

        <select name="sort">

            <option
                value="date_asc"
                <?= $sort === 'date_asc' ? 'selected' : '' ?>
            >
                Date - Earliest First
            </option>

            <option
                value="date_desc"
                <?= $sort === 'date_desc' ? 'selected' : '' ?>
            >
                Date - Latest First
            </option>

            <option
                value="title_asc"
                <?= $sort === 'title_asc' ? 'selected' : '' ?>
            >
                Title - A to Z
            </option>

            <option
                value="title_desc"
                <?= $sort === 'title_desc' ? 'selected' : '' ?>
            >
                Title - Z to A
            </option>

            <option
                value="capacity_asc"
                <?= $sort === 'capacity_asc' ? 'selected' : '' ?>
            >
                Capacity - Lowest First
            </option>

            <option
                value="capacity_desc"
                <?= $sort === 'capacity_desc' ? 'selected' : '' ?>
            >
                Capacity - Highest First
            </option>

        </select>

        <button type="submit">
            Search / Filter
        </button>

        <?php if ($search !== '' || $status !== '' || $sort !== 'date_asc'): ?>

            <a href="events.php" class = "clear-filter">
                Clear
            </a>

        <?php endif; ?>

    </form>

    <br>


    <?php if (isset($error)): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php elseif (empty($events)): ?>

        <p>No events found.</p>

    <?php else: ?>

        <div class="table-container">

            <table>

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Venue</th>
                        <th>Capacity</th>
                        <th>Available Slots</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($events as $event): ?>

                        <tr>

                            <td>
                                <?= (int) $event['id'] ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($event['title']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($event['event_date']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($event['event_time']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($event['venue']) ?>
                            </td>

                            <td>
                                <?= (int) $event['capacity'] ?>
                            </td>

                            <td>
                                <?= (int) $event['available_slots'] ?>
                            </td>

                            <td>

                                <?php if ($event['status'] === 'OPEN'): ?>

                                    <span class="status-badge status-open">
                                        OPEN
                                    </span>

                                <?php elseif ($event['status'] === 'CLOSED'): ?>

                                    <span class="status-badge status-closed">
                                        CLOSED
                                    </span>

                                <?php elseif ($event['status'] === 'CANCELLED'): ?>

                                    <span class="status-badge status-cancelled">
                                        CANCELLED
                                    </span>

                                <?php endif; ?>

                            </td>

                           <td>
                                <a href="update-event.php?id=<?= (int) $event['id'] ?>" class="btn btn-secondary">Edit</a>

                                <a href="delete-event.php?id=<?= (int) $event['id'] ?>" class="btn btn-danger">Delete</a>

                                <?php if ($event['status'] === 'OPEN' && (int) $event['available_slots'] > 0): ?>
                                    <a href="reserve.php?event_id=<?= (int) $event['id'] ?>" class="btn btn-primary">Reserve</a>
                                <?php endif; ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</div>

<script src="../assets/js/navigation.js"></script>

</body>

</html>
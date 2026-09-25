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

    <title>Event Management</title>
</head>

<body>

    <h1>Event Management</h1>

<form method="GET" action="">

    <input
        type="text"
        name="search"
        placeholder="Search event title or venue..."
        value="<?php echo htmlspecialchars($search); ?>"
    >

    <select name="status">

        <option value="">
            All Statuses
        </option>

        <option
            value="OPEN"
            <?php echo $status === 'OPEN' ? 'selected' : ''; ?>
        >
            OPEN
        </option>

        <option
            value="CLOSED"
            <?php echo $status === 'CLOSED' ? 'selected' : ''; ?>
        >
            CLOSED
        </option>

        <option
            value="CANCELLED"
            <?php echo $status === 'CANCELLED' ? 'selected' : ''; ?>
        >
            CANCELLED
        </option>

    </select>

    <select name="sort">

        <option
            value="date_asc"
            <?php echo $sort === 'date_asc' ? 'selected' : ''; ?>
        >
            Date - Earliest First
        </option>

        <option
            value="date_desc"
            <?php echo $sort === 'date_desc' ? 'selected' : ''; ?>
        >
            Date - Latest First
        </option>

        <option
            value="title_asc"
            <?php echo $sort === 'title_asc' ? 'selected' : ''; ?>
        >
            Title - A to Z
        </option>

        <option
            value="title_desc"
            <?php echo $sort === 'title_desc' ? 'selected' : ''; ?>
        >
            Title - Z to A
        </option>

        <option
            value="capacity_asc"
            <?php echo $sort === 'capacity_asc' ? 'selected' : ''; ?>
        >
            Capacity - Lowest First
        </option>

        <option
            value="capacity_desc"
            <?php echo $sort === 'capacity_desc' ? 'selected' : ''; ?>
        >
            Capacity - Highest First
        </option>

    </select>

    <button type="submit">
        Search / Filter
    </button>

    <?php if ($search !== '' || $status !== '' || $sort !== 'date_asc'): ?>

        <a href="events.php">
            Clear
        </a>

    <?php endif; ?>

</form>

<br>

<a href="create-event.php">Add New Event</a>

    <br><br>

    <?php if (isset($error)): ?>

        <p>
            <?php echo htmlspecialchars($error); ?>
        </p>

    <?php elseif (empty($events)): ?>

        <p>No events found.</p>

    <?php else: ?>

        <table border="1" cellpadding="8">

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
                            <?php echo $event['id']; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($event['title']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($event['event_date']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($event['event_time']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($event['venue']); ?>
                        </td>

                        <td>
                            <?php echo $event['capacity']; ?>
                        </td>

                        <td>
                            <?php echo $event['available_slots']; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($event['status']); ?>
                        </td>

                        <td>

                            <?php if ($event['status'] === 'OPEN' && $event['available_slots'] > 0): ?>

                            <a href="reserve.php?event_id=<?php echo $event['id']; ?>">
                                Reserve
                            </a>

                                    <?php else: ?>

                                        Not Available

                                    <?php endif; ?>
                            |

                            <a href="update-event.php?id=<?php echo $event['id']; ?>">
                                Edit
                            </a>

                            |

                            <a href="delete-event.php?id=<?php echo $event['id']; ?>">
                                Delete
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

</body>
</html>
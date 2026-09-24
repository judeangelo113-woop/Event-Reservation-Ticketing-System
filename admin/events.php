<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../models/Event.php';

$event = new Event();

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'event_date';
$order = $_GET['order'] ?? 'ASC';

$events = $event->search($search, $sort, $order);

$message = $_GET['message'] ?? '';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Event Management</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f4f4f4;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        h1 {
            margin-bottom: 20px;
        }

        .top-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        input,
        select,
        button {
            padding: 10px;
            font-size: 14px;
        }

        button {
            cursor: pointer;
        }

        a {
            text-decoration: none;
        }

        .create-btn {
            display: inline-block;
            background: #198754;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #eee;
        }

        .edit {
            color: blue;
        }

        .delete {
            color: red;
        }

        .message {
            background: #d1e7dd;
            color: #0f5132;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Event Management</h1>

    <?php if ($message === 'created'): ?>
        <div class="message">
            Event created successfully.
        </div>
    <?php endif; ?>

    <?php if ($message === 'updated'): ?>
        <div class="message">
            Event updated successfully.
        </div>
    <?php endif; ?>

    <?php if ($message === 'deleted'): ?>
        <div class="message">
            Event deleted successfully.
        </div>
    <?php endif; ?>

    <a class="create-btn" href="create_event.php">
        + Create Event
    </a>

    <form method="GET" class="top-bar">

        <input
            type="text"
            name="search"
            placeholder="Search event..."
            value="<?= htmlspecialchars($search) ?>"
        >

        <select name="sort">

            <option value="event_date"
                <?= $sort === 'event_date' ? 'selected' : '' ?>>
                Date
            </option>

            <option value="title"
                <?= $sort === 'title' ? 'selected' : '' ?>>
                Title
            </option>

            <option value="event_time"
                <?= $sort === 'event_time' ? 'selected' : '' ?>>
                Time
            </option>

            <option value="venue"
                <?= $sort === 'venue' ? 'selected' : '' ?>>
                Venue
            </option>

            <option value="capacity"
                <?= $sort === 'capacity' ? 'selected' : '' ?>>
                Capacity
            </option>

            <option value="available_slots"
                <?= $sort === 'available_slots' ? 'selected' : '' ?>>
                Available Slots
            </option>

            <option value="status"
                <?= $sort === 'status' ? 'selected' : '' ?>>
                Status
            </option>

        </select>

        <select name="order">

            <option value="ASC"
                <?= $order === 'ASC' ? 'selected' : '' ?>>
                Ascending
            </option>

            <option value="DESC"
                <?= $order === 'DESC' ? 'selected' : '' ?>>
                Descending
            </option>

        </select>

        <button type="submit">
            Search / Sort
        </button>

    </form>

    <table>

        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Date</th>
                <th>Time</th>
                <th>Venue</th>
                <th>Capacity</th>
                <th>Available</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

        <?php if (empty($events)): ?>

            <tr>
                <td colspan="9">
                    No events found.
                </td>
            </tr>

        <?php else: ?>

            <?php foreach ($events as $eventData): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars($eventData['id']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($eventData['title']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($eventData['event_date']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($eventData['event_time']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($eventData['venue']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($eventData['capacity']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($eventData['available_slots']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($eventData['status']) ?>
                    </td>

                    <td>

                        <a
                            class="edit"
                            href="edit_event.php?id=<?= $eventData['id'] ?>"
                        >
                            Edit
                        </a>

                        |

                        <a
                            class="delete"
                            href="delete_event.php?id=<?= $eventData['id'] ?>"
                            onclick="return confirm('Are you sure you want to delete this event?');"
                        >
                            Delete
                        </a>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php endif; ?>

        </tbody>

    </table>

</div>

</body>
</html>
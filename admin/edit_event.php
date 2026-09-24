<?php

require_once __DIR__ . '/../models/Event.php';

$event = new Event();

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die('Invalid event ID.');
}

$currentEvent = $event->find($id);

if (!$currentEvent) {
    die('Event not found.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $eventDate = $_POST['event_date'] ?? '';
    $eventTime = $_POST['event_time'] ?? '';
    $venue = trim($_POST['venue'] ?? '');
    $capacity = (int)($_POST['capacity'] ?? 0);

    if (
        $title === '' ||
        $eventDate === '' ||
        $eventTime === '' ||
        $venue === ''
    ) {
        $error = 'Please fill in all required fields.';
    } elseif ($capacity <= 0) {
        $error = 'Capacity must be greater than 0.';
    } else {

        if (
            $event->update(
                $id,
                $title,
                $description,
                $eventDate,
                $eventTime,
                $venue,
                $capacity
            )
        ) {
            header('Location: events.php?message=updated');
            exit;
        }

        $error = 'Failed to update event.';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>Edit Event</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        input,
        textarea {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }

        textarea {
            height: 100px;
        }

        button {
            margin-top: 20px;
            padding: 10px 20px;
        }

        .error {
            background: #f8d7da;
            color: #842029;
            padding: 10px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Edit Event</h1>

    <?php if ($error !== ''): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <label>Event Title *</label>

        <input
            type="text"
            name="title"
            value="<?= htmlspecialchars($currentEvent['title']) ?>"
            required
        >

        <label>Description</label>

        <textarea name="description"><?= htmlspecialchars($currentEvent['description']) ?></textarea>

        <label>Event Date *</label>

        <input
            type="date"
            name="event_date"
            value="<?= htmlspecialchars($currentEvent['event_date']) ?>"
            required
        >

        <label>Event Time *</label>

        <input
            type="time"
            name="event_time"
            value="<?= htmlspecialchars($currentEvent['event_time']) ?>"
            required
        >

        <label>Venue *</label>

        <input
            type="text"
            name="venue"
            value="<?= htmlspecialchars($currentEvent['venue']) ?>"
            required
        >

        <label>Capacity *</label>

        <input
            type="number"
            name="capacity"
            min="1"
            value="<?= htmlspecialchars($currentEvent['capacity']) ?>"
            required
        >

        <button type="submit">
            Update Event
        </button>

    </form>

    <br>

    <a href="events.php">
        ← Back to Events
    </a>

</div>

</body>
</html>
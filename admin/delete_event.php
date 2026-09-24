<?php

require_once __DIR__ . '/../models/Event.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die('Invalid event ID.');
}

$event = new Event();

if ($event->delete($id)) {
    header('Location: events.php?message=deleted');
    exit;
}

die('Failed to delete event.');
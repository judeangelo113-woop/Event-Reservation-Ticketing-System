<?php

require_once __DIR__ . '/../classes/Event.php';
require_once __DIR__ . '/../classes/Attendee.php';
require_once __DIR__ . '/../classes/Reservation.php';
require_once __DIR__ . '/../classes/Ticket.php';

// Create Event object
$event = new Event(
    'IT Seminar',
    'A seminar for IT students.',
    '2026-10-15',
    '09:00:00',
    'Computer Laboratory',
    100,
    100,
    'OPEN'
);

// Create Attendee object
$attendee = new Attendee(
    'Jude',
    'Angelo',
    'jude@example.com',
    '09123456789'
);

// Create Reservation object
$reservation = new Reservation(
    1,
    1,
    2,
    date('Y-m-d'),
    'PENDING'
);

// Create Ticket object
$ticket = new Ticket(
    1,
    'TKT-2026-0001',
    date('Y-m-d H:i:s'),
    'VALID'
);

echo '<h1>OOP Classes Test</h1>';

echo '<h2>Event</h2>';
echo 'Title: ' . htmlspecialchars($event->getTitle()) . '<br>';
echo 'Venue: ' . htmlspecialchars($event->getVenue()) . '<br>';
echo 'Capacity: ' . $event->getCapacity() . '<br>';
echo 'Available Slots: ' . $event->getAvailableSlots() . '<br>';

echo '<h2>Attendee</h2>';
echo 'Name: ' . htmlspecialchars($attendee->getFullName()) . '<br>';
echo 'Email: ' . htmlspecialchars($attendee->getEmail()) . '<br>';
echo 'Valid Email: ' . ($attendee->isEmailValid() ? 'Yes' : 'No') . '<br>';

echo '<h2>Reservation</h2>';

if ($reservation->reserve(2)) {
    echo 'Reservation Status: ' . $reservation->getStatus() . '<br>';
    echo 'Quantity: ' . $reservation->getQuantity() . '<br>';
}

echo '<h2>Ticket</h2>';
echo 'Ticket Number: ' . htmlspecialchars($ticket->getTicketNumber()) . '<br>';
echo 'Status: ' . htmlspecialchars($ticket->getStatus()) . '<br>';
echo 'Valid: ' . ($ticket->isValid() ? 'Yes' : 'No') . '<br>';
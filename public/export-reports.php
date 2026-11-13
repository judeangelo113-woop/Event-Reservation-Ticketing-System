<?php

require_once __DIR__ . '/../includes/require_admin.php';
require_once __DIR__ . '/../config/Database.php';

$database = new Database();
$db = $database->connect();

try {

    $stmt = $db->query(
        "SELECT
            e.title,
            e.event_date,
            COUNT(DISTINCT r.id) AS total_reservations,
            COUNT(DISTINCT t.id) AS total_tickets
        FROM events e
        LEFT JOIN reservations r ON e.id = r.event_id
        LEFT JOIN tickets t ON r.id = t.reservation_id
        GROUP BY e.id, e.title, e.event_date
        ORDER BY e.event_date DESC"
    );

    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="event-reservation-report.csv"');

    $output = fopen('php://output', 'w');

    // CSV column headings
    fputcsv($output, [
        'Event',
        'Event Date',
        'Total Reservations',
        'Tickets Issued'
    ]);

    foreach ($reports as $report) {
        fputcsv($output, [
            $report['title'],
            $report['event_date'],
            $report['total_reservations'],
            $report['total_tickets']
        ]);
    }

    fclose($output);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    exit('Unable to export reports.');
}

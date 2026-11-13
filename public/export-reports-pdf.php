<?php

require_once __DIR__ . '/../includes/require_admin.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$database = new Database();
$db = $database->connect();

try {

    // Retrieve event reservation reports
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

    // Retrieve reservation status summary
    $stmt = $db->query(
        "SELECT
            status,
            COUNT(*) AS total,
            ROUND(
                COUNT(*) * 100.0 /
                NULLIF((SELECT COUNT(*) FROM reservations), 0),
                2
            ) AS percentage
        FROM reservations
        GROUP BY status"
    );

$reservationSummary = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate report totals
    $totalReservations = 0;
    $totalTickets = 0;

    foreach ($reports as $report) {
        $totalReservations += (int) $report['total_reservations'];
        $totalTickets += (int) $report['total_tickets'];
    }



        // Prepare HTML content
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body {
                    font-family: DejaVu Sans, sans-serif;
                    font-size: 12px;
                    color: #333;
                }

                h1 {
                    text-align: center;
                    margin-bottom: 5px;
                }

                .subtitle {
                    text-align: center;
                    color: #666;
                    margin-bottom: 25px;
                }

                .summary {
                    width: 100%;
                    margin-bottom: 25px;
                }

                .summary td {
                    border: 1px solid #ccc;
                    padding: 12px;
                    text-align: center;
                }

                table.report {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }

                table.report th,
                table.report td {
                    border: 1px solid #ccc;
                    padding: 9px;
                    text-align: left;
                }

                table.report th {
                    background-color: #eeeeee;
                }

                .footer {
                    margin-top: 30px;
                    text-align: center;
                    font-size: 10px;
                    color: #777;
                }
            </style>
        </head>
        <body>

            <h1>EventlyPH Report</h1>

            <p class="subtitle">
                Generated on: ' . date('F d, Y h:i A') . '
            </p>

            <table class="summary">
                <tr>
                    <td>
                        <strong>Total Reservations</strong><br>
                        ' . $totalReservations . '
                    </td>
                    <td>
                        <strong>Total Tickets Issued</strong><br>
                        ' . $totalTickets . '
                    </td>
                    <td>
                        <strong>Total Events</strong><br>
                        ' . count($reports) . '
                    </td>
                </tr>
            </table>

            <h3>Reservation Status Summary</h3>

            <table class="report">
                <thead>
                    <tr>
                        <th>Reservation Status</th>
                        <th>Total Reservations</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>';

        if (empty($reservationSummary)) {

            $html .= '
                <tr>
                    <td colspan="3">No reservation records found.</td>
                </tr>';

        } else {

            foreach ($reservationSummary as $summary) {

                $html .= '
                    <tr>
                        <td>' . htmlspecialchars($summary['status']) . '</td>
                        <td>' . (int) $summary['total'] . '</td>
                        <td>' . number_format((float) $summary['percentage'], 2) . '%</td>
                    </tr>';
            }
        }

        $html .= '
                </tbody>
            </table>

            <h3>Event Reservation Details</h3>

            <table class="report">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Event Date</th>
                        <th>Total Reservations</th>
                        <th>Tickets Issued</th>
                    </tr>
                </thead>
                <tbody>';

        if (empty($reports)) {

            $html .= '
                <tr>
                    <td colspan="4">No event records found.</td>
                </tr>';

        } else {

            foreach ($reports as $report) {

                $html .= '
                    <tr>
                        <td>' . htmlspecialchars($report['title']) . '</td>
                        <td>' . htmlspecialchars($report['event_date']) . '</td>
                        <td>' . (int) $report['total_reservations'] . '</td>
                        <td>' . (int) $report['total_tickets'] . '</td>
                    </tr>';
            }
        }

        $html .= '
                </tbody>
            </table>

            <div class="footer">
                End of Report – EventlyPH
            </div>

        </body>
        </html>';



    // Generate PDF
    $options = new Options();
    $options->set('isRemoteEnabled', false);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('letter', 'portrait');
    $dompdf->render();

    $dompdf->stream(
    'EventlyPH-Report.pdf',
    ['Attachment' => true]
    );

    exit;

} catch (Throwable $e) {

    error_log($e->getMessage());

    http_response_code(500);
    exit('Unable to generate PDF report.');

}

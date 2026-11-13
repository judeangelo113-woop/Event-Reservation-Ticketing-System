
<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/auth.php';

try {

    $database = new Database();
    $db = $database->connect();

    $method = $_SERVER['REQUEST_METHOD'];

    // Only admins can create, update, or delete events.
    if (
        in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE']) &&
        (!isLoggedIn() || !isAdmin())
    ) {
        http_response_code(403);

        echo json_encode([
            'success' => false,
            'message' => 'Access denied. Admin privileges are required.'
        ]);

        exit;
    }

    // Verify CSRF token for API write requests.
    if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {

        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if (!verifyCSRFToken($csrfToken)) {
            http_response_code(403);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid or missing CSRF token.'
            ]);

            exit;
        }
    }

    // GET: Retrieve all events
    if ($method === 'GET') {

        $sql = "SELECT * FROM events ORDER BY id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'message' => 'Events retrieved successfully.',
            'data' => $events
        ]);

    }

    // POST: Create a new event
    elseif ($method === 'POST') {

        $input = json_decode(file_get_contents('php://input'), true);

        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid JSON input.'
            ]);
            exit;
        }

        $title = trim($input['title'] ?? '');
        $description = trim($input['description'] ?? '');
        $eventDate = $input['event_date'] ?? '';
        $eventTime = $input['event_time'] ?? '';
        $venue = trim($input['venue'] ?? '');
        $capacity = filter_var(
            $input['capacity'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            $title === '' ||
            $eventDate === '' ||
            $eventTime === '' ||
            $venue === '' ||
            $capacity === false ||
            $capacity === null ||
            $capacity <= 0
        ) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Please provide valid event details.'
            ]);
            exit;
        }

        $sql = "INSERT INTO events
                (title, description, event_date, event_time,
                 venue, capacity, available_slots, status,
                 created_at, updated_at)
                VALUES
                (:title, :description, :event_date, :event_time,
                 :venue, :capacity, :available_slots, 'OPEN',
                 CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";

        $stmt = $db->prepare($sql);

        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':event_date' => $eventDate,
            ':event_time' => $eventTime,
            ':venue' => $venue,
            ':capacity' => $capacity,
            ':available_slots' => $capacity
        ]);

        $eventId = $db->lastInsertId();

        http_response_code(201);

        echo json_encode([
            'success' => true,
            'message' => 'Event created successfully.',
            'event_id' => $eventId
        ]);

    }


        // PUT: Update an existing event
        elseif ($method === 'PUT') {

            $input = json_decode(file_get_contents('php://input'), true);

            if (!is_array($input)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid JSON input.'
                ]);
                exit;
            }

            $id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);

            if (!$id || $id <= 0) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Valid event ID is required.'
                ]);
                exit;
            }

            $sql = "SELECT * FROM events WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => $id]);

            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Event not found.'
                ]);
                exit;
            }

            $title = trim($input['title'] ?? $event['title']);
            $description = trim($input['description'] ?? $event['description']);
            $eventDate = $input['event_date'] ?? $event['event_date'];
            $eventTime = $input['event_time'] ?? $event['event_time'];
            $venue = trim($input['venue'] ?? $event['venue']);
            $capacity = filter_var(
                $input['capacity'] ?? $event['capacity'],
                FILTER_VALIDATE_INT
            );

            if (
                $title === '' ||
                $eventDate === '' ||
                $eventTime === '' ||
                $venue === '' ||
                $capacity === false ||
                $capacity === null ||
                $capacity < 0
            ) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid event details.'
                ]);
                exit;
            }

            $reserved = (int) $event['capacity'] -
                        (int) $event['available_slots'];

            if ($capacity < $reserved) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Capacity cannot be less than reserved tickets.'
                ]);
                exit;
            }

            $availableSlots = $capacity - $reserved;

            $sql = "UPDATE events SET
                    title = :title,
                    description = :description,
                    event_date = :event_date,
                    event_time = :event_time,
                    venue = :venue,
                    capacity = :capacity,
                    available_slots = :available_slots,
                    updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id";

            $stmt = $db->prepare($sql);

            $stmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':event_date' => $eventDate,
                ':event_time' => $eventTime,
                ':venue' => $venue,
                ':capacity' => $capacity,
                ':available_slots' => $availableSlots,
                ':id' => $id
            ]);

            http_response_code(200);

            echo json_encode([
                'success' => true,
                'message' => 'Event updated successfully.',
                'event_id' => $id
            ]);

        }


    // PATCH: Partially update an existing event
    elseif ($method === 'PATCH') {

        $input = json_decode(file_get_contents('php://input'), true);

        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid JSON input.'
            ]);
            exit;
        }

        $id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);

        if (!$id || $id <= 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Valid event ID is required.'
            ]);
            exit;
        }

        // Check if the event exists.
        $stmt = $db->prepare("SELECT * FROM events WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Event not found.'
            ]);
            exit;
        }

        // Allow only these fields to be updated.
        $allowedFields = [
            'title',
            'description',
            'event_date',
            'event_time',
            'venue',
            'capacity'
        ];

        $updates = [];
        $params = [':id' => $id];

        foreach ($allowedFields as $field) {

            if (array_key_exists($field, $input)) {

                $value = $input[$field];

                if (in_array($field, ['title', 'venue'])) {
                    $value = trim((string) $value);

                    if ($value === '') {
                        http_response_code(400);
                        echo json_encode([
                            'success' => false,
                            'message' => ucfirst($field) . ' cannot be empty.'
                        ]);
                        exit;
                    }
                }

                if ($field === 'capacity') {
                    $value = filter_var($value, FILTER_VALIDATE_INT);

                    if ($value === false || $value <= 0) {
                        http_response_code(400);
                        echo json_encode([
                            'success' => false,
                            'message' => 'Capacity must be a positive integer.'
                        ]);
                        exit;
                    }

                    $reserved = (int) $event['capacity']
                            - (int) $event['available_slots'];

                    if ($value < $reserved) {
                        http_response_code(400);
                        echo json_encode([
                            'success' => false,
                            'message' => 'Capacity cannot be less than reserved tickets.'
                        ]);
                        exit;
                    }

                    $updates[] = "available_slots = :available_slots";
                    $params[':available_slots'] = $value - $reserved;
                }

                $updates[] = "$field = :$field";
                $params[":$field"] = $value;
            }
        }

        if (empty($updates)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'No valid event fields provided for update.'
            ]);
            exit;
        }

        $updates[] = "updated_at = CURRENT_TIMESTAMP";

        $sql = "UPDATE events SET " . implode(', ', $updates)
            . " WHERE id = :id";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'message' => 'Event partially updated successfully.',
            'event_id' => $id,
            'updated_fields' => array_keys(
                array_intersect_key($input, array_flip($allowedFields))
            )
        ]);
    }


        // DELETE: Remove an event
        elseif ($method === 'DELETE') {

            $input = json_decode(file_get_contents('php://input'), true);

            if (!is_array($input)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid JSON input.'
                ]);
                exit;
            }

            $id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);

            if (!$id || $id <= 0) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Valid event ID is required.'
                ]);
                exit;
            }

            $sql = "SELECT id FROM events WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => $id]);

            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Event not found.'
                ]);
                exit;
            }

            // Check if the event has existing reservations.
    $sql = "SELECT COUNT(*) FROM reservations WHERE event_id = :event_id";
    $stmt = $db->prepare($sql);
    $stmt->execute([':event_id' => $id]);

    $reservationCount = (int) $stmt->fetchColumn();

    if ($reservationCount > 0) {
        http_response_code(409);

        echo json_encode([
            'success' => false,
            'message' => 'Event cannot be deleted because it has existing reservations.'
        ]);

        exit;
    }

        $sql = "DELETE FROM events WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'message' => 'Event deleted successfully.',
            'event_id' => $id
        ]);

    }



    // Unsupported HTTP method
    else {

        http_response_code(405);

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed.'
        ]);

    }

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing the request.'
    ]);

}

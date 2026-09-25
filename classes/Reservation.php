<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/Reservable.php';
require_once __DIR__ . '/Attendee.php';

class Reservation extends BaseModel implements Reservable
{
    private int $id = 0;
    private int $eventId;
    private int $attendeeId;
    private string $reservationDate;
    private string $status;
    private int $quantity;

    public function __construct (
        int $eventId = 0,
        int $attendeeId = 0,
        int $quantity = 0,
        string $reservationDate = '',
        string $status = 'PENDING'
    ) {
        parent::__construct();
        $this -> eventId = $eventId;
        $this -> attendeeId = $attendeeId;
        $this -> quantity = $quantity;
        $this -> reservationDate = $reservationDate;
        $this -> status = $status;
    }

    public function getId(): int
    {
        return $this -> id;
    }
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getEventId(): int
    {
        return $this -> eventId;
    }

    public function getAttendeeId(): int
    {
        return $this -> attendeeId;
    }

    public function getReservationDate(): string
    {
        return $this -> reservationDate;
    }

    public function getStatus(): string
    {
        return $this -> status;
    }

    public function getQuantity(): int
    {
        return $this -> quantity;
    }

    public function setEventId(int $eventId): void
    {
        $this ->eventId = $eventId;    
    }

    public function setAttendeeId(int $attendeeId): void
    {
        $this ->attendeeId = $attendeeId;
    }

    public function setReservationDate(string $reservationDate): void
    {
        $this -> reservationDate = $reservationDate;
    }

    public function setStatus(string $status): void 
    {
        $this -> status = $status;
    }

    public function setQuantity(int $quantity): void
    {
        $this -> quantity = $quantity;
    }

    public function reserve(int $quantity): bool
    {
        if ($quantity <= 0) 
            {
                return false;
            }

        $this -> quantity = $quantity;
        $this -> status = 'CONFIRMED';
        
        return true;
    }

    public function cancelReservation(): bool
    {
        if ($this -> status !== 'CONFIRMED')
            {
                return false;
            }
        
        $this -> status = 'CANCELLED';
        
        return true;
    }

    public function isValid(): bool
    {
        if ($this -> eventId <= 0)
            {
                return false;
            }
        
        if ($this -> attendeeId <= 0)
            {
                return false;
            }
        
        if ($this -> quantity <= 0)
            {
                return false;
            }

        if (empty($this -> reservationDate))
            {
                return false;
            }

        return true;
    }

    //create a new reservation in the database
    public function create(): bool
    {
        $sql = "INSERT INTO reservations
            (
                event_id,
                attendee_id,
                reservation_date,
                quantity,
                status
            )
            VALUES
            (
                :event_id,
                :attendee_id,
                :reservation_date,
                :quantity,
                :status
            )";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
        ':event_id' => $this->eventId,
        ':attendee_id' => $this->attendeeId,
        ':reservation_date' => $this->reservationDate,
        ':quantity' => $this->quantity,
        ':status' => $this->status
        ]);
    }

    //get all reservations from the database
    public function getAllReservations(): array
    {
        $sql = "SELECT
                    r.id,
                    r.event_id,
                    r.attendee_id,
                    r.reservation_date,
                    r.quantity,
                    r.status,
                    e.title AS event_title,
                    CONCAT(a.first_name, ' ', a.last_name) AS attendee_name,
                    a.email AS attendee_email
                FROM reservations r
                INNER JOIN events e
                    ON r.event_id = e.id
                INNER JOIN attendees a
                    ON r.attendee_id = a.id
                ORDER BY r.reservation_date DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    //get reservation by id from the database
    public function getReservationById(int $id): ?array
    {
        $sql = "SELECT
                    r.id,
                    r.event_id,
                    r.attendee_id,
                    r.reservation_date,
                    r.quantity,
                    r.status,
                    e.title AS event_title,
                    CONCAT(a.first_name, ' ', a.last_name) AS attendee_name,
                    a.email AS attendee_email,
                    t.id AS ticket_id,
                    t.ticket_number,
                    t.issued_at AS ticket_issued_at,
                    t.status AS ticket_status
                FROM reservations r
                INNER JOIN events e
                    ON r.event_id = e.id
                INNER JOIN attendees a
                    ON r.attendee_id = a.id
                LEFT JOIN tickets t
                    ON r.id = t.reservation_id
                WHERE r.id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $reservation = $stmt->fetch();

        if ($reservation === false) {
            return null;
        }

        return $reservation;
    }

    //update reservation in the database
    public function update(): bool
    {
        $sql = "UPDATE reservations
                SET
                    event_id = :event_id,
                    attendee_id = :attendee_id,
                    quantity = :quantity,
                    status = :status
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':event_id' => $this->eventId,
            ':attendee_id' => $this->attendeeId,
            ':quantity' => $this->quantity,
            ':status' => $this->status,
            ':id' => $this->id
        ]);
    }

    //delete reservation from the database/
    public function delete(): bool
    {
        $sql = "DELETE FROM reservations
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $this->id
        ]);
    }

    //create a reservation transaction
    public function createReservationTransaction(
        string $firstName,
        string $lastName,
        string $email,
        string $phone
    ): int {
        if ($this->eventId <= 0) {
            throw new Exception("Invalid event.");
        }

        if ($this->quantity <= 0) {
            throw new Exception("Reservation quantity must be greater than zero.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email address.");
        }

        try {

            $this->db->beginTransaction();

            // Check the event and lock the row
            $sql = "SELECT
                        id,
                        available_slots,
                        status
                    FROM events
                    WHERE id = :event_id
                    FOR UPDATE";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':event_id' => $this->eventId
            ]);

            $event = $stmt->fetch();

            if ($event === false) {
                throw new Exception("Event not found.");
            }

            if ($event['status'] !== 'OPEN') {
                throw new Exception("This event is not currently open for reservation.");
            }

            if ($this->quantity > (int) $event['available_slots']) {
                throw new Exception("Not enough available slots.");
            }

            // Find existing attendee by email
            $attendeeModel = new Attendee();

            $attendee = $attendeeModel->getAttendeeByEmail($email);

            if ($attendee === null) {

                // Create new attendee
                $attendeeModel->setFirstName($firstName);
                $attendeeModel->setLastName($lastName);
                $attendeeModel->setEmail($email);
                $attendeeModel->setPhone($phone);

                if (!$attendeeModel->create()) {
                    throw new Exception("Unable to create attendee.");
                }

                // Get the newly created attendee
                $attendee = $attendeeModel->getAttendeeByEmail($email);

                if ($attendee === null) {
                    throw new Exception("Unable to retrieve the new attendee.");
                }

            } else {

                // Use existing attendee
                $this->attendeeId = (int) $attendee['id'];
            }

            $this->attendeeId = (int) $attendee['id'];

            // Create reservation
            $this->status = 'CONFIRMED';

            $sql = "INSERT INTO reservations
                    (
                        event_id,
                        attendee_id,
                        reservation_date,
                        quantity,
                        status
                    )
                    VALUES
                    (
                        :event_id,
                        :attendee_id,
                        CURRENT_TIMESTAMP,
                        :quantity,
                        :status
                    )";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':event_id' => $this->eventId,
                ':attendee_id' => $this->attendeeId,
                ':quantity' => $this->quantity,
                ':status' => $this->status
            ]);

            $reservationId = (int) $this->db->lastInsertId();

        // Decrease available slots
        $sql = "UPDATE events
                SET available_slots = available_slots - :quantity
                WHERE id = :event_id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':quantity' => $this->quantity,
            ':event_id' => $this->eventId
        ]);

    // Generate ticket number
    $ticketNumber = 'TKT-' . date('YmdHis') . '-' . strtoupper(
        bin2hex(random_bytes(3))
    );

    // Create ticket
    $sql = "INSERT INTO tickets
            (
                reservation_id,
                ticket_number,
                issued_at,
                status
            )
            VALUES
            (
                :reservation_id,
                :ticket_number,
                CURRENT_TIMESTAMP,
                :status
            )";

    $stmt = $this->db->prepare($sql);

    if (!$stmt->execute([
        ':reservation_id' => $reservationId,
        ':ticket_number' => $ticketNumber,
        ':status' => 'VALID'
    ])) {
        throw new Exception("Unable to create ticket.");
    }

    $this->db->commit();

                $this->id = $reservationId;

                return $reservationId;

            } catch (Exception $e) {

                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }

                throw $e;
            }
    }
}

?>
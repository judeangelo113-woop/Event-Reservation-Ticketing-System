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

    // Update reservation in the databas
    public function update(): bool
    {
        if ($this->id <= 0) {
            throw new Exception("Invalid reservation.");
        }

        if ($this->eventId <= 0) {
            throw new Exception("Invalid event.");
        }

        if ($this->attendeeId <= 0) {
            throw new Exception("Invalid attendee.");
        }

        if ($this->quantity <= 0) {
            throw new Exception("Reservation quantity must be greater than zero.");
        }

        $allowedStatuses = [
            'CONFIRMED',
            'CANCELLED'
        ];

        if (!in_array($this->status, $allowedStatuses, true)) {
            throw new Exception("Invalid reservation status.");
        }

        try {

            $this->db->beginTransaction();

            // Get the existing reservation
            $sql = "SELECT
                        id,
                        event_id,
                        quantity,
                        status
                    FROM reservations
                    WHERE id = :id
                    FOR UPDATE";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':id' => $this->id
            ]);

            $oldReservation = $stmt->fetch();

            if ($oldReservation === false) {
                throw new Exception("Reservation not found.");
            }

            $oldEventId = (int) $oldReservation['event_id'];
            $oldQuantity = (int) $oldReservation['quantity'];
            $oldStatus = $oldReservation['status'];

            /*
            * Return the old reserved slots if the
            * old reservation was CONFIRMED.
            */
            if ($oldStatus === 'CONFIRMED') {

                $sql = "UPDATE events
                        SET available_slots = available_slots + :quantity
                        WHERE id = :event_id";

                $stmt = $this->db->prepare($sql);

                $stmt->execute([
                    ':quantity' => $oldQuantity,
                    ':event_id' => $oldEventId
                ]);
            }

            /*
            * Reserve slots again if the updated
            * reservation will be CONFIRMED.
            */
            if ($this->status === 'CONFIRMED') {

                $sql = "SELECT
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
                    throw new Exception(
                        "This event is not currently open for reservation."
                    );
                }

                if ($this->quantity > (int) $event['available_slots']) {
                    throw new Exception("Not enough available slots.");
                }

                $sql = "UPDATE events
                        SET available_slots = available_slots - :quantity
                        WHERE id = :event_id";

                $stmt = $this->db->prepare($sql);

                $stmt->execute([
                    ':quantity' => $this->quantity,
                    ':event_id' => $this->eventId
                ]);
            }

            /*
            * Update the reservation.
            */
            $sql = "UPDATE reservations
                    SET
                        event_id = :event_id,
                        attendee_id = :attendee_id,
                        quantity = :quantity,
                        status = :status
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':event_id' => $this->eventId,
                ':attendee_id' => $this->attendeeId,
                ':quantity' => $this->quantity,
                ':status' => $this->status,
                ':id' => $this->id
            ]);

            /*
            * Keep the ticket status synchronized
            * with the reservation status.
            */
            $ticketStatus = ($this->status === 'CONFIRMED')
                ? 'VALID'
                : 'CANCELLED';

            $sql = "UPDATE tickets
                    SET status = :ticket_status
                    WHERE reservation_id = :reservation_id";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':ticket_status' => $ticketStatus,
                ':reservation_id' => $this->id
            ]);

            $this->db->commit();

            return true;

        } catch (Exception $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
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

            // Start one transaction for the entire reservation process
            $this->db->beginTransaction();

            /*
            * 1. Lock the event row
            * so available slots cannot be changed by
            * another reservation at the same time.
            */
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
                throw new Exception(
                    "This event is not currently open for reservation."
                );
            }

            if ($this->quantity > (int) $event['available_slots']) {
                throw new Exception("Not enough available slots.");
            }

            /*
            * 2. Find attendee using the SAME PDO connection.
            */
            $sql = "SELECT
                        id,
                        first_name,
                        last_name,
                        email,
                        phone
                    FROM attendees
                    WHERE email = :email";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':email' => $email
            ]);

            $attendee = $stmt->fetch();

            /*
            * 3. Create attendee if they don't exist.
            */
            if ($attendee === false) {

                $sql = "INSERT INTO attendees
                        (
                            first_name,
                            last_name,
                            email,
                            phone
                        )
                        VALUES
                        (
                            :first_name,
                            :last_name,
                            :email,
                            :phone
                        )";

                $stmt = $this->db->prepare($sql);

                $stmt->execute([
                    ':first_name' => trim($firstName),
                    ':last_name' => trim($lastName),
                    ':email' => trim($email),
                    ':phone' => trim($phone)
                ]);

                $this->attendeeId = (int) $this->db->lastInsertId();

            } else {

                $this->attendeeId = (int) $attendee['id'];
            }

            /*
            * 4. Create the reservation.
            */
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

            /*
            * 5. Decrease available slots.
            */
            $sql = "UPDATE events
                    SET available_slots = available_slots - :quantity
                    WHERE id = :event_id";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':quantity' => $this->quantity,
                ':event_id' => $this->eventId
            ]);

            /*
            * 6. Generate a unique ticket number.
            */
            $ticketNumber = 'TKT-' . date('YmdHis') . '-' . strtoupper(
                bin2hex(random_bytes(3))
            );

            /*
            * 7. Create the ticket.
            */
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

            $stmt->execute([
                ':reservation_id' => $reservationId,
                ':ticket_number' => $ticketNumber,
                ':status' => 'VALID'
            ]);

            /*
            * 8. Everything succeeded.
            * Commit the entire transaction.
            */
            $this->db->commit();

            $this->id = $reservationId;

            return $reservationId;

        } catch (Exception $e) {

            /*
            * If anything failed, undo everything
            * performed during this transaction.
            */
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    // Delete reservation from the database
    public function delete(): bool
    {
        if ($this->id <= 0) {
            throw new Exception("Invalid reservation.");
        }

        try {
            $this->db->beginTransaction();

            // Get the reservation before deleting it
            $sql = "SELECT
                        event_id,
                        quantity,
                        status
                    FROM reservations
                    WHERE id = :id
                    FOR UPDATE";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':id' => $this->id
            ]);

            $reservation = $stmt->fetch();

            if ($reservation === false) {
                throw new Exception("Reservation not found.");
            }

            // Return the reserved slots if the reservation is confirmed
            if ($reservation['status'] === 'CONFIRMED') {

                $sql = "UPDATE events
                        SET available_slots = available_slots + :quantity
                        WHERE id = :event_id";

                $stmt = $this->db->prepare($sql);

                $stmt->execute([
                    ':quantity' => (int) $reservation['quantity'],
                    ':event_id' => (int) $reservation['event_id']
                ]);
            }

            // Delete the reservation
            $sql = "DELETE FROM reservations
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':id' => $this->id
            ]);

            $this->db->commit();

            return true;

        } catch (Exception $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }
}

?>
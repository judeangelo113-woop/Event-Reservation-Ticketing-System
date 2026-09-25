<?php

require_once __DIR__ . '/BaseModel.php';

class Ticket extends BaseModel
{
    private int $id = 0;
    private int $reservationId;
    private string $ticketNumber;
    private string $issuedAt;
    private string $status;

    public function __construct(
        int $reservationId = 0,
        string $ticketNumber = '',
        string $issuedAt = '',
        string $status = 'VALID'
    ) {
        parent::__construct();

        $this->reservationId = $reservationId;
        $this->ticketNumber = $ticketNumber;
        $this->issuedAt = $issuedAt;
        $this->status = $status;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function getReservationId(): int
    {
        return $this->reservationId;
    }

    public function getTicketNumber(): string
    {
        return $this->ticketNumber;
    }

    public function getIssuedAt(): string
    {
        return $this->issuedAt;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setReservationId(int $reservationId): void
    {
        $this->reservationId = $reservationId;
    }

    public function setTicketNumber(string $ticketNumber): void
    {
        $this->ticketNumber = trim($ticketNumber);
    }

    public function setIssuedAt(string $issuedAt): void
    {
        $this->issuedAt = $issuedAt;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function cancel(): void
    {
        $this->status = 'CANCELLED';
    }

    public function isValid(): bool
    {
        return $this->status === 'VALID';
    }


    //create ticket
    public function create(): bool
    {
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

        return $stmt->execute([
            ':reservation_id' => $this->reservationId,
            ':ticket_number' => $this->ticketNumber,
            ':status' => $this->status
        ]);
    }

    //generate ticket number
    public function generateTicketNumber(): string
    {
        return 'TKT-' . date('YmdHis') . '-' . strtoupper(
            bin2hex(random_bytes(3))
        );
    }

    //get all tickets
    public function getAllTickets(): array
    {
        $sql = "SELECT
                    t.id,
                    t.reservation_id,
                    t.ticket_number,
                    t.issued_at,
                    t.status,
                    r.event_id,
                    r.attendee_id,
                    r.quantity,
                    e.title AS event_title,
                    CONCAT(a.first_name, ' ', a.last_name) AS attendee_name,
                    a.email AS attendee_email
                FROM tickets t
                INNER JOIN reservations r
                    ON t.reservation_id = r.id
                INNER JOIN events e
                    ON r.event_id = e.id
                INNER JOIN attendees a
                    ON r.attendee_id = a.id
                ORDER BY t.issued_at DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }
    
    //get ticket by Id
    public function getTicketById(int $id): ?array
    {
        $sql = "SELECT
                    t.id,
                    t.reservation_id,
                    t.ticket_number,
                    t.issued_at,
                    t.status,
                    r.event_id,
                    r.attendee_id,
                    r.quantity,
                    e.title AS event_title,
                    CONCAT(a.first_name, ' ', a.last_name) AS attendee_name,
                    a.email AS attendee_email
                FROM tickets t
                INNER JOIN reservations r
                    ON t.reservation_id = r.id
                INNER JOIN events e
                    ON r.event_id = e.id
                INNER JOIN attendees a
                    ON r.attendee_id = a.id
                WHERE t.id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $ticket = $stmt->fetch();

        if ($ticket === false) {
            return null;
        }

        return $ticket;
    }
    
    //update ticket
    public function update(): bool
    {
        $sql = "UPDATE tickets
                SET
                    ticket_number = :ticket_number,
                    status = :status
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':ticket_number' => $this->ticketNumber,
            ':status' => $this->status,
            ':id' => $this->id
        ]);
    }

    public function delete(): bool
    {
        $sql = "DELETE FROM tickets
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $this->id
        ]);
    }
}
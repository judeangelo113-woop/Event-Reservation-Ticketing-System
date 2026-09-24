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
}
<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/Reservable.php';

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
        string $status = 'PENDING',
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

    public function reserve($quantity): bool
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
}

?>
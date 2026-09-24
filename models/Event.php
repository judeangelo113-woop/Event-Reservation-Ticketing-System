<?php

require_once __DIR__ . '/BaseModel.php';

class Event extends BaseModel
{
    private int $id = 0;
    private string $title;
    private string $description;
    private string $eventDate;
    private string $eventTime;
    private string $venue;
    private int $capacity;
    private int $availableSlots;
    private string $status;

    public function __construct(
        string $title = '',
        string $description = '',
        string $eventDate = '',
        string $eventTime = '',
        string $venue = '',
        int $capacity = 0,
        int $availableSlots = 0,
        string $status = 'OPEN'
    ) {
        parent::__construct();

        $this->title = $title;
        $this->description = $description;
        $this->eventDate = $eventDate;
        $this->eventTime = $eventTime;
        $this->venue = $venue;
        $this->capacity = $capacity;
        $this->availableSlots = $availableSlots;
        $this->status = $status;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = trim($title);
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = trim($description);
    }

    public function getEventDate(): string
    {
        return $this->eventDate;
    }

    public function setEventDate(string $eventDate): void
    {
        $this->eventDate = $eventDate;
    }

    public function getEventTime(): string
    {
        return $this->eventTime;
    }

    public function setEventTime(string $eventTime): void
    {
        $this->eventTime = $eventTime;
    }

    public function getVenue(): string
    {
        return $this->venue;
    }

    public function setVenue(string $venue): void
    {
        $this->venue = trim($venue);
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): void
    {
        $this->capacity = $capacity;
    }

    public function getAvailableSlots(): int
    {
        return $this->availableSlots;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    
    public function create(
        string $title,
        string $description,
        string $eventDate,
        string $eventTime,
        string $venue,
        int $capacity
    ): bool {
        $sql = "
            INSERT INTO events
            (
                title,
                description,
                event_date,
                event_time,
                venue,
                capacity,
                available_slots,
                status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            trim($title),
            trim($description),
            $eventDate,
            $eventTime,
            trim($venue),
            $capacity,
            $capacity,
            'OPEN'
        ]);
    }

   
    public function update(
        int $id,
        string $title,
        string $description,
        string $eventDate,
        string $eventTime,
        string $venue,
        int $capacity
    ): bool {
        $sql = "
            UPDATE events
            SET
                title = ?,
                description = ?,
                event_date = ?,
                event_time = ?,
                venue = ?,
                capacity = ?,
                available_slots = ?,
                status = ?
            WHERE id = ?
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            trim($title),
            trim($description),
            $eventDate,
            $eventTime,
            trim($venue),
            $capacity,
            $capacity,
            'OPEN',
            $id
        ]);
    }

    
   public function search(
    string $search = '',
    string $sort = 'event_date',
    string $order = 'ASC'
): array {
    $allowedSort = [
        'title',
        'event_date',
        'event_time',
        'venue',
        'capacity',
        'available_slots',
        'status'
    ];

    if (!in_array($sort, $allowedSort, true)) {
        $sort = 'event_date';
    }

    $order = strtoupper($order);

    if (!in_array($order, ['ASC', 'DESC'], true)) {
        $order = 'ASC';
    }

    $sql = "
        SELECT *
        FROM events
        WHERE
            title LIKE ?
            OR venue LIKE ?
            OR description LIKE ?
        ORDER BY $sort $order
    ";

    $keyword = '%' . trim($search) . '%';

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        $keyword,
        $keyword,
        $keyword
    ]);

    return $stmt->fetchAll();
}
}
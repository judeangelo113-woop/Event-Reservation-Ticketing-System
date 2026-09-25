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

    public function setId(int $id): void
{
    $this->id = $id;
}

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
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
        $this->venue = $venue;
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

    //create a new event
    public function create(): bool
    {
        $sql = "INSERT INTO events
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
            VALUES
            (
                :title,
                :description,
                :event_date,
                :event_time,
                :venue,
                :capacity,
                :available_slots,
                :status
            )";

        $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        ':title' => $this->title,
        ':description' => $this->description,
        ':event_date' => $this->eventDate,
        ':event_time' => $this->eventTime,
        ':venue' => $this->venue,
        ':capacity' => $this->capacity,
        ':available_slots' => $this->capacity,
        ':status' => $this->status
    ]);
    }

    //read all events from the database
    public function getAllEvents(
    string $search = '',
    string $status = '',
    string $sort = 'date_asc'
    ): array
    {
    $sql = "SELECT
                id,
                title,
                description,
                event_date,
                event_time,
                venue,
                capacity,
                available_slots,
                status,
                created_at,
                updated_at
            FROM events";

    $conditions = [];
    $params = [];

    if ($search !== '') {

        $conditions[] = "(title LIKE :title_search
                        OR venue LIKE :venue_search)";

        $params[':title_search'] = '%' . $search . '%';
        $params[':venue_search'] = '%' . $search . '%';
    }

    $allowedStatuses = [
        'OPEN',
        'CLOSED',
        'CANCELLED'
    ];

    if (in_array($status, $allowedStatuses, true)) {

        $conditions[] = "status = :status";

        $params[':status'] = $status;
    }

    
    if (!empty($conditions)) {

        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    
   // Allowed sorting options
    $allowedSorts = [
        'date_asc' => 'event_date ASC, event_time ASC',
        'date_desc' => 'event_date DESC, event_time DESC',
        'title_asc' => 'title ASC',
        'title_desc' => 'title DESC',
        'capacity_asc' => 'capacity ASC',
        'capacity_desc' => 'capacity DESC'
    ];

// Use the selected sort or default to date ascending
$orderBy = $allowedSorts[$sort] ?? $allowedSorts['date_asc'];

$sql .= " ORDER BY " . $orderBy;

    $stmt = $this->db->prepare($sql);

    $stmt->execute($params);

    return $stmt->fetchAll();
    }

    //update an existing event in the database
    public function update(): bool
    {
            $sql = "UPDATE events
                SET
                    title = :title,
                    description = :description,
                    event_date = :event_date,
                    event_time = :event_time,
                    venue = :venue,
                    capacity = :capacity,
                    status = :status
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':title' => $this->title,
            ':description' => $this->description,
            ':event_date' => $this->eventDate,
            ':event_time' => $this->eventTime,
            ':venue' => $this->venue,
            ':capacity' => $this->capacity,
            ':status' => $this->status,
            ':id' => $this->id
        ]);
    }

    //delete an event from the database
    public function delete(): bool
    {
        $sql = "DELETE FROM events
                WHERE id = :id";

        $stmt = $this -> db -> prepare($sql);

        return $stmt -> execute([
            ':id' => $this -> id
        ]);
    }

    //get an event by its ID
    public function getEventById(int $id): ?array
    {
        $sql = "SELECT
                    id,
                    title,
                    description,
                    event_date,
                    event_time,
                    venue,
                    capacity,
                    available_slots,
                    status,
                    created_at,
                    updated_at
                FROM events
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $event = $stmt->fetch();

        if ($event === false) {
            return null;
        }

        return $event;
    }   

    
}

?>
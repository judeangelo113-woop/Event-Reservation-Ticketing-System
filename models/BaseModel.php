<?php

require_once __DIR__ . '/../config/Database.php';

class BaseModel
{
    protected PDO $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM events ORDER BY event_date ASC, event_time ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM events WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM events WHERE id = ?";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([$id]);
    }
}
<?php

require_once __DIR__ . '/../config/Database.php';

class BaseModel
{
    protected PDO $db;

    public function __construct()
    {
        try {
            $database = new Database();

            $this->db = $database->connect();

        } catch (PDOException $e) {
            throw new Exception(
                "Unable to connect to the database.",
                0,
                $e
            );
        }
    }
}
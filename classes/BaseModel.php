<?php
    
    require_once __DIR__ . '/../config/Database.php';

        class BaseModel
        {
            protected PDO $db;

            public function __construct()
            {
                $database = new Database();

                $this ->db = $database -> connect();
            }
        }

?>
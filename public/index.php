<?php

require_once '../config/Database.php';

$database = new Database();
$pdo = $database->connect();

echo "Database connection successful!";

?>
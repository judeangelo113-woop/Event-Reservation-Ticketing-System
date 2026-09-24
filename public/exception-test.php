<?php

require_once __DIR__ . '/../classes/BaseModel.php';

try {
    $test = new BaseModel();

    echo "Database connection successful.";
} catch (Exception $e) {
    echo "Exception handled successfully: " . $e->getMessage();
}
<?php

try {
    $pdo = new PDO("sqlite:" . __DIR__ . "/../storage/database.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error while trying to connect SQLite database: " . $e->getMessage());
}

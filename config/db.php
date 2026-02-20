<?php
// config/db.php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

$DB_HOST = 'localhost';
$DB_NAME = 'araf_blog';
$DB_USER = 'root';
$DB_PASS = ''; // set your password if you have one

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed.");
}
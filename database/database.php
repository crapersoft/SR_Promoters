<?php
require_once __DIR__ . '/../vendor/autoload.php'; // For loading .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$connection = mysqli_connect($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);

if (!$connection) {
    die("Database connection error");
}
?>

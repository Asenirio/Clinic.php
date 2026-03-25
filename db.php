<?php
// Database Configuration
$host = 'localhost';
$db = 'clinic_portal';
$user = 'root';
$pass = ''; // Default XAMPP password is empty
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // If database doesn't exist, provide a helpful message or attempt to create it
    // For this task, we assume the user might need to run the SQL script first
    die("Database connection failed: " . $e->getMessage() . ". Please ensure 'clinic_portal' database exists and 'db_setup.sql' has been run.");
}
?>
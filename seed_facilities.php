<?php
require_once 'config.php';

try {
    $facilities = [
        ['General Ward Bed 01', 'bed', 'available'],
        ['General Ward Bed 02', 'bed', 'occupied'],
        ['General Ward Bed 03', 'bed', 'maintenance'],
        ['Private Room A1', 'room', 'available'],
        ['Private Room A2', 'room', 'available'],
        ['ICU Bed 01', 'icu', 'occupied'],
        ['ICU Bed 02', 'icu', 'available'],
        ['Operating Theater 1', 'operating_theater', 'maintenance']
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO facilities (name, type, status) VALUES (?, ?, ?)");
    foreach ($facilities as $f) {
        $stmt->execute($f);
    }

    echo "Facilities seeded successfully!";
} catch (PDOException $e) {
    echo "Seed failed: " . $e->getMessage();
}
?>
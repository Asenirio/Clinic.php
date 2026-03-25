<?php
require 'c:/xampp/htdocs/Clinicportal/db.php';
try {
    $res = $pdo->query("SELECT * FROM specialties")->fetchAll(PDO::FETCH_ASSOC);
    echo "--- Current Specialties ---\n";
    print_r($res);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

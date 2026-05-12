<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=flowershop_db;charset=utf8", "root", "");
} catch (PDOException $e) {
    die("Connection Failed: " . $e->getMessage());
}
?>

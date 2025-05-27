<?php
$host = 'localhost';
$db   = 'TRack_db';
$user = 'track';
$pass = 'MySQL4track!';

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS `$db`");
$conn->select_db($db);

$conn->query(
    "CREATE TABLE IF NOT EXISTS racks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
    )"
);

$conn->query("CREATE TABLE IF NOT EXISTS shelves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rack_id INT NOT NULL,
    number INT NOT NULL,
    UNIQUE (rack_id, number),
    FOREIGN KEY (rack_id) REFERENCES racks(id) ON DELETE CASCADE
)");

$conn->query("CREATE TABLE IF NOT EXISTS objects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shelf_id INT NOT NULL,
    quantity INT NOT NULL,
    object_number VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    defects TEXT,
    FOREIGN KEY (shelf_id) REFERENCES shelves(id) ON DELETE CASCADE
)");
?>
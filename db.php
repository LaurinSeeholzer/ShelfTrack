<?php
$host = 'localhost';
$db   = 'ShelfTrackInventory_db';
$user = 'shelftrack';
$pass = 'MySQL4ShelfTrack!';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
?>
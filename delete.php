<?php
require_once 'db.php';

if (!isset($_GET['id'])) {
    die("No ID provided.");
}

$id = intval($_GET['id']);

// Delete from database
$stmt = $conn->prepare("DELETE FROM objects WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// Delete image file if it exists
$image_file = __DIR__ . "/upload/" . $id . ".jpg";
if (file_exists($image_file)) {
    unlink($image_file);
}

// Redirect back to main page
header("Location: index.php");
exit;
?>
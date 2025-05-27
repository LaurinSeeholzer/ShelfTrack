<?php require_once 'db.php';

    $id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM objects WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    $image_file = __DIR__ . "/upload/" . $id . ".jpg";
    if (file_exists($image_file)) {
        unlink($image_file);
    }

    header("Location: index.php");
    exit;
?>
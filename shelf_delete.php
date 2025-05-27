<?php require_once 'db.php';

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $shelf_id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT id FROM objects WHERE shelf_id = ?");
    $stmt->bind_param("i", $shelf_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $object_id = $row['id'];
        $image_path = __DIR__ . "/upload/" . $object_id . ".jpg";

        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM objects WHERE shelf_id = ?");
    $stmt->bind_param("i", $shelf_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM shelves WHERE id = ?");
    $stmt->bind_param("i", $shelf_id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
    
?>

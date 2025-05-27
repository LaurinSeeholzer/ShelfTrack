<?php require_once 'db.php';

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $rack_id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT id FROM shelves WHERE rack_id = ?");
    $stmt->bind_param("i", $rack_id);
    $stmt->execute();
    $shelf_result = $stmt->get_result();
    $shelf_ids = [];

    while ($row = $shelf_result->fetch_assoc()) {
        $shelf_ids[] = $row['id'];
    }
    $stmt->close();

    foreach ($shelf_ids as $shelf_id) {
        $stmt = $conn->prepare("SELECT id FROM objects WHERE shelf_id = ?");
        $stmt->bind_param("i", $shelf_id);
        $stmt->execute();
        $object_result = $stmt->get_result();

        while ($obj = $object_result->fetch_assoc()) {
            $image_path = __DIR__ . "/upload/" . $obj['id'] . ".jpg";
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        $stmt->close();
    }

    if (count($shelf_ids) > 0) {
        // Step 3: Delete all objects on these shelves
        $placeholders = implode(',', array_fill(0, count($shelf_ids), '?'));
        $types = str_repeat('i', count($shelf_ids));  // type string for bind_param

        $stmt = $conn->prepare("DELETE FROM objects WHERE shelf_id IN ($placeholders)");
        $stmt->bind_param($types, ...$shelf_ids);
        $stmt->execute();
        $stmt->close();
    }


    $stmt = $conn->prepare("DELETE FROM shelves WHERE rack_id = ?");
    $stmt->bind_param("i", $rack_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM racks WHERE id = ?");
    $stmt->bind_param("i", $rack_id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
?>

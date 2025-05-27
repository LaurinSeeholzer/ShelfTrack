<?php require_once 'db.php';

    $id = intval($_GET['id']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $shelf_id = intval($_POST['shelf_id']);
        $quantity = intval($_POST['quantity']);
        $object_number = $_POST['object_number'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $defects = $_POST['defects'];

        if (!empty($_FILES['image']['tmp_name'])) {
            $upload_path = __DIR__ . "/upload/" . $id . ".jpg";
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_path);
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $stmt = $conn->prepare("UPDATE objects SET shelf_id=?, quantity=?, object_number=?, image_path=?, name=?, description=?, defects=? WHERE id=?");
                $stmt->bind_param("iisssssi", $shelf_id, $quantity, $object_number, $target_dir, $name, $description, $defects, $id);
            } else {
                die("Image upload failed.");
            }

        } else {
            $stmt = $conn->prepare("UPDATE objects SET shelf_id=?, quantity=?, object_number=?, name=?, description=?, defects=? WHERE id=?");
            $stmt->bind_param("iissssi", $shelf_id, $quantity, $object_number, $name, $description, $defects, $id);
        }

        $stmt->execute();
        $stmt->close();

        header("Location: index.php");
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM objects WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $object = $result->fetch_assoc();

    $shelves = $conn->query("SELECT id, name FROM shelves");

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Object</title>
</head>
<body>
    <h2>Edit Object</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Shelf:</label><br>
        <select name="shelf_id" id="shelf_id" required>
            <?php while ($shelf = $shelves->fetch_assoc()): ?>
                <option value="<?= $shelf['id'] ?>" <?= $shelf['id'] == $object['shelf_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($shelf['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Quantity:</label><br>
        <input type="number" name="quantity" value="<?= $object['quantity'] ?>"><br>

        <label>Object Number:</label><br>
        <input type="text" name="object_number" value="<?= htmlspecialchars($object['object_number']) ?>"><br>

        <label>Image (leave empty to keep current):</label><br>
        <input type="file" name="image"><br>
        <p>Current: <img src="upload/<?= $object['id'] ?>.jpg" alt="Image" height="60"></p>

        <label>Name:</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($object['name']) ?>"><br>

        <label>Description:</label><br>
        <textarea name="description"><?= htmlspecialchars($object['description']) ?></textarea><br>

        <label>Defects:</label><br>
        <textarea name="defects"><?= htmlspecialchars($object['defects']) ?></textarea><br><br>

        <input type="submit" value="Update Object">
    </form>
</body>
</html>

<?php include 'db.php'; ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shelf = $_POST['shelf'];
    $quantity = $_POST['quantity'];
    $object_number = $_POST['object_number'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $defects = $_POST['defects'];

    $stmt = $conn->prepare("INSERT INTO objects (shelf, quantity, object_number, name, description, defects) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissss", $shelf, $quantity, $object_number, $name, $description, $defects);
    $stmt->execute();

    $last_id = $stmt->insert_id;
    $stmt->close();

    // Handle image upload
    if (!empty($_FILES['image']['tmp_name'])) {
        $upload_path = __DIR__ . "/upload/" . $last_id . ".jpg";
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_path);
    }
    
    header("Location: index.php");
    exit;
}
?>

<h2>Add Object</h2>
<form method="post" enctype="multipart/form-data">
    Shelf: <input type="text" name="shelf"><br>
    Quantity: <input type="number" name="quantity"><br>
    Object Number: <input type="text" name="object_number"><br>
    Name: <input type="text" name="name"><br>
    Description:<br> <textarea name="description"></textarea><br>
    Defects:<br> <textarea name="defects"></textarea><br>
    Image: <input type="file" name="image"><br>
    <button type="submit">Add Object</button>
</form>

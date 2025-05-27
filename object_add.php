<?php include 'db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $shelf_id = intval($_POST['shelf_id']);
        $quantity = $_POST['quantity'];
        $object_number = trim($_POST['object_number']);
        $name = $_POST['name'];
        $description = $_POST['description'];
        $defects = $_POST['defects'];
        
        $shelf_check = $conn->prepare("SELECT id FROM shelves WHERE id = ?");
        $shelf_check->bind_param("i", $shelf_id);
        $shelf_check->execute();
        $shelf_check->store_result();

        if ($shelf_check->num_rows === 0) {
            $error = $shelf_check;
        }
        $shelf_check->close();

        $check = $conn->prepare("SELECT id FROM objects WHERE object_number= ?");
        $check->bind_param("s", $object_number);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "This object already exists";
        }
        $check->close();

        if (!isset($error)) {
            $stmt = $conn->prepare("INSERT INTO objects (shelf_id, quantity, object_number, name, description, defects) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissss", $shelf_id, $quantity, $object_number, $name, $description, $defects);
            $stmt->execute();

            $last_id = $stmt->insert_id;
            $stmt->close();

            if (!empty($_FILES['image']['tmp_name'])) {
                $upload_path = __DIR__ . "/upload/" . $last_id . ".jpg";
                move_uploaded_file($_FILES['image']['tmp_name'], $upload_path);
            } else {
                $error = "Object was stored without Image";
            }
                    
            header("Location: index.php");
            exit;
        }
    }

    $shelfsql =  "SELECT shelves.id, shelves.number, racks.name AS rack_name
            FROM shelves
            LEFT JOIN racks ON shelves.rack_id = racks.id
            ORDER BY racks.name ASC, shelves.number + 0 ASC";

    $shelves = $conn->query($shelfsql);

?>

<h2>Add Object</h2>
<form method="post" enctype="multipart/form-data">
    Shelf: 
    <select name="shelf_id" id="shelf_id">
        <?php while ($shelf = $shelves->fetch_assoc()): ?>
            <option value="<?= $shelf['id'] ?>"><?= htmlspecialchars($shelf['rack_name']) ?>.<?= htmlspecialchars($shelf['number']) ?></option>
        <?php endwhile; ?>
    </select>
    Quantity: 
    <input type="number" name="quantity"><br>
    Object Number: 
    <input type="text" name="object_number"><br>
    Name: 
    <input type="text" name="name"><br>
    Description:<br> 
    <textarea name="description"></textarea><br>
    Defects:<br> 
    <textarea name="defects"></textarea><br>
    Image: 
    <input type="file" name="image"><br>
    <button type="submit">Add Object</button>
</form>

<?php if (isset($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

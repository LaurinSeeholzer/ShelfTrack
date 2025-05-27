<?php require_once 'db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $number = intval($_POST['number']);
        $rack_id = intval($_POST['rack_id']);
        
        $rack_check = $conn->prepare("SELECT id FROM racks WHERE id = ?");
        $rack_check->bind_param("i", $rack_id);
        $rack_check->execute();
        $rack_check->store_result();

        if ($rack_check->num_rows === 0) {
            $error = "The given rack does not exist";
        }
        $rack_check->close();

        $check = $conn->prepare("SELECT id FROM shelves WHERE rack_id = ? AND number = ?");
        $check->bind_param("ii", $rack_id, $number);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "This shelf already exists";
        }
        $check->close();

        if (!isset($error)) {
            
            $stmt = $conn->prepare("INSERT INTO shelves (rack_id, number) VALUES (?, ?)");
            $stmt->bind_param("ii", $rack_id, $number);
            $stmt->execute();
            $stmt->close();

            header("Location: index.php");
            exit;
        }

    }

    $racks = $conn->query("SELECT id, name FROM racks");

?>

<form method="POST">
    Rack: 
    <select name="rack_id" id="rack_id">
        <?php while ($rack = $racks->fetch_assoc()): ?>
            <option value="<?= $rack['id'] ?>"><?= htmlspecialchars($rack['name']) ?></option>
        <?php endwhile; ?>
    </select>
    <label for="number">Shelf Number:</label>
    <input type="number" name="number" id="number" required>
    <button type="submit">Add Shelf</button>
</form>

<?php if (isset($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

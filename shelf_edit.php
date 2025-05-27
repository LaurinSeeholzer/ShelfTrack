<?php require_once 'db.php';

    $id = intval($_GET['id']);

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

        $check = $conn->prepare("SELECT id FROM shelves WHERE rack_id = ? AND number = ? AND id != ?");
        $check->bind_param("iii", $rack_id, $number, $id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "This shelf already exists";
        }
        $check->close();

        if (!isset($error)) {

            $stmt = $conn->prepare("UPDATE shelves SET number = ?, rack_id = ? WHERE id = ?");
            $stmt->bind_param("iii", $number, $rack_id, $id);
            $stmt->execute();
            $stmt->close();

            header("Location: index.php");
            exit;
        }

    }

    $rackresult = $conn->query("SELECT * FROM racks");

    $stmt = $conn->prepare("SELECT shelves.id, shelves.number, shelves.rack_id, racks.name AS rack_name FROM shelves LEFT JOIN racks ON shelves.rack_id = racks.id WHERE shelves.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $shelf = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Shelf <?= htmlspecialchars($shelf['rack_name'])?>.<?= htmlspecialchars($shelf['number'])?></title>
</head>
<body>
    <h2>Edit Shelf <?= htmlspecialchars($shelf['rack_name'])?>.<?= htmlspecialchars($shelf['number'])?></h2>
    <form method="POST">
    Rack: 
    <select name="rack_id" id="rack_id">
        <?php while ($rack = $rackresult->fetch_assoc()): ?>
            <option value="<?= $rack['id'] ?>" <?= $rack['id'] == $shelf['rack_id'] ? 'selected' : '' ?>><?= htmlspecialchars($rack['name']) ?></option>
        <?php endwhile; ?>
    </select>
    <label for="number">Shelf Number:</label>
    <input type="number" name="number" id="number" required>
    <button type="submit">Add Shelf</button>
</form>
</body>
</html>

<?php if (isset($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
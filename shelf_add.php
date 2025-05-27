<?php require_once 'db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $number = intval($_POST['number']);
        $rack_id = intval($_POST['rack_id']);
        
        $stmt = $conn->prepare("INSERT INTO shelves (rack_id, number) VALUES (?, ?)");
        $stmt->bind_param("is", $rack_id, $number);

        try {
            $stmt->execute();
            header("Location: index.php");
            exit;
        } catch (mysqli_sql_exception $e) {
            $error = $e;
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

<?php require_once 'db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $name = $_POST['name'];

        $check = $conn->prepare("SELECT id FROM racks WHERE name = ?");
        $check->bind_param("s", $name);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "This rack already exists";
        } else {
            // Safe to insert
            $stmt = $conn->prepare("INSERT INTO racks (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            header("Location: index.php");
            exit;
        }

        $check->close();
    }
?>

<h2>Add Rack</h2>
<form method="POST">
    <input name="name" placeholder="Rack name" required>
    <button type="submit">Add Rack</button>
</form>

<?php if (isset($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
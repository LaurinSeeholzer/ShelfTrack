<?php require_once 'db.php';

    $id = intval($_GET['id']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $name = $_POST['name'];

        $check = $conn->prepare("SELECT id FROM racks WHERE name = ? AND id != ?");
        $check->bind_param("si", $name, $id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "This rack already exists";
        } else {
            // Safe to insert
            $stmt = $conn->prepare("UPDATE racks SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $name, $id);
            $stmt->execute();
            header("Location: index.php");
            exit;
        }

        $check->close();

    }
        
    $stmt = $conn->prepare("SELECT * FROM racks WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rack = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Rack <?= htmlspecialchars($rack['name'])?></title>
</head>
<body>
    <h2>Edit Rack <?= htmlspecialchars($rack['name'])?></h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="name">Rack Name:</label>
        <input type="text" name="name" id="name" required>

        <input type="submit" value="Update Rack">
    </form>
</body>
</html>

<?php if (isset($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

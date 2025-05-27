<?php require_once 'db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $stmt = $conn->prepare("INSERT INTO racks (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        header("Location: index.php");
        exit;
    }
?>

<h2>Add Rack</h2>
<form method="POST">
    <input name="name" placeholder="Rack name" required>
    <button type="submit">Add Rack</button>
</form>
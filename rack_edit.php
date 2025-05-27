<?php require_once 'db.php';

    $id = intval($_GET['id']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $name = $_POST['name'];

        $stmt = $conn->prepare("UPDATE racks SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: index.php");
        exit;
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

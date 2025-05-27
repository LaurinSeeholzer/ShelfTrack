<?php require_once 'db.php';

    $id = intval($_GET['id']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $number = $_POST['number'];

        $stmt = $conn->prepare("UPDATE shelves SET number = ? WHERE id = ?");
        $stmt->bind_param("si", $number, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: index.php");
        exit;
    } 

    $stmt = $conn->prepare("SELECT * FROM shelves WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $shelf = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Shelf <?= htmlspecialchars($shelf['number'])?></title>
</head>
<body>
    <h2>Edit Shelf <?= htmlspecialchars($shelf['number'])?></h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="number">Shelf Number:</label>
        <input type="number" name="number" id="number" required>

        <input type="submit" value="Update Shelf">
    </form>
</body>
</html>

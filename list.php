<?php require_once 'db.php';

    $racksql = "SELECT racks.id, racks.name FROM racks
            ORDER BY racks.name ASC";

    $shelfsql = "SELECT shelves.id, shelves.number, racks.name AS rack_name
            FROM shelves
            LEFT JOIN racks ON shelves.rack_id = racks.id
            ORDER BY racks.name ASC, shelves.number + 0 ASC";

    $objectsql = "SELECT objects.id, objects.quantity, objects.object_number, objects.name, objects.description, objects.defects, shelves.number AS shelf_number, racks.name AS rack_name
            FROM objects
            LEFT JOIN shelves ON objects.shelf_id = shelves.id
            LEFT JOIN racks ON shelves.rack_id = racks.id
            ORDER BY objects.object_number ASC";
    
    $rackresult = $conn->query($racksql);
    $shelfresult = $conn->query($shelfsql);
    $objectresult = $conn->query($objectsql);
?>
    
<h1>Inventory</h1>

<h2>Racks</h2>
<a href="rack_add.php">Add New Rack</a>
<ul>
    <?php while ($rack = $rackresult->fetch_assoc()): ?>
        <li>
            <strong>Name:</strong> <?= htmlspecialchars($rack['name']) ?><br>

            <div class="buttons">
                <a href="rack_edit.php?id=<?= $rack['id'] ?>">Edit</a>
                <a href="rack_delete.php?id=<?= $rack['id'] ?>" onclick="return confirm('Delete this object?');">Delete</a>
            </div>
        </li>
    <?php endwhile; ?>
</ul>

<h2>Shelves</h2>
<a href="shelf_add.php">Add New Shelf</a>
<ul>
    <?php while ($shelf = $shelfresult->fetch_assoc()): ?>
        <li>
            <strong>Number:</strong><?=htmlspecialchars($shelf['rack_name'])?>.<?= htmlspecialchars($shelf['number']) ?><br>

            <div class="buttons">
                <a href="shelf_edit.php?id=<?= $shelf['id'] ?>">Edit</a>
                <a href="shelf_delete.php?id=<?= $shelf['id'] ?>" onclick="return confirm('Delete this object?');">Delete</a>
            </div>
        </li>
    <?php endwhile; ?>
</ul>

<h2>Objects</h2>
<a href="object_add.php">Add New Object</a>
<ul>
    <?php while ($object = $objectresult->fetch_assoc()): ?>
        <li>
            <strong>Name:</strong> <?= htmlspecialchars($object['name']) ?><br>
            <strong>Object Number:</strong> <?= htmlspecialchars($object['object_number']) ?><br>
            <strong>Quantity:</strong> <?= $object['quantity'] ?><br>
            <strong>Shelf:</strong><?= htmlspecialchars($object['rack_name'])?>.<?= htmlspecialchars($object['shelf_number'])?><br>
            <strong>Description:</strong> <?= nl2br(htmlspecialchars($object['description'])) ?><br>
            <strong>Defects:</strong> <?= nl2br(htmlspecialchars($object['defects'])) ?><br>

            <?php
                $image_path = "upload/" . $object['id'] . ".jpg";
                if (file_exists($image_path)): ?>
                    <img src="<?= $image_path ?>" alt="Image" style="max-height: 100px;">
            <?php endif; ?>

            <div class="buttons">
                <a href="object_edit.php?id=<?= $object['id'] ?>">Edit</a>
                <a href="object_delete.php?id=<?= $object['id'] ?>" onclick="return confirm('Delete this object?');">Delete</a>
            </div>
        </li>
    <?php endwhile; ?>
</ul>
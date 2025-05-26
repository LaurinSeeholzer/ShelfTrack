<?php include 'db.php'; ?>
<h2>Inventory List</h2>
<a href="add.php">Add New Object</a>
<table border="1">
    <tr>
        <th>Image</th>
        <th>Shelf</th>
        <th>Quantity</th>
        <th>Object Number</th>
        <th>Name</th>
        <th>Description</th>
        <th>Defects</th>
        <th>Actions</th>
    </tr>
    <?php
    $result = $conn->query("SELECT * FROM objects");
    while ($row = $result->fetch_assoc()):
    ?>
    <tr>
        <td>
            <?php if ($row['image_path']): ?>
                <img src="<?= $row['image_path'] ?>" width="50">
            <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($row['shelf']) ?></td>
        <td><?= $row['quantity'] ?></td>
        <td><?= htmlspecialchars($row['object_number']) ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
        <td><?= nl2br(htmlspecialchars($row['defects'])) ?></td>
        <td>
            <a href="edit.php?id=<?= $row['id'] ?>">Edit</a> |
            <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this item?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

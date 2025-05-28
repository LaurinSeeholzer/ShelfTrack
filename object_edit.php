<?php require_once 'db.php';

    $id = intval($_GET['id']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $shelf_id = intval($_POST['shelf_id']);
        $quantity = intval($_POST['quantity']);
        $object_number = trim($_POST['object_number']);
        $name = $_POST['name'];
        $description = $_POST['description'];
        $defects = $_POST['defects'];

        $shelf_check = $conn->prepare("SELECT id FROM shelves WHERE id = ?");
        $shelf_check->bind_param("i", $shelf_id);
        $shelf_check->execute();
        $shelf_check->store_result();

        if ($shelf_check->num_rows === 0) {
            $error = "The given shelf does not exist";
        }
        $shelf_check->close();

        $check = $conn->prepare("SELECT id FROM objects WHERE object_number= ? AND id != ?");
        $check->bind_param("si", $object_number, $id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "This object already exists";
        }
        $check->close();

        if (!isset($error)) {
            $stmt = $conn->prepare("UPDATE objects SET shelf_id=?, quantity=?, object_number=?, name=?, description=?, defects=? WHERE id=?");
            $stmt->bind_param("iissssi", $shelf_id, $quantity, $object_number, $name, $description, $defects, $id);
            $stmt->execute();
            $stmt->close();

            if (!empty($_FILES['image']['tmp_name'])) {
                $upload_path = __DIR__ . "/upload/" . $id . ".jpg";
                move_uploaded_file($_FILES['image']['tmp_name'], $upload_path);
            } else {
                $error = "Object was stored without Image";
            }
                    
            header("Location: index.php");
            exit;
        }
    }

    $stmt = $conn->prepare("SELECT * FROM objects WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $object = $result->fetch_assoc();

    $shelfsql =  "SELECT shelves.id, shelves.number, racks.name AS rack_name
            FROM shelves
            LEFT JOIN racks ON shelves.rack_id = racks.id
            ORDER BY racks.name ASC, shelves.number + 0 ASC";

    $shelves = $conn->query($shelfsql);

?>


<!DOCTYPE html>
<html class="h-full bg-gray-100">

<head>
    <title>TRack</title>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>  

</head>

<body class="h-full">

    <?php if (isset($error)): ?>
        <div class="rounded-md bg-red-50 p-4 z-10 top-4 right-4">
        <div class="flex">
            <div class="shrink-0">
            <svg class="size-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd" />
            </svg>
            </div>
            <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">
                <?= htmlspecialchars($error) ?>
            </h3>
            </div>
        </div>
        </div>
    <?php endif; ?>

    <div class="min-h-full">
    <div class="bg-gray-900 pb-32">
        <nav class="bg-gray-900">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="border-b border-gray-700">
            <div class="flex h-16 items-center justify-between px-4 sm:px-0">
                <div class="flex items-center">
                <div class="shrink-0">
                    <h1 class="font-normal text-white text-4xl">T<strong class="font-extrabold">Rack</strong></h1>
                </div>
                </div>
            </div>
            </div>
        </div>
        </nav>
        <header class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-white">EDIT <?= htmlspecialchars($object['object_number']) ?></h1>
        </div>
        </header>
    </div>

    <main class="-mt-32">
        <div class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
        <div class="rounded-lg bg-white px-5 p-4 shadow sm:px-6">

            <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="">
                    <div>
                        <label for="name" class="block text-sm/6 font-medium text-gray-900">Name</label>
                        <div class="mt-2">
                            <input value="<?= htmlspecialchars($object['name'])?>" type="text" name="name" id="name" class="block w-full px-3 py-1.5 rounded-md bg-gray-50 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-200 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-gray-600 sm:text-sm/6" placeholder="Your new object">
                        </div>
                    </div>
                </div>
                <div class="flex gap-x-4">
                    <div class="grow">
                        <label for="object_number" class="block text-sm/6 font-medium text-gray-900">Object Number</label>
                        <div class="mt-2">
                            <input value="<?= htmlspecialchars($object['object_number'])?>" type="text" name="object_number" id="object_number" class="block w-full px-3 py-1.5 rounded-md bg-gray-50 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-200 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-gray-600 sm:text-sm/6" placeholder="0000.0000">
                        </div>
                    </div>
                    <div class="flex-none min-w-16">
                    <label for="shelf_id" class="block text-sm/6 font-medium text-gray-900">Shelf</label>
                    <div class="mt-2 grid grid-cols-1">
                        <select id="shelf_id" name="shelf_id" class="col-start-1 row-start-1 w-full appearance-none block w-full px-3 py-1.5 rounded-md bg-gray-50 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-200 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-gray-600 sm:text-sm/6">
                            <?php while ($shelf = $shelves->fetch_assoc()): ?>
                                <option value="<?= $shelf['id'] ?>" <?php if ($shelf['id'] == $object['shelf_id']): ?>selected<?php endif; ?>><?= htmlspecialchars($shelf['rack_name']) ?>.<?= htmlspecialchars($shelf['number']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <svg class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" data-slot="icon">
                        <path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    </div>
                </div>
                
                <div class="sm:col-span-2">
                    <div>
                        <label for="description" class="block text-sm/6 font-medium text-gray-900">Description</label>
                        <div class="mt-2">
                            <textarea rows="4" name="description" id="description" class="block w-full px-3 py-1.5 rounded-md bg-gray-50 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-200 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-gray-600 sm:text-sm/6"><?= htmlspecialchars($object['description'])?></textarea>
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <div>
                        <label for="defects" class="block text-sm/6 font-medium text-gray-900">Defects</label>
                        <div class="mt-2">
                            <textarea rows="4" name="defects" id="defects" class="block w-full px-3 py-1.5 rounded-md bg-gray-50 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-200 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-gray-600 sm:text-sm/6"><?= htmlspecialchars($object['defects'])?></textarea>
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <input id="fileInput" name="image" type="file" class="hidden">
                    <button id="previewContainer" onclick="document.getElementById('fileInput').click();" 
                        type="button" class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        <svg class="size-12 text-gray-400 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        <span class="mt-2 block text-sm font-semibold text-gray-900">Upload Image</span>
                    </button>
                    <script>
                        const fileInput = document.getElementById('fileInput');
                        const previewContainer = document.getElementById('previewContainer');

                        fileInput.addEventListener('change', function () {
                            const file = fileInput.files[0];
                            if (!file) return;

                            const reader = new FileReader();
                            reader.onload = function (e) {
                            previewContainer.innerHTML = `
                                <img src="${e.target.result}" class="mx-auto h-32 w-32 object-cover rounded-md" alt="Uploaded image preview">
                            `;
                            };
                            reader.readAsDataURL(file);
                        });
                    </script>
                </div>

                <div class="">
                    <div>
                        <div class="gap-x-4 w-full flex">
                            <div class="w-full">
                                <label for="quantity" class="h-6 block text-sm/6 font-medium text-gray-900">Quantity</label>
                                <div class="mt-2 w-full">
                                    <input type="number" name="quantity" id="quantity" class="h-8 w-full px-3 py-1.5 rounded-md bg-gray-50 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-200 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-gray-600 sm:text-sm/6" value="<?= htmlspecialchars($object['quantity'])?>">
                                </div>
                            </div>
                            <button type="button" onclick="document.getElementById('quantity').value = parseInt(document.getElementById('quantity').value || 0) + 1" class="mt-8 aspect-square h-8 inline-flex items-center rounded-full bg-gray-900 p-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                            <button type="button" onclick="document.getElementById('quantity').value = parseInt(document.getElementById('quantity').value || 0) - 1" class="mt-8 aspect-square h-8 inline-flex items-center rounded-full bg-gray-900 p-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="">
                    <button type="submit" class="mt-8 sm:h-8 w-full text-center justify-center sm:w-auto sm:float-right inline-flex items-center rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900">Update</a>
                </div>

            </form>

        </div>
        </div>
    </main>
    </div>
</body>
</html>
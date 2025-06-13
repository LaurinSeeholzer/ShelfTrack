<?php require_once 'db.php';

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

        $check = $conn->prepare("SELECT id FROM shelves WHERE rack_id = ? AND number = ?");
        $check->bind_param("ii", $rack_id, $number);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "This shelf already exists";
        }
        $check->close();

        if (!isset($error)) {
            
            $stmt = $conn->prepare("INSERT INTO shelves (rack_id, number) VALUES (?, ?)");
            $stmt->bind_param("ii", $rack_id, $number);
            $stmt->execute();
            $stmt->close();

            header("Location: index.php");
            exit;
        }

    }

    $racks = $conn->query("SELECT id, name FROM racks");

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
    <div class="bg-gray-900 pb-32 pt-12">
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
            <h1 class="text-3xl font-bold tracking-tight text-white">ADD NEW SHELF</h1>
        </div>
        </header>
    </div>

    <main class="-mt-32">
        <div class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
        <div class="rounded-lg bg-white px-5 p-4 shadow sm:px-6">

            <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 gap-4">

                <div class="flex gap-x-4">
                    <div class="grow">
                        <label for="number" class="block text-sm/6 font-medium text-gray-900">Number</label>
                        <div class="mt-2">
                            <input type="number" name="number" id="umber" class="block w-full px-3 py-1.5 rounded-md bg-gray-50 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-200 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-gray-600 sm:text-sm/6" placeholder="0">
                        </div>
                    </div>
                    <div class="flex-none min-w-16">
                        <label for="rack_id" class="block text-sm/6 font-medium text-gray-900">Rack</label>
                        <div class="mt-2 grid grid-cols-1">
                            <select id="rack_id" name="rack_id" class="col-start-1 row-start-1 w-full appearance-none block w-full px-3 py-1.5 rounded-md bg-gray-50 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-200 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-gray-600 sm:text-sm/6">
                                <?php while ($rack = $racks->fetch_assoc()): ?>
                                    <option value="<?= $rack['id'] ?>"><?= htmlspecialchars($rack['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="flex-none">
                        <button type="submit" class="mt-8 text-center justify-center inline-flex items-center rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900">Add</a>
                    </div>
                </div>

            </form>

        </div>
        </div>
    </main>
    </div>
</body>
</html>
<?php require_once 'db.php';

    $currentRackId = $_GET['rack_id'] ?? null;
    $currentShelfId = $_GET['shelf_id'] ?? null;

    $racks = [];
    $shelves = [];
    $objects = [];

        
    $rackresult = $conn->query("SELECT racks.id, racks.name FROM racks ORDER BY racks.name ASC");
    while ($row = $rackresult->fetch_assoc()) {
        $racks[] = $row;
    }

    if ($currentRackId) {
        $shelfsql = $conn->prepare("SELECT * FROM shelves WHERE rack_id = ? ORDER BY shelves.number + 0 ASC");
        $shelfsql->bind_param("i", $currentRackId);
        $shelfsql->execute();
        $shelfresult = $shelfsql->get_result();

        while ($row = $shelfresult->fetch_assoc()) {
            $shelves[] = $row;
        }

        if ($currentShelfId) {
            $objectsql = $conn->prepare("SELECT * FROM objects WHERE shelf_id = ? ORDER BY objects.object_number ASC");
            $objectsql->bind_param("i", $currentShelfId);
            $objectsql->execute();
            $objectresult = $objectsql->get_result();

            while ($row = $objectresult->fetch_assoc()) {
                $objects[] = $row;
            }
        }
    }
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

<div class="min-h-full">
  <div class="bg-gray-800 pb-32">
    <nav class="bg-gray-800">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="border-b border-gray-700">
          <div class="flex h-16 items-center justify-between px-4 sm:px-0">
            <div class="flex items-center">
              <div class="shrink-0">
                <img class="size-8" src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=gray&shade=100" alt="Your Company">
              </div>
              <div class="hidden sm:block">
                <div class="ml-10 flex items-baseline space-x-4">
                    <?php foreach ($racks as $rack): ?>
                        <?php
                            $isCurrent = $currentRackId == $rack['id'];
                            $classes = $isCurrent
                                ? 'rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white'
                                : 'rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white';
                        ?>
                        <a href="?rack_id=<?= $rack['id'] ?>" class="<?= $classes ?>">
                            <?= htmlspecialchars($rack['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="border-b border-gray-700 sm:hidden" id="mobile-menu">
        <div class="space-y-1 px-2 py-3 sm:px-3">
            <?php foreach ($racks as $rack): ?>
                <?php
                    $isCurrent = $currentRackId == $rack['id'];
                    $classes = $isCurrent
                        ? 'block rounded-md bg-gray-900 px-3 py-2 text-base font-medium text-white'
                        : 'block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white';
                ?>
                <a href="?rack_id=<?= $rack['id'] ?>" class="<?= $classes ?>">
                    <?= htmlspecialchars($rack['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
      </div>
    </nav>
    <header class="py-10">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-white">RACK A</h1>
      </div>
    </header>
  </div>

  <main class="-mt-32">
    <div class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
      <div class="rounded-lg bg-white px-5 py-6 shadow sm:px-6">

        <?php if (!empty($shelves)): ?>
        <div>
        <div class="grid grid-cols-1 sm:hidden">
            <!-- Use an "onChange" listener to redirect the user to the selected tab URL. -->
            <select aria-label="Select a tab" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-2 pl-3 pr-8 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                <?php foreach ($shelves as $shelf): ?>
                    <option value="<?= $shelf['id'] ?>"><?= htmlspecialchars($shelf['number']) ?></option>
                <?php endforeach; ?>
            </select>
            <svg class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end fill-gray-500" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" data-slot="icon">
            <path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="hidden sm:block">
            <nav class="flex space-x-4" aria-label="Tabs">
                <?php foreach ($shelves as $shelf): ?>
                <a href="?rack_id=<?= $currentRackId ?>&shelf_id=<?= $shelf['id'] ?>"
                    class="rounded-md px-3 py-2 text-sm font-medium
                    <?php
                        $isCurrent = $currentShelfId == $rshelf['id'];
                        $classes = $isCurrent
                            ? 'bg-gray-100 text-gray-700' 
                            : 'text-gray-500 hover:text-gray-700' ?>">
                    <?= htmlspecialchars($shelf['number']) ?>
                </a>
                <?php endforeach; ?>
            </nav>
        </div>
        </div>
        <?php endif; ?>
        

        <?php foreach ($objects as $object): ?>
            <?= htmlspecialchars($object['object_number']) ?><?= htmlspecialchars($object["name"]) ?><br>
        <?php endforeach; ?>

      </div>
    </div>
  </main>
</div>

</body>

</html>
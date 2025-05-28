<?php require_once 'db.php';

    $currentRackId = $_GET['rack_id'] ?? null;
    $currentShelfId = $_GET['shelf_id'] ?? null;

    $racks = [];
    $shelves = [];
    $objects = [];

        
    $rackresult = $conn->query("SELECT racks.id, racks.name FROM racks ORDER BY racks.name ASC");
    while ($row = $rackresult->fetch_assoc()) {
        if (intval($row['id']) === intval($currentRackId)) {
          $currentRack = $row;
        }
        $racks[] = $row;
    }

    if ($currentRackId) {
        $shelfsql = $conn->prepare("SELECT * FROM shelves WHERE rack_id = ? ORDER BY shelves.number + 0 ASC");
        $shelfsql->bind_param("i", $currentRackId);
        $shelfsql->execute();
        $shelfresult = $shelfsql->get_result();

        while ($row = $shelfresult->fetch_assoc()) {
            if (intval($row['id']) === intval($currentShelfId)) {
              $currentShelf = $row;
            }
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

<div id="overlay" class="fixed top-0 w-full h-full z-15 bg-[rgba(0,0,0,0.5)] hidden" onclick="document.getElementById('overlaybutton').click()"></div>

<div class="fixed top-0 w-full h-full z-15 pointer-events-none">
  <div class="relative max-w-7xl px-4 mx-auto h-full">
    <div class="absolute z-20 bottom-4 right-4">
      <div class="w-32 grid grid-cols-1 gap-y-4 hidden pointer-events-auto " id="addbuttons">
        <a href="/rack_add.php" class="relative inline-flex items-center rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900">RACK</a>
        <a href="/shelf_add.php" class="relative inline-flex items-center rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900">SHELF</a>
        <a href="/object_add.php" class="relative inline-flex items-center rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900">OBJECT</a>
      </div>
      <div id="overlaybutton" onclick="document.getElementById('addbuttons').classList.toggle('hidden');document.getElementById('overlay').classList.toggle('hidden')" class="pointer-events-auto hover:bg-gray-800 rounded-2xl float-right mt-4 h-16 aspect-square inline-flex items-center text-white bg-gray-900">
        <svg class="w-12 aspect-square mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
      </div>
    </div>
  </div>
</div>

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
        <?php if($currentShelf && $currentRack):?>
          <h1 class="text-3xl font-bold tracking-tight text-white">SHELF <?= htmlspecialchars($currentRack['name']); ?>.<?= htmlspecialchars($currentShelf['number']); ?></h1>
        <?php endif; ?>
        <?php if(!$currentShelf && $currentRack):?>
          <h1 class="text-3xl font-bold tracking-tight text-white">RACK <?= htmlspecialchars($currentRack['name']); ?></h1>
        <?php endif; ?>
        <?php if(!$currentRack):?>
          <h1 class="text-3xl font-bold tracking-tight text-white">INVENTORY</h1>
        <?php endif; ?>
      </div>
    </header>
  </div>

  <main class="-mt-32">
    <div class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
      <div class="rounded-lg bg-white px-5 py-6 shadow sm:px-6">

            <div class="flex pb-4 gap-x-2">
              <?php foreach ($racks as $rack): ?>
                <?php 
                  $isCurrent = intval($currentRackId) == intval($rack['id']);
                  $classes = $isCurrent
                    ? 'flex-none rounded-md px-3 py-2 text-sm font-medium bg-gray-100 text-gray-900 border border-gray-200'
                    : 'flex-none rounded-md px-3 py-2 text-sm font-medium text-gray-900 hover:text-gray-800 border border-gray-200'
                ?>
                <a href="?rack_id=<?= $rack['id'] ?>" class="<?= $classes ?>">
                  <?= htmlspecialchars($rack['name']) ?>
                </a>
              <?php endforeach; ?>
            </div>

          <?php if ($currentRack): ?>
              <div class="border border-gray-200 bg-gray-100 p-4 rounded-lg grid grid-cols-1 gap-y-4 my-4">
                <div class="flex flex-wrap items-center justify-between sm:flex-nowrap">
                  <div class="">
                    <h3 class="text-base font-semibold text-gray-900">RACK <?= htmlspecialchars($currentRack['name']) ?></h3>
                  </div>
                    <div class="shrink-0">
                      <a href="/rack_edit.php?id=<?= htmlspecialchars($currentRack['id'])?>" class="relative inline-flex items-center rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900">Edit</a>
                      <a href="/rack_delete.php?id=<?= htmlspecialchars($currentRack['id'])?>" class="relative inline-flex items-center rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900">Delete</a>
                    </div>
                </div>
                <div class="w-full py-4">
                  <div class="grid grid-cols-1 gap-y-2 w-full">

                    <?php foreach($shelves as $shelf): ?>
                      <?php 
                        $isCurrent = intval($currentShelfId) == intval($shelf['id']);
                        $classes = $isCurrent
                          ? 'bg-white rounded-md pl-4 w-full flex border-gray-200 border'
                          : 'bg-gray-50 rounded-md pl-4 w-full flex border-gray-200 border'
                      ?>
                      <div class="<?= $classes ?>">
                        <div onclick="window.location='?rack_id=<?= $currentRackId ?>&shelf_id=<?= $shelf['id'] ?>'"  class="grow whitespace-nowrap py-4 text-sm font-medium text-gray-900 sm:pl-0"><?= htmlspecialchars($currentRack['name']); ?>.<?= htmlspecialchars($shelf['number']); ?></div>
                        <div class="grid grid-cols-2">
                          <div onclick="window.location='/shelf_edit.php?id=<?=htmlspecialchars($shelf['id'])?>'" class="items-center inline-flex px-4 border-l border-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                              <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                            </svg>
                          </div>
                          <div onclick="window.location='/shelf_delete.php?id=<?=htmlspecialchars($shelf['id'])?>'" class="items-center inline-flex px-4 border-l border-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                              <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                          </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                  </div>
                </div>
              </div>

              <?php if($currentShelf): ?>
                <div class="grid grid-cols-1 gap-y-4">
                    <?php foreach($objects as $object): ?>
                      <div class="relative w-full border border-gray-200 rounded-md sm:h-40">
                        <span class="absolute top-2 z-10 left-2 inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600"><?= htmlspecialchars($object['quantity'])?></span>
                        <div class="flex w-full">
                          <img src="upload/<?= $object['id']?>.jpg" class="flex-none rounded-md overflow-hidden h-32 m-4 aspect-square cover border border-gray-200">
                          <div class="grow grid sm:grid-cols-2 p-4 h-40 w-full">
                            <div class="hidden sm:block">
                              <h5 class="font-semibold text-gray-500 h-6">
                                <?= htmlspecialchars($object['object_number'])?>
                              </h5>
                              <h5 class="font-semibold text-gray-900 h-6">
                                <?= htmlspecialchars($object['name'])?>
                              </h5>
                              <p class="h-20 overflow-hidden">
                                <?= htmlspecialchars($object['description'])?>
                              </p>
                            </div>
                            <?php if ($object['defects'] != ''): ?>
                              <div class="h-32 overflow-hidden ml-4 bg-red-100 p-2 grid grid-cols-1 gap-y-2 rounded-md border border-red-200">
                                <p class="text-red-900">
                                  <?= htmlspecialchars($object['defects']) ?>
                                </p>
                              </div>
                            <?php endif; ?>
                          </div>
                          <div class="grid grid-rows-2 flex-none divide-y divide-gray-200 border-l border-gray-200 rounded-r-md h-40">
                            <a href="/object_edit.php?id=<?= htmlspecialchars($object['id'])?>" class="h-full w-12 px-3 relative">
                              <svg class="w-6 h-6 mx-auto absolute top-[50%] -translate-y-[50%]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                              </svg>
                            </a>
                            <a href="/object_delete.php?id=<?= htmlspecialchars($object['id'])?>" class="h-full w-12 px-3 relative">
                              <svg class="w-6 h-6 mx-auto absolute top-[50%] -translate-y-[50%]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                              </svg>
                            </a>
                          </div>
                        </div>
                        <div class="block sm:hidden p-4 border-t border-gray-200">
                          <h5 class="font-semibold text-gray-500 h-6">
                            <?= htmlspecialchars($object['object_number'])?>
                          </h5>
                          <h5 class="font-semibold text-gray-900 h-6">
                            <?= htmlspecialchars($object['name'])?>
                          </h5>
                          <p class="sm:h-20 overflow-hidden">
                            <?= htmlspecialchars($object['description'])?>
                          </p>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
              <?php endif; ?>

          <?php endif; ?>

          <?php if (!$currentRack): ?>

            <?php foreach($racks as $rack): ?>
                <div onclick="window.location = '/?rack_id=<?= htmlspecialchars($rack['id'])?>'" class="border border-gray-200 bg-gray-100 p-4 rounded-lg grid grid-cols-1 gap-y-4 my-4">
                  <div class="flex flex-wrap items-center justify-between sm:flex-nowrap">
                    <div class="">
                      <h3 class="text-base font-semibold text-gray-900">RACK <?= htmlspecialchars($rack['name']) ?></h3>
                    </div>
                    <div class="shrink-0">
                      <a href="/rack_edit.php?id=<?= htmlspecialchars($rack['id'])?>" class="relative inline-flex items-center rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900">Edit</a>
                      <a href="/rack_delete.php?id=<?= htmlspecialchars($rack['id'])?>" class="relative inline-flex items-center rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900">Delete</a>
                    </div>
                  </div>
                </div>
            <?php endforeach; ?>
            
          <?php endif; ?>

      </div>
    </div>
  </main>
</div>

</body>

</html>
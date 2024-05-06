<?php
require 'views/partials/head.php';
require 'views/partials/nav.php';
require 'views/partials/banner.php';
?>

<style>
    .note-link {
        transition: background-color 0.3s ease-in-out;
    }
    .note-item:hover {
        background-color: #f0f0f0;
    }
</style>

<main>
  <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
    <ul class="list-disc pl-5">
      <?php foreach ($notes as $note): ?>
        <li class="mb-2 note-item">
          <a href="/note?id=<?= $note['id'] ?>" class="text-blue-500 hover:underline note-link"><?= htmlspecialchars($note['body']) ?></a>
        </li>
      <?php endforeach; ?>
    </ul>

    <!-- Align the button with the list -->
    <div class="mt-4">
      <a href="/notes/create" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
        Create Note
      </a>
    </div>
  </div>
</main>



<?php
require 'views/partials/footer.php';
?>


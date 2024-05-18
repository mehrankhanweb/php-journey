<?php
require base_path('views/partials/head.php');
require base_path('views/partials/nav.php');
require base_path('views/partials/banner.php');
?>

<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
    <p class="mb-6">
    <a href="/notes" class="text-blue-500 underline">Go Back...</a>
    </p>
    <p>
    <?= htmlspecialchars($note['body']) ?>
    </p>
    </div>

<div class="mt-8">
            <form method="POST">
                <input type="hidden" name="id" value="<?= $note['id'] ?>">
                <button class="mx-40 px-2 py-2 text-xs font-small text-white bg-red-600 rounded-lg focus:ring-4 focus:ring-red-200 hover:bg-red-700">
                    Delete Note
                </button>
            </form>
        </div>
</main>

<?php
require base_path('views/partials/footer.php');

?>


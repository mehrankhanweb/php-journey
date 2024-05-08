<?php
require base_path('views/partials/head.php');
require base_path('views/partials/nav.php');
require base_path('views/partials/banner.php');
?>

<main class="flex flex-col items-center w-full pt-10 bg-gray-100 dark:bg-gray-900">
    <h1 class="text-3xl font-medium dark:text-white mb-6">Create New Note</h1>
    <form class="w-full max-w-lg px-4 py-8 bg-white dark:bg-gray-800 rounded-lg shadow" method="post">
        <label for="message" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Your message</label>
        <textarea id="message" name="body" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Write your thoughts here..."><?= isset($_POST['body']) ? $_POST['body'] : '' ?>
</textarea>
        <?php if (isset($errors['body'])) : ?>
            <p class="text-red-500 text-xs mt-2"><?= $errors['body'] ?></p>
        <?php endif; ?>
        <button type="submit" class="mt-4 w-full px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-700 rounded-lg focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 hover:bg-blue-800">
            Publish Note
        </button>
    </form>
</main>

<?php
require base_path('views/partials/footer.php');
?>
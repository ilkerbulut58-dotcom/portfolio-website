<footer class="bg-white dark:bg-gray-800 shadow-lg mt-12">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Über uns</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    Ein professionelles Blog-System mit modernem Design und leistungsstarken Funktionen.
                </p>
            </div>
            
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Kategorien</h3>
                <ul class="space-y-2">
                    <?php
                    global $conn;
                    if (!isset($conn)) {
                        require_once __DIR__ . '/../config/database.php';
                        $database = new Database();
                        $conn = $database->getConnection();
                    }
                    $footer_categories = $conn->query("SELECT * FROM blog_categories ORDER BY name LIMIT 5")->fetchAll();
                    foreach ($footer_categories as $cat):
                    ?>
                    <li>
                        <a href="/?category=<?php echo urlencode($cat['slug']); ?>" 
                           class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Links</h3>
                <ul class="space-y-2">
                    <li><a href="/" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">Startseite</a></li>
                    <li><a href="/admin/" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">Admin-Panel</a></li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-200 dark:border-gray-700 mt-8 pt-8 text-center">
            <p class="text-gray-600 dark:text-gray-300">
                &copy; <?php echo date('Y'); ?> My Blog. Alle Rechte vorbehalten.
            </p>
        </div>
    </div>
</footer>

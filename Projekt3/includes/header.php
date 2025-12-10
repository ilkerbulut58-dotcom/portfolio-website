<header class="bg-white dark:bg-gray-800 shadow-lg">
    <nav class="max-w-7xl mx-auto px-4 py-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-8">
                <a href="/" class="text-2xl font-bold text-blue-600 dark:text-blue-400">My Blog</a>
                <div class="hidden md:flex space-x-6">
                    <a href="/" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400">Startseite</a>
                    
                    <div class="relative group">
                        <button class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 flex items-center">
                            Kategorien
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute hidden group-hover:block bg-white dark:bg-gray-700 shadow-lg rounded-lg mt-2 py-2 w-48 z-10">
                            <?php
                            global $conn;
                            if (!isset($conn)) {
                                require_once __DIR__ . '/../config/database.php';
                                $database = new Database();
                                $conn = $database->getConnection();
                            }
                            $nav_categories = $conn->query("SELECT * FROM blog_categories ORDER BY name")->fetchAll();
                            foreach ($nav_categories as $cat):
                            ?>
                            <a href="/?category=<?php echo urlencode($cat['slug']); ?>" 
                               class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <form method="GET" action="/" class="hidden md:block">
                    <input type="text" name="search" placeholder="Suchen..." 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                           class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500">
                </form>
                
                <button id="themeToggle" class="p-2 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600">
                    <svg id="themeIcon" class="w-6 h-6 text-gray-800 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>
                
                <a href="/admin/" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </a>
            </div>
        </div>
    </nav>
</header>

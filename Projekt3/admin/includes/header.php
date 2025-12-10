<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-8">
                <a href="/admin/" class="text-2xl font-bold text-blue-600">Blog Admin</a>
                <div class="hidden md:flex space-x-4">
                    <a href="/admin/" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md">Dashboard</a>
                    <a href="/admin/posts/" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md">Posts</a>
                    <a href="/admin/categories/" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md">Kategorien</a>
                    <a href="/admin/comments/" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md">Kommentare</a>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <a href="/" target="_blank" class="text-gray-700 hover:text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
                <span class="text-gray-700">Hallo, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="/admin/logout.php" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">Abmelden</a>
            </div>
        </div>
    </div>
</nav>

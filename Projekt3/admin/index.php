<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../includes/functions.php';

Security::requireLogin();

$database = new Database();
$conn = $database->getConnection();

$stats_posts = $conn->query("SELECT COUNT(*) as count FROM blog_posts")->fetch()['count'];
$stats_published = $conn->query("SELECT COUNT(*) as count FROM blog_posts WHERE status = 'published'")->fetch()['count'];
$stats_categories = $conn->query("SELECT COUNT(*) as count FROM blog_categories")->fetch()['count'];
$stats_comments = $conn->query("SELECT COUNT(*) as count FROM blog_comments")->fetch()['count'];
$stats_pending_comments = $conn->query("SELECT COUNT(*) as count FROM blog_comments WHERE status = 'pending'")->fetch()['count'];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Blog Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/includes/header.php'; ?>
    
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Dashboard</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Alle Posts</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $stats_posts; ?></p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Veröffentlicht</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $stats_published; ?></p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Kategorien</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $stats_categories; ?></p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Kommentare</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $stats_comments; ?></p>
                        <?php if ($stats_pending_comments > 0): ?>
                        <p class="text-sm text-orange-600 mt-1"><?php echo $stats_pending_comments; ?> warten</p>
                        <?php endif; ?>
                    </div>
                    <div class="bg-orange-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Neueste Posts</h2>
            <?php
            $stmt = $conn->query("SELECT p.*, c.name as category_name, u.full_name as author_name 
                                   FROM blog_posts p 
                                   LEFT JOIN blog_categories c ON p.category_id = c.id 
                                   LEFT JOIN blog_users u ON p.author_id = u.id 
                                   ORDER BY p.created_at DESC LIMIT 5");
            $recent_posts = $stmt->fetchAll();
            ?>
            
            <?php if (count($recent_posts) > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-3 px-4">Titel</th>
                            <th class="text-left py-3 px-4">Kategorie</th>
                            <th class="text-left py-3 px-4">Status</th>
                            <th class="text-left py-3 px-4">Datum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_posts as $post): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <a href="/admin/posts/edit.php?id=<?php echo $post['id']; ?>" class="text-blue-600 hover:underline">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($post['category_name'] ?? 'Keine'); ?></td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 text-xs rounded <?php echo $post['status'] === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                    <?php echo $post['status'] === 'published' ? 'Veröffentlicht' : 'Entwurf'; ?>
                                </span>
                            </td>
                            <td class="py-3 px-4"><?php echo formatDate($post['created_at']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-gray-500 text-center py-8">Noch keine Posts vorhanden. <a href="/admin/posts/create.php" class="text-blue-600 hover:underline">Ersten Post erstellen</a></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

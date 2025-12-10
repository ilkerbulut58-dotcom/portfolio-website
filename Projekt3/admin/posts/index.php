<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/functions.php';

Security::requireLogin();

$database = new Database();
$conn = $database->getConnection();

$search = isset($_GET['search']) ? Security::cleanInput($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$sql = "SELECT p.*, c.name as category_name, u.full_name as author_name 
        FROM blog_posts p 
        LEFT JOIN blog_categories c ON p.category_id = c.id 
        LEFT JOIN blog_users u ON p.author_id = u.id 
        WHERE 1=1";

if ($search) {
    $sql .= " AND (p.title LIKE :search OR p.content LIKE :search)";
}
if ($category_filter > 0) {
    $sql .= " AND p.category_id = :category";
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);

if ($search) {
    $stmt->bindValue(':search', '%' . $search . '%');
}
if ($category_filter > 0) {
    $stmt->bindValue(':category', $category_filter);
}

$stmt->execute();
$posts = $stmt->fetchAll();

$categories = $conn->query("SELECT * FROM blog_categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts - Blog Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Blog Posts</h1>
            <a href="/admin/posts/create.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                + Neuer Post
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" class="flex gap-4">
                <input type="text" name="search" placeholder="Suchen..." value="<?php echo htmlspecialchars($search); ?>"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                
                <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="0">Alle Kategorien</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $category_filter == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Filtern
                </button>
                
                <?php if ($search || $category_filter): ?>
                <a href="/admin/posts/" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                    Zurücksetzen
                </a>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <?php if (count($posts) > 0): ?>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-3 px-4">Titel</th>
                        <th class="text-left py-3 px-4">Kategorie</th>
                        <th class="text-left py-3 px-4">Autor</th>
                        <th class="text-left py-3 px-4">Status</th>
                        <th class="text-left py-3 px-4">Aufrufe</th>
                        <th class="text-left py-3 px-4">Datum</th>
                        <th class="text-left py-3 px-4">Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">
                            <div class="font-semibold"><?php echo htmlspecialchars($post['title']); ?></div>
                            <?php if ($post['excerpt']): ?>
                            <div class="text-sm text-gray-500"><?php echo truncateText(htmlspecialchars($post['excerpt']), 60); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($post['category_name'] ?? 'Keine'); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($post['author_name']); ?></td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs rounded <?php echo $post['status'] === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                <?php echo $post['status'] === 'published' ? 'Veröffentlicht' : 'Entwurf'; ?>
                            </span>
                        </td>
                        <td class="py-3 px-4"><?php echo $post['views']; ?></td>
                        <td class="py-3 px-4"><?php echo formatDate($post['created_at']); ?></td>
                        <td class="py-3 px-4">
                            <div class="flex gap-2">
                                <a href="/admin/posts/edit.php?id=<?php echo $post['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-800">Bearbeiten</a>
                                <a href="/admin/posts/delete.php?id=<?php echo $post['id']; ?>" 
                                   onclick="return confirm('Post wirklich löschen?')"
                                   class="text-red-600 hover:text-red-800">Löschen</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="text-center py-12">
                <p class="text-gray-500 mb-4">Keine Posts gefunden.</p>
                <a href="/admin/posts/create.php" class="text-blue-600 hover:underline">Ersten Post erstellen</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$category_slug = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : '';

$sql = "SELECT p.*, c.name as category_name, c.slug as category_slug, u.full_name as author_name 
        FROM blog_posts p 
        LEFT JOIN blog_categories c ON p.category_id = c.id 
        LEFT JOIN blog_users u ON p.author_id = u.id 
        WHERE p.status = 'published'";

if ($search) {
    $sql .= " AND (p.title LIKE :search OR p.content LIKE :search OR p.excerpt LIKE :search)";
}
if ($category_slug) {
    $sql .= " AND c.slug = :category";
}

$sql .= " ORDER BY p.published_at DESC LIMIT 12";

$stmt = $conn->prepare($sql);

if ($search) {
    $stmt->bindValue(':search', '%' . $search . '%');
}
if ($category_slug) {
    $stmt->bindValue(':category', $category_slug);
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
    <title>Blog - Willkommen</title>
    <meta name="description" content="Ein professionelles Blog-System">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <?php include __DIR__ . '/includes/header.php'; ?>
    
    <div class="max-w-7xl mx-auto px-4 py-8">
        <?php if ($category_slug): 
            $cat_name = '';
            foreach ($categories as $cat) {
                if ($cat['slug'] === $category_slug) {
                    $cat_name = $cat['name'];
                    break;
                }
            }
        ?>
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Kategorie: <?php echo htmlspecialchars($cat_name); ?></h2>
            <a href="/" class="text-blue-600 dark:text-blue-400 hover:underline">← Alle Posts anzeigen</a>
        </div>
        <?php endif; ?>
        
        <?php if ($search): ?>
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Suchergebnisse für: "<?php echo htmlspecialchars($search); ?>"</h2>
            <a href="/" class="text-blue-600 dark:text-blue-400 hover:underline">← Alle Posts anzeigen</a>
        </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $post): ?>
                <article class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-200">
                    <?php if ($post['featured_image']): ?>
                    <a href="/post.php?slug=<?php echo urlencode($post['slug']); ?>">
                        <img src="/<?php echo htmlspecialchars($post['featured_image']); ?>" 
                             alt="<?php echo htmlspecialchars($post['title']); ?>"
                             class="w-full h-48 object-cover">
                    </a>
                    <?php endif; ?>
                    
                    <div class="p-6">
                        <?php if ($post['category_name']): ?>
                        <a href="/?category=<?php echo urlencode($post['category_slug']); ?>" 
                           class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs px-3 py-1 rounded-full mb-3">
                            <?php echo htmlspecialchars($post['category_name']); ?>
                        </a>
                        <?php endif; ?>
                        
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-2">
                            <a href="/post.php?slug=<?php echo urlencode($post['slug']); ?>" class="hover:text-blue-600 dark:hover:text-blue-400">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </h2>
                        
                        <?php if ($post['excerpt']): ?>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">
                            <?php echo truncateText(htmlspecialchars($post['excerpt']), 120); ?>
                        </p>
                        <?php endif; ?>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                            <span><?php echo formatDate($post['published_at']); ?></span>
                            <span><?php echo $post['views']; ?> Aufrufe</span>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 dark:text-gray-400 text-xl">
                        <?php echo $search ? 'Keine Posts gefunden für Ihre Suche.' : 'Noch keine Posts vorhanden.'; ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include __DIR__ . '/includes/footer.php'; ?>
    
    <script src="/assets/theme.js"></script>
</body>
</html>

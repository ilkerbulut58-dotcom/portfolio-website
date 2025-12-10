<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$slug = isset($_GET['slug']) ? htmlspecialchars($_GET['slug']) : '';

if (!$slug) {
    header('Location: /');
    exit();
}

$stmt = $conn->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug, u.full_name as author_name 
                        FROM blog_posts p 
                        LEFT JOIN blog_categories c ON p.category_id = c.id 
                        LEFT JOIN blog_users u ON p.author_id = u.id 
                        WHERE p.slug = ? AND p.status = 'published'");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: /');
    exit();
}

$conn->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = ?")->execute([$post['id']]);

$comment_success = '';
$comment_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
        $comment_error = 'Ungültiges Sicherheitstoken.';
    } else {
        $author_name = Security::cleanInput($_POST['author_name']);
        $author_email = Security::cleanInput($_POST['author_email']);
        $content = Security::cleanInput($_POST['content']);
        
        if (empty($author_name) || empty($author_email) || empty($content)) {
            $comment_error = 'Bitte füllen Sie alle Felder aus.';
        } elseif (!filter_var($author_email, FILTER_VALIDATE_EMAIL)) {
            $comment_error = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
        } else {
            $ip_address = Security::getClientIP();
            
            $stmt = $conn->prepare("INSERT INTO blog_comments (post_id, author_name, author_email, content, ip_address) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$post['id'], $author_name, $author_email, $content, $ip_address]);
            
            $comment_success = 'Vielen Dank für Ihren Kommentar! Er wird nach Prüfung veröffentlicht.';
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM blog_comments WHERE post_id = ? AND status = 'approved' ORDER BY created_at DESC");
$stmt->execute([$post['id']]);
$comments = $stmt->fetchAll();

$csrf_token = Security::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - Blog</title>
    <meta name="description" content="<?php echo htmlspecialchars($post['excerpt'] ?: truncateText(strip_tags($post['content']), 150)); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <?php include __DIR__ . '/includes/header.php'; ?>
    
    <article class="max-w-4xl mx-auto px-4 py-8">
        <?php if ($post['category_name']): ?>
        <a href="/?category=<?php echo urlencode($post['category_slug']); ?>" 
           class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm px-4 py-2 rounded-full mb-4">
            <?php echo htmlspecialchars($post['category_name']); ?>
        </a>
        <?php endif; ?>
        
        <h1 class="text-4xl md:text-5xl font-bold text-gray-800 dark:text-white mb-4">
            <?php echo htmlspecialchars($post['title']); ?>
        </h1>
        
        <div class="flex items-center gap-4 text-gray-600 dark:text-gray-400 mb-8">
            <span>Von <?php echo htmlspecialchars($post['author_name']); ?></span>
            <span>•</span>
            <span><?php echo formatDate($post['published_at']); ?></span>
            <span>•</span>
            <span><?php echo $post['views']; ?> Aufrufe</span>
        </div>
        
        <?php if ($post['featured_image']): ?>
        <img src="/<?php echo htmlspecialchars($post['featured_image']); ?>" 
             alt="<?php echo htmlspecialchars($post['title']); ?>"
             class="w-full rounded-lg shadow-lg mb-8">
        <?php endif; ?>
        
        <div class="prose prose-lg dark:prose-invert max-w-none bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg">
            <?php echo $post['content']; ?>
        </div>
        
        <div class="mt-12 bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">
                Kommentare (<?php echo count($comments); ?>)
            </h2>
            
            <?php if ($comment_success): ?>
                <?php echo showAlert($comment_success, 'success'); ?>
            <?php endif; ?>
            
            <?php if ($comment_error): ?>
                <?php echo showAlert($comment_error, 'error'); ?>
            <?php endif; ?>
            
            <form method="POST" class="mb-8">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Name *</label>
                        <input type="text" name="author_name" required
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">E-Mail *</label>
                        <input type="email" name="author_email" required
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Kommentar *</label>
                    <textarea name="content" rows="4" required
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <button type="submit" name="submit_comment" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                    Kommentar absenden
                </button>
            </form>
            
            <div class="space-y-6">
                <?php foreach ($comments as $comment): ?>
                <div class="border-l-4 border-blue-500 pl-4 py-2">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-bold text-gray-800 dark:text-white"><?php echo htmlspecialchars($comment['author_name']); ?></h4>
                        <span class="text-sm text-gray-500 dark:text-gray-400"><?php echo formatDate($comment['created_at']); ?></span>
                    </div>
                    <p class="text-gray-700 dark:text-gray-300"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                </div>
                <?php endforeach; ?>
                
                <?php if (count($comments) === 0): ?>
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">
                    Noch keine Kommentare. Seien Sie der Erste!
                </p>
                <?php endif; ?>
            </div>
        </div>
    </article>
    
    <?php include __DIR__ . '/includes/footer.php'; ?>
    
    <script src="/assets/theme.js"></script>
</body>
</html>

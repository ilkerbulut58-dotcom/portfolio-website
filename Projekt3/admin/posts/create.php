<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/functions.php';

Security::requireLogin();

$database = new Database();
$conn = $database->getConnection();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Ungültiges Sicherheitstoken.';
    } else {
        $title = Security::cleanInput($_POST['title']);
        $slug = createSlug($_POST['slug'] ?: $title);
        $content = Security::cleanHTML($_POST['content']);
        $excerpt = Security::cleanInput($_POST['excerpt']);
        $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        $status = $_POST['status'];
        $author_id = $_SESSION['user_id'];
        
        $featured_image = '';
        if (!empty($_FILES['featured_image']['name'])) {
            $upload_result = uploadImage($_FILES['featured_image']);
            if ($upload_result['success']) {
                $featured_image = $upload_result['path'];
            } else {
                $error = $upload_result['error'];
            }
        }
        
        if (!$error) {
            $stmt = $conn->prepare("SELECT id FROM blog_posts WHERE slug = ?");
            $stmt->execute([$slug]);
            if ($stmt->fetch()) {
                $slug = $slug . '-' . time();
            }
            
            $sql = "INSERT INTO blog_posts (title, slug, content, excerpt, featured_image, category_id, author_id, status, published_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $published_at = ($status === 'published') ? date('Y-m-d H:i:s') : null;
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([$title, $slug, $content, $excerpt, $featured_image, $category_id, $author_id, $status, $published_at]);
            
            $success = 'Post erfolgreich erstellt!';
            $post_id = $conn->lastInsertId();
            header("Location: /admin/posts/edit.php?id=$post_id&success=1");
            exit();
        }
    }
}

$categories = $conn->query("SELECT * FROM blog_categories ORDER BY name")->fetchAll();
$csrf_token = Security::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neuer Post - Blog Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <div class="max-w-5xl mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Neuer Blog Post</h1>
        </div>
        
        <?php if ($error): ?>
            <?php echo showAlert($error, 'error'); ?>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Titel *</label>
                    <input type="text" name="title" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">URL-Slug (leer lassen für automatisch)</label>
                    <input type="text" name="slug"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="mein-blog-post">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Inhalt *</label>
                    <textarea name="content" id="content" rows="15"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Kurzbeschreibung</label>
                    <textarea name="excerpt" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="Eine kurze Zusammenfassung des Posts"></textarea>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Post-Einstellungen</h2>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Kategorie</label>
                    <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Keine Kategorie</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Beitragsbild</label>
                    <input type="file" name="featured_image" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="draft">Entwurf</option>
                        <option value="published">Veröffentlichen</option>
                    </select>
                </div>
            </div>
            
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                    Post erstellen
                </button>
                <a href="/admin/posts/" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 font-semibold">
                    Abbrechen
                </a>
            </div>
        </form>
    </div>
    
    <script>
        tinymce.init({
            selector: '#content',
            height: 500,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'preview', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic forecolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
        });
    </script>
</body>
</html>

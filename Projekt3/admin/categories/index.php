<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/functions.php';

Security::requireLogin();

$database = new Database();
$conn = $database->getConnection();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Ungültiges Sicherheitstoken.';
    } else {
        if ($_POST['action'] === 'create') {
            $name = Security::cleanInput($_POST['name']);
            $slug = createSlug($_POST['slug'] ?: $name);
            $description = Security::cleanInput($_POST['description']);
            
            $stmt = $conn->prepare("SELECT id FROM blog_categories WHERE slug = ?");
            $stmt->execute([$slug]);
            if ($stmt->fetch()) {
                $error = 'Slug existiert bereits!';
            } else {
                $stmt = $conn->prepare("INSERT INTO blog_categories (name, slug, description) VALUES (?, ?, ?)");
                $stmt->execute([$name, $slug, $description]);
                $success = 'Kategorie erfolgreich erstellt!';
            }
        } elseif ($_POST['action'] === 'update') {
            $id = (int)$_POST['id'];
            $name = Security::cleanInput($_POST['name']);
            $slug = createSlug($_POST['slug'] ?: $name);
            $description = Security::cleanInput($_POST['description']);
            
            $stmt = $conn->prepare("SELECT id FROM blog_categories WHERE slug = ? AND id != ?");
            $stmt->execute([$slug, $id]);
            if ($stmt->fetch()) {
                $error = 'Slug existiert bereits!';
            } else {
                $stmt = $conn->prepare("UPDATE blog_categories SET name = ?, slug = ?, description = ? WHERE id = ?");
                $stmt->execute([$name, $slug, $description, $id]);
                $success = 'Kategorie erfolgreich aktualisiert!';
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("DELETE FROM blog_categories WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'Kategorie erfolgreich gelöscht!';
        }
    }
}

$categories = $conn->query("SELECT c.*, COUNT(p.id) as post_count 
                             FROM blog_categories c 
                             LEFT JOIN blog_posts p ON c.id = p.category_id 
                             GROUP BY c.id 
                             ORDER BY c.name")->fetchAll();

$csrf_token = Security::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategorien - Blog Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Kategorien</h1>
        
        <?php if ($success): ?>
            <?php echo showAlert($success, 'success'); ?>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <?php echo showAlert($error, 'error'); ?>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Neue Kategorie</h2>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Name *</label>
                            <input type="text" name="name" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Slug</label>
                            <input type="text" name="slug"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                   placeholder="automatisch generiert">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Beschreibung</label>
                            <textarea name="description" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Kategorie erstellen
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left py-3 px-4">Name</th>
                                <th class="text-left py-3 px-4">Slug</th>
                                <th class="text-left py-3 px-4">Posts</th>
                                <th class="text-left py-3 px-4">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <div class="font-semibold"><?php echo htmlspecialchars($cat['name']); ?></div>
                                    <?php if ($cat['description']): ?>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($cat['description']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-gray-600"><?php echo htmlspecialchars($cat['slug']); ?></td>
                                <td class="py-3 px-4"><?php echo $cat['post_count']; ?></td>
                                <td class="py-3 px-4">
                                    <div class="flex gap-2">
                                        <button onclick="editCategory(<?php echo htmlspecialchars(json_encode($cat)); ?>)" 
                                                class="text-blue-600 hover:text-blue-800">Bearbeiten</button>
                                        <form method="POST" class="inline" onsubmit="return confirm('Kategorie wirklich löschen?')">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-800">Löschen</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Kategorie bearbeiten</h2>
            <form method="POST" id="editForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Name *</label>
                    <input type="text" name="name" id="edit_name" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Slug</label>
                    <input type="text" name="slug" id="edit_slug"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Beschreibung</label>
                    <textarea name="description" id="edit_description" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Speichern
                    </button>
                    <button type="button" onclick="closeModal()" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                        Abbrechen
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function editCategory(cat) {
            document.getElementById('edit_id').value = cat.id;
            document.getElementById('edit_name').value = cat.name;
            document.getElementById('edit_slug').value = cat.slug;
            document.getElementById('edit_description').value = cat.description || '';
            document.getElementById('editModal').classList.remove('hidden');
        }
        
        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</body>
</html>

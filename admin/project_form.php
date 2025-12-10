<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: /admin/');
    exit;
}

require_once '../config/database.php';
$db = getDB();

$project = null;
$isEdit = false;
$message = '';
$error = '';

// Load project for editing
if (isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $project = $stmt->fetch();
    $isEdit = true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $detailed_description = $_POST['detailed_description'] ?? '';
    $category = $_POST['category'] ?? '';
    $technologies = array_filter(array_map('trim', explode(',', $_POST['technologies'] ?? '')));
    $features = array_filter(array_map('trim', explode(',', $_POST['features'] ?? '')));
    $image_url = $_POST['image_url'] ?? null;
    $demo_url = $_POST['demo_url'] ?? null;
    $github_url = $_POST['github_url'] ?? null;
    
    if (empty($title) || empty($description) || empty($detailed_description) || empty($category) || empty($technologies) || empty($features)) {
        $error = 'Bitte füllen Sie alle Pflichtfelder aus.';
    } else {
        try {
            $tech_json = json_encode($technologies);
            $feat_json = json_encode($features);
            
            if ($isEdit && $project) {
                $stmt = $db->prepare("UPDATE projects SET title = ?, description = ?, detailed_description = ?, category = ?, technologies = ?, features = ?, image_url = ?, demo_url = ?, github_url = ? WHERE id = ?");
                $stmt->execute([$title, $description, $detailed_description, $category, $tech_json, $feat_json, $image_url, $demo_url, $github_url, $project['id']]);
                $message = 'Projekt erfolgreich aktualisiert!';
            } else {
                $stmt = $db->prepare("INSERT INTO projects (title, description, detailed_description, category, technologies, features, image_url, demo_url, github_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $detailed_description, $category, $tech_json, $feat_json, $image_url, $demo_url, $github_url]);
                $message = 'Projekt erfolgreich erstellt!';
            }
            
            header('Location: /admin/projects.php');
            exit;
        } catch (Exception $e) {
            $error = 'Fehler beim Speichern: ' . $e->getMessage();
        }
    }
}

$pageTitle = ($isEdit ? 'Projekt bearbeiten' : 'Neues Projekt') . ' - Admin';
require_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-3xl mx-auto">
        <a href="/admin/projects.php" class="text-primary hover:underline mb-6 inline-block">← Zurück</a>
        
        <h1 class="text-3xl font-bold mb-6"><?= $isEdit ? 'Projekt bearbeiten' : 'Neues Projekt' ?></h1>
        
        <?php if ($message): ?>
            <div class="bg-green-500/10 border border-green-500 text-green-500 p-4 rounded-md mb-6">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-md mb-6">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium mb-2">Titel *</label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       value="<?= htmlspecialchars($project['title'] ?? $_POST['title'] ?? '') ?>"
                       required
                       class="w-full px-4 py-2 bg-background text-foreground border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            
            <div>
                <label for="description" class="block text-sm font-medium mb-2">Kurzbeschreibung *</label>
                <textarea id="description" 
                          name="description" 
                          rows="3" 
                          required
                          class="w-full px-4 py-2 bg-background text-foreground border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"><?= htmlspecialchars($project['description'] ?? $_POST['description'] ?? '') ?></textarea>
            </div>
            
            <div>
                <label for="detailed_description" class="block text-sm font-medium mb-2">Detaillierte Beschreibung *</label>
                <textarea id="detailed_description" 
                          name="detailed_description" 
                          rows="8" 
                          required
                          class="w-full px-4 py-2 bg-background text-foreground border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"><?= htmlspecialchars($project['detailed_description'] ?? $_POST['detailed_description'] ?? '') ?></textarea>
            </div>
            
            <div>
                <label for="category" class="block text-sm font-medium mb-2">Kategorie *</label>
                <input type="text" 
                       id="category" 
                       name="category" 
                       value="<?= htmlspecialchars($project['category'] ?? $_POST['category'] ?? '') ?>"
                       placeholder="z.B. PHP/MySQL, Full-Stack"
                       required
                       class="w-full px-4 py-2 bg-background text-foreground border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            
            <div>
                <label for="technologies" class="block text-sm font-medium mb-2">Technologien (kommagetrennt) *</label>
                <input type="text" 
                       id="technologies" 
                       name="technologies" 
                       value="<?= htmlspecialchars($project ? implode(', ', json_decode($project['technologies'], true)) : ($_POST['technologies'] ?? '')) ?>"
                       placeholder="PHP, MySQL, JavaScript"
                       required
                       class="w-full px-4 py-2 bg-background text-foreground border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            
            <div>
                <label for="features" class="block text-sm font-medium mb-2">Features (kommagetrennt) *</label>
                <textarea id="features" 
                          name="features" 
                          rows="4" 
                          placeholder="Feature 1, Feature 2, Feature 3"
                          required
                          class="w-full px-4 py-2 bg-background text-foreground border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"><?= htmlspecialchars($project ? implode(', ', json_decode($project['features'], true)) : ($_POST['features'] ?? '')) ?></textarea>
            </div>
            
            <div>
                <label for="image_url" class="block text-sm font-medium mb-2">Bild-URL (optional)</label>
                <input type="url" 
                       id="image_url" 
                       name="image_url" 
                       value="<?= htmlspecialchars($project['image_url'] ?? $_POST['image_url'] ?? '') ?>"
                       class="w-full px-4 py-2 bg-background text-foreground border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            
            <div>
                <label for="demo_url" class="block text-sm font-medium mb-2">Demo-URL (optional)</label>
                <input type="url" 
                       id="demo_url" 
                       name="demo_url" 
                       value="<?= htmlspecialchars($project['demo_url'] ?? $_POST['demo_url'] ?? '') ?>"
                       class="w-full px-4 py-2 bg-background text-foreground border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            
            <div>
                <label for="github_url" class="block text-sm font-medium mb-2">GitHub-URL (optional)</label>
                <input type="url" 
                       id="github_url" 
                       name="github_url" 
                       value="<?= htmlspecialchars($project['github_url'] ?? $_POST['github_url'] ?? '') ?>"
                       class="w-full px-4 py-2 bg-background text-foreground border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-primary text-primary-foreground px-6 py-3 rounded-md hover:bg-primary/90 transition-colors">
                    <?= $isEdit ? 'Aktualisieren' : 'Erstellen' ?>
                </button>
                <a href="/admin/projects.php" class="px-6 py-3 border border-border rounded-md hover:bg-secondary text-center">
                    Abbrechen
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

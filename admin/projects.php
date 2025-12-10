<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: /admin/');
    exit;
}

require_once '../config/database.php';
$pageTitle = 'Admin Panel - Projekte';

// Handle delete
if (isset($_GET['delete'])) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: /admin/projects.php');
    exit;
}

// Fetch all projects
$db = getDB();
$stmt = $db->query("SELECT * FROM projects ORDER BY created_at DESC");
$projects = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-12">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold">Admin Panel</h1>
            <p class="text-muted-foreground">Verwalten Sie Ihre Projekte</p>
        </div>
        <div class="flex gap-2">
            <a href="/admin/project_form.php" class="bg-primary text-primary-foreground px-4 py-2 rounded-md hover:bg-primary/90">
                + Neues Projekt
            </a>
            <a href="/admin/logout.php" class="border border-border px-4 py-2 rounded-md hover:bg-secondary">
                Abmelden
            </a>
        </div>
    </div>
    
    <div class="space-y-4">
        <?php foreach ($projects as $project): 
            $technologies = json_decode($project['technologies'], true);
        ?>
            <div class="border border-border rounded-lg p-6">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($project['title']) ?></h3>
                        <p class="text-muted-foreground mb-4"><?= htmlspecialchars($project['description']) ?></p>
                        
                        <div class="flex flex-wrap gap-2 mb-3">
                            <?php foreach ($technologies as $tech): ?>
                                <span class="px-2 py-1 bg-secondary text-xs rounded">
                                    <?= htmlspecialchars($tech) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        
                        <p class="text-sm text-muted-foreground">
                            <strong>Kategorie:</strong> <?= htmlspecialchars($project['category']) ?>
                        </p>
                    </div>
                    
                    <div class="flex gap-2">
                        <a href="/admin/project_form.php?id=<?= $project['id'] ?>" 
                           class="px-3 py-1 border border-border rounded-md hover:bg-secondary">
                            Bearbeiten
                        </a>
                        <a href="?delete=<?= $project['id'] ?>" 
                           onclick="return confirm('Projekt wirklich löschen?')"
                           class="px-3 py-1 text-red-500 border border-red-500 rounded-md hover:bg-red-500/10">
                            Löschen
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

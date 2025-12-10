<?php
require_once 'config/database.php';

$projectId = $_GET['id'] ?? '';
if (empty($projectId)) {
    header('Location: /projects.php');
    exit;
}

// Fetch project
$db = getDB();
$stmt = $db->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$projectId]);
$project = $stmt->fetch();

if (!$project) {
    header('Location: /projects.php');
    exit;
}

$pageTitle = htmlspecialchars($project['title']) . ' - Portfolio';
$technologies = json_decode($project['technologies'], true);
$features = json_decode($project['features'], true);

require_once 'includes/header.php';
?>

<div class="container mx-auto px-4 py-12">
    <a href="/projects.php" class="text-primary hover:underline mb-6 inline-block">← Zurück zu Projekten</a>
    
    <h1 class="text-4xl font-bold mb-4"><?= htmlspecialchars($project['title']) ?></h1>
    <p class="text-xl text-muted-foreground mb-8"><?= htmlspecialchars($project['description']) ?></p>
    
    <div class="grid md:grid-cols-3 gap-8">
        <div class="md:col-span-2">
            <h2 class="text-2xl font-bold mb-4">Projektbeschreibung</h2>
            <div class="prose prose-invert max-w-none mb-8">
                <?= nl2br(htmlspecialchars($project['detailed_description'])) ?>
            </div>
            
            <h2 class="text-2xl font-bold mb-4">Hauptfunktionen</h2>
            <ul class="list-disc list-inside space-y-2 mb-8">
                <?php foreach ($features as $feature): ?>
                    <li><?= htmlspecialchars($feature) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div>
            <div class="border border-border rounded-lg p-6 sticky top-20">
                <h3 class="text-lg font-bold mb-4">Technologien</h3>
                <div class="flex flex-wrap gap-2 mb-6">
                    <?php foreach ($technologies as $tech): ?>
                        <span class="px-3 py-1 bg-secondary text-sm rounded">
                            <?= htmlspecialchars($tech) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
                
                <div class="space-y-2">
                    <p class="text-sm text-muted-foreground">
                        <strong>Kategorie:</strong> <?= htmlspecialchars($project['category']) ?>
                    </p>
                    
                    <?php if ($project['demo_url']): ?>
                        <a href="<?= htmlspecialchars($project['demo_url']) ?>" 
                           target="_blank" 
                           class="block w-full text-center bg-primary text-primary-foreground px-4 py-2 rounded-md hover:bg-primary/90">
                            Live Demo
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($project['github_url']): ?>
                        <a href="<?= htmlspecialchars($project['github_url']) ?>" 
                           target="_blank" 
                           class="block w-full text-center border border-border px-4 py-2 rounded-md hover:bg-secondary">
                            GitHub ansehen
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

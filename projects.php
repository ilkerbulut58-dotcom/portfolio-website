<?php
// HATA AYIKLAMA - Tüm hataları göster
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<!-- DEBUG: projects.php yükleniyor... -->\n";

try {
    require_once __DIR__ . '/config/database.php';
    echo "<!-- DEBUG: database.php yüklendi -->\n";
} catch (Exception $e) {
    die("HATA - database.php yüklenemedi: " . $e->getMessage());
}

$pageTitle = 'Projekte - Portfolio';

// Get filter category from query string
$category = $_GET['category'] ?? 'Alle';

// Fetch projects
try {
    echo "<!-- DEBUG: Veritabanına bağlanılıyor... -->\n";
    $db = getDB();
    echo "<!-- DEBUG: Veritabanı bağlantısı başarılı -->\n";
    
    if ($category === 'Alle') {
        $stmt = $db->prepare("SELECT * FROM projects ORDER BY created_at DESC");
        $stmt->execute();
    } else {
        $stmt = $db->prepare("SELECT * FROM projects WHERE category = ? ORDER BY created_at DESC");
        $stmt->execute([$category]);
    }
    $projects = $stmt->fetchAll();
    echo "<!-- DEBUG: " . count($projects) . " proje bulundu -->\n";
} catch (Exception $e) {
    die("<h1>VERITABANI HATASI</h1><pre>" . $e->getMessage() . "</pre>");
}

// Get unique categories
try {
    $categoriesStmt = $db->query("SELECT DISTINCT category FROM projects");
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<!-- DEBUG: " . count($categories) . " kategori bulundu -->\n";
} catch (Exception $e) {
    die("<h1>KATEGORİ HATASI</h1><pre>" . $e->getMessage() . "</pre>");
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="py-12 md:py-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                Meine Projekte
            </h1>
            <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
                Eine Auswahl meiner Softwareentwicklungs-Projekte, die meine Fähigkeiten und Erfahrung demonstrieren
            </p>
        </div>
        
        <!-- Category Filter -->
        <div class="mb-8 overflow-x-auto">
            <div class="flex flex-wrap gap-2 justify-center min-w-max">
                <a href="?category=Alle" class="px-4 py-2 rounded-md font-medium transition-colors <?= $category === 'Alle' ? 'bg-primary text-primary-foreground' : 'border border-border hover:bg-accent' ?>">
                    Alle
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="?category=<?= urlencode($cat) ?>" class="px-4 py-2 rounded-md font-medium transition-colors <?= $category === $cat ? 'bg-primary text-primary-foreground' : 'border border-border hover:bg-accent' ?>">
                        <?= htmlspecialchars($cat) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Projects Grid -->
        <?php if (count($projects) > 0): ?>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($projects as $project): 
                    $technologies = json_decode($project['technologies'], true);
                ?>
                    <div class="flex flex-col bg-card border border-border rounded-lg hover-lift overflow-hidden">
                        <!-- Project Image/Placeholder -->
                        <div class="aspect-video bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center border-b border-border">
                            <?php if ($project['image_url']): ?>
                                <img src="<?= htmlspecialchars($project['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($project['title']) ?>" 
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="text-6xl font-bold text-primary/20">
                                    <?= strtoupper(substr($project['title'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Card Content -->
                        <div class="flex-1 p-6">
                            <h3 class="font-bold text-xl mb-3">
                                <?= htmlspecialchars($project['title']) ?>
                            </h3>
                            <p class="text-sm text-muted-foreground mb-4 line-clamp-3">
                                <?= htmlspecialchars($project['description']) ?>
                            </p>
                            <div class="flex flex-wrap gap-2">
                                <?php 
                                $displayTechs = array_slice($technologies, 0, 4);
                                foreach ($displayTechs as $tech): 
                                ?>
                                    <span class="px-2 py-1 bg-secondary text-xs rounded-md font-mono">
                                        <?= htmlspecialchars($tech) ?>
                                    </span>
                                <?php endforeach; ?>
                                <?php if (count($technologies) > 4): ?>
                                    <span class="px-2 py-1 bg-secondary text-xs rounded-md font-mono">
                                        +<?= count($technologies) - 4 ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Card Footer -->
                        <div class="p-6 pt-0 flex flex-wrap gap-2">
                            <a href="/project.php?id=<?= $project['id'] ?>" class="inline-flex items-center gap-2 bg-primary text-primary-foreground px-4 py-2 rounded-md hover:opacity-90 transition-opacity text-sm font-medium">
                                Details ansehen
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <?php if ($project['github_url']): ?>
                                <a href="<?= htmlspecialchars($project['github_url']) ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 border border-border px-4 py-2 rounded-md hover:bg-accent transition-colors text-sm font-medium">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                    </svg>
                                    GitHub
                                </a>
                            <?php endif; ?>
                            <?php if ($project['demo_url']): ?>
                                <a href="<?= htmlspecialchars($project['demo_url']) ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 border border-border px-4 py-2 rounded-md hover:bg-accent transition-colors text-sm font-medium">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Demo
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-24">
                <p class="text-lg text-muted-foreground">
                    Keine Projekte in dieser Kategorie gefunden.
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

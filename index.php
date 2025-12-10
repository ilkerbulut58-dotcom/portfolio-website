<?php
$pageTitle = 'Portfolio - Softwareentwickler & Projektmanager';
require_once 'includes/header.php';

$technologies = ['PHP', 'MySQL', 'JavaScript', 'TypeScript', 'React', 'Node.js', 'Python', 'PostgreSQL', 'MongoDB', 'Docker', 'Git', 'REST APIs'];
?>

<!-- Hero Section -->
<section class="py-16 md:py-24">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
                    Softwareentwickler & Projektmanager
                </h1>
                <p class="text-lg md:text-xl text-muted-foreground mb-8">
                    Erfahrener Entwickler mit Fokus auf moderne Web-Technologien und maßgeschneiderte Softwarelösungen. 
                    Spezialisiert auf Full-Stack-Entwicklung mit PHP, JavaScript und Python.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="/projects.php" class="inline-flex items-center gap-2 bg-primary text-primary-foreground px-6 py-3 rounded-md hover:opacity-90 transition-opacity font-medium">
                        Projekte ansehen
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="/contact.php" class="inline-flex items-center gap-2 border border-border px-6 py-3 rounded-md hover:bg-accent transition-colors font-medium">
                        Kontakt aufnehmen
                    </a>
                </div>
                <div class="mt-8">
                    <p class="text-sm text-muted-foreground mb-3">Technologie-Stack:</p>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach (array_slice($technologies, 0, 6) as $tech): ?>
                            <span class="px-3 py-1 bg-secondary text-xs rounded-md font-mono">
                                <?= $tech ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Visual Element -->
            <div class="hidden lg:flex items-center justify-center">
                <div class="relative w-full max-w-md aspect-square">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/20 via-primary/10 to-transparent rounded-lg blur-3xl"></div>
                    <div class="relative bg-card border border-border rounded-lg p-8 h-full flex items-center justify-center">
                        <div class="grid grid-cols-2 gap-4 w-full">
                            <div class="bg-primary/10 rounded-md p-4 h-24"></div>
                            <div class="bg-primary/5 rounded-md p-4 h-24"></div>
                            <div class="bg-primary/5 rounded-md p-4 h-24"></div>
                            <div class="bg-primary/10 rounded-md p-4 h-24"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Skills Section -->
<section class="py-16 md:py-24 bg-muted/30">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Kernkompetenzen</h2>
            <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
                Vielseitige Expertise in der modernen Softwareentwicklung
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Frontend -->
            <div class="bg-card border border-border rounded-lg p-6 hover-lift">
                <div class="mb-4">
                    <div class="inline-flex p-3 bg-primary/10 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                    </div>
                </div>
                <h3 class="font-bold text-lg mb-2">Frontend-Entwicklung</h3>
                <p class="text-sm text-muted-foreground">Moderne, responsive Benutzeroberflächen mit React, TypeScript und modernen CSS-Frameworks</p>
            </div>
            
            <!-- Backend -->
            <div class="bg-card border border-border rounded-lg p-6 hover-lift">
                <div class="mb-4">
                    <div class="inline-flex p-3 bg-primary/10 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                        </svg>
                    </div>
                </div>
                <h3 class="font-bold text-lg mb-2">Backend-Entwicklung</h3>
                <p class="text-sm text-muted-foreground">Robuste Server-Anwendungen mit PHP, Node.js und Python für skalierbare Lösungen</p>
            </div>
            
            <!-- Database -->
            <div class="bg-card border border-border rounded-lg p-6 hover-lift">
                <div class="mb-4">
                    <div class="inline-flex p-3 bg-primary/10 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                    </div>
                </div>
                <h3 class="font-bold text-lg mb-2">Datenbank-Design</h3>
                <p class="text-sm text-muted-foreground">Effiziente Datenbankarchitekturen mit MySQL, PostgreSQL und NoSQL-Lösungen</p>
            </div>
            
            <!-- Full-Stack -->
            <div class="bg-card border border-border rounded-lg p-6 hover-lift">
                <div class="mb-4">
                    <div class="inline-flex p-3 bg-primary/10 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="font-bold text-lg mb-2">Full-Stack-Projekte</h3>
                <p class="text-sm text-muted-foreground">End-to-End Entwicklung von kompletten Webanwendungen und Management-Systemen</p>
            </div>
        </div>
    </div>
</section>

<!-- Technologies Section -->
<section class="py-16 md:py-24">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Technologien</h2>
            <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
                Umfassende Kenntnisse in modernen Entwicklungs-Tools und Frameworks
            </p>
        </div>
        
        <div class="flex flex-wrap justify-center gap-3 max-w-4xl mx-auto">
            <?php foreach ($technologies as $tech): ?>
                <span class="px-4 py-2 text-sm border border-border rounded-md font-mono hover:bg-accent transition-colors cursor-pointer">
                    <?= $tech ?>
                </span>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 md:py-24 bg-primary/5">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">
                Bereit für Ihr nächstes Projekt?
            </h2>
            <p class="text-lg text-muted-foreground mb-8">
                Lassen Sie uns gemeinsam innovative Lösungen entwickeln. 
                Nehmen Sie Kontakt auf und erfahren Sie mehr über meine Arbeit.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="/projects.php" class="inline-flex items-center gap-2 bg-primary text-primary-foreground px-6 py-3 rounded-md hover:opacity-90 transition-opacity font-medium">
                    Portfolio durchsehen
                </a>
                <a href="/contact.php" class="inline-flex items-center gap-2 border border-border px-6 py-3 rounded-md hover:bg-accent transition-colors font-medium">
                    Nachricht senden
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

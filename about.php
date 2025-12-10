<?php
$pageTitle = 'Über Mich - Portfolio';
require_once 'includes/header.php';
?>

<div class="container mx-auto px-4 py-12">
    <h1 class="text-4xl font-bold mb-2">Über Mich</h1>
    <p class="text-muted-foreground mb-8">Professioneller Softwareentwickler mit Leidenschaft für innovative Lösungen</p>
    
    <div class="max-w-3xl">
        <p class="text-lg mb-6">
            Als erfahrener Softwareentwickler bringe ich fundierte Kenntnisse in der Full-Stack-Entwicklung mit. 
            Ich habe erfolgreich zahlreiche Projekte von der Konzeption bis zur Umsetzung realisiert.
        </p>
        
        <h2 class="text-2xl font-bold mb-4 mt-8">Technische Fähigkeiten</h2>
        
        <div class="space-y-6">
            <div>
                <div class="flex justify-between mb-2">
                    <span class="font-medium">PHP & MySQL</span>
                    <span class="text-muted-foreground">90%</span>
                </div>
                <div class="h-2 bg-secondary rounded-full overflow-hidden">
                    <div class="h-full bg-primary" style="width: 90%"></div>
                </div>
            </div>
            
            <div>
                <div class="flex justify-between mb-2">
                    <span class="font-medium">JavaScript / TypeScript</span>
                    <span class="text-muted-foreground">85%</span>
                </div>
                <div class="h-2 bg-secondary rounded-full overflow-hidden">
                    <div class="h-full bg-primary" style="width: 85%"></div>
                </div>
            </div>
            
            <div>
                <div class="flex justify-between mb-2">
                    <span class="font-medium">React & Frontend Frameworks</span>
                    <span class="text-muted-foreground">80%</span>
                </div>
                <div class="h-2 bg-secondary rounded-full overflow-hidden">
                    <div class="h-full bg-primary" style="width: 80%"></div>
                </div>
            </div>
            
            <div>
                <div class="flex justify-between mb-2">
                    <span class="font-medium">Python & Django</span>
                    <span class="text-muted-foreground">75%</span>
                </div>
                <div class="h-2 bg-secondary rounded-full overflow-hidden">
                    <div class="h-full bg-primary" style="width: 75%"></div>
                </div>
            </div>
        </div>
        
        <h2 class="text-2xl font-bold mb-4 mt-12">Arbeitsphilosophie</h2>
        <div class="grid md:grid-cols-3 gap-6 mt-6">
            <div class="p-6 border border-border rounded-lg">
                <h3 class="font-bold mb-2">Sauberer Code</h3>
                <p class="text-muted-foreground text-sm">
                    Wartbarer und gut dokumentierter Code für langfristige Projekte
                </p>
            </div>
            <div class="p-6 border border-border rounded-lg">
                <h3 class="font-bold mb-2">Best Practices</h3>
                <p class="text-muted-foreground text-sm">
                    Moderne Entwicklungsmethoden und bewährte Architekturmuster
                </p>
            </div>
            <div class="p-6 border border-border rounded-lg">
                <h3 class="font-bold mb-2">Nutzerorientiert</h3>
                <p class="text-muted-foreground text-sm">
                    Lösungen, die echten Mehrwert schaffen und begeistern
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

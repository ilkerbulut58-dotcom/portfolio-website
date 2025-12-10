<?php
require_once 'config/database.php';
$pageTitle = 'Kontakt - Portfolio';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $msg = $_POST['message'] ?? '';
    
    if (empty($name) || empty($email) || empty($subject) || empty($msg)) {
        $error = 'Bitte füllen Sie alle Felder aus.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $msg]);
            $message = 'Nachricht erfolgreich gesendet!';
            $_POST = []; // Clear form
        } catch (Exception $e) {
            $error = 'Fehler beim Senden der Nachricht.';
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-4xl font-bold mb-2">Kontakt</h1>
        <p class="text-muted-foreground mb-8">Nehmen Sie Kontakt mit mir auf</p>
        
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
                <label for="name" class="block text-sm font-medium mb-2">Name</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                       required
                       class="w-full px-4 py-2 bg-background border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            
            <div>
                <label for="email" class="block text-sm font-medium mb-2">E-Mail</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       required
                       class="w-full px-4 py-2 bg-background border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            
            <div>
                <label for="subject" class="block text-sm font-medium mb-2">Betreff</label>
                <input type="text" 
                       id="subject" 
                       name="subject" 
                       value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>"
                       required
                       class="w-full px-4 py-2 bg-background border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            
            <div>
                <label for="message" class="block text-sm font-medium mb-2">Nachricht</label>
                <textarea id="message" 
                          name="message" 
                          rows="6" 
                          required
                          class="w-full px-4 py-2 bg-background border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
            </div>
            
            <button type="submit" class="w-full bg-primary text-primary-foreground px-6 py-3 rounded-md hover:bg-primary/90 transition-colors">
                Nachricht senden
            </button>
        </form>
        
        <div class="mt-12 p-6 border border-border rounded-lg">
            <h3 class="text-lg font-bold mb-4">Kontaktinformationen</h3>
            <div class="space-y-2 text-muted-foreground">
                <p><strong>E-Mail:</strong> ilkerbulut83@hotmail.com</p>
                <p><strong>Telefon:</strong> +49 162 932 99 58</p>
                <p><strong>Standort:</strong> Kornwestheim</p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

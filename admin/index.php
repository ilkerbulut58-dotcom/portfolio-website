<?php
session_start();
require_once '../config/database.php';

$adminPassword = getenv('ADMIN_PASSWORD') ?: '';
if ($adminPassword === '' && is_file(__DIR__ . '/../config/admin.local.php')) {
    require __DIR__ . '/../config/admin.local.php';
    $adminPassword = defined('ADMIN_PASSWORD') ? ADMIN_PASSWORD : '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if ($adminPassword !== '' && hash_equals($adminPassword, $password)) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: /admin/projects.php');
        exit;
    }
    $error = 'Falsches Passwort oder Admin-Passwort nicht konfiguriert';
}

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    header('Location: /admin/projects.php');
    exit;
}

$pageTitle = 'Admin Login - Portfolio';
require_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-center">Admin Login</h1>

        <?php if (isset($error)): ?>
            <div class="bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-md mb-6">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($adminPassword === ''): ?>
            <div class="bg-amber-500/10 border border-amber-500 text-amber-700 p-4 rounded-md mb-6">
                Admin-Passwort nicht gesetzt. Setzen Sie die Umgebungsvariable <code>ADMIN_PASSWORD</code> auf dem Server.
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6 border border-border p-6 rounded-lg">
            <div>
                <label for="password" class="block text-sm font-medium mb-2">Passwort</label>
                <input type="password"
                       id="password"
                       name="password"
                       required
                       class="w-full px-4 py-2 bg-background border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <button type="submit" class="w-full bg-primary text-primary-foreground px-6 py-3 rounded-md hover:bg-primary/90 transition-colors">
                Anmelden
            </button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

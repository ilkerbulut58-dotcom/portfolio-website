<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog System - Demo Nicht Verfügbar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-4xl w-full bg-white rounded-lg shadow-xl p-8">
        <div class="text-center mb-8">
            <div class="inline-block bg-blue-100 rounded-full p-4 mb-4">
                <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Professionelles Blog-System</h1>
            <p class="text-xl text-gray-600">Bereit für Ionos Shared Hosting Deployment</p>
        </div>
        
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 mb-8">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Demo nicht verfügbar in Replit</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Die externe Ionos MySQL-Datenbank ist von Replit aus nicht erreichbar. Dies ist normal und erwartet.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg">
                <h3 class="text-lg font-bold text-gray-800 mb-4">✨ Features</h3>
                <ul class="space-y-2 text-gray-700">
                    <li>✓ Admin-Panel mit Login</li>
                    <li>✓ Blog Posts CRUD</li>
                    <li>✓ Kategorien-System</li>
                    <li>✓ Bild-Upload</li>
                    <li>✓ Kommentarsystem</li>
                    <li>✓ Suchfunktion</li>
                    <li>✓ Dark/Light Mode</li>
                    <li>✓ SEO-freundliche URLs</li>
                    <li>✓ Responsive Design</li>
                </ul>
            </div>
            
            <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg">
                <h3 class="text-lg font-bold text-gray-800 mb-4">🔒 Sicherheit</h3>
                <ul class="space-y-2 text-gray-700">
                    <li>✓ SQL Injection-Schutz</li>
                    <li>✓ XSS-Filterung</li>
                    <li>✓ CSRF-Token</li>
                    <li>✓ Passwort-Hashing</li>
                    <li>✓ Session-Authentifizierung</li>
                    <li>✓ .htaccess-Schutz</li>
                    <li>✓ Input-Validierung</li>
                    <li>✓ Prepared Statements</li>
                </ul>
            </div>
        </div>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4">🚀 Deployment auf Ionos</h3>
            <ol class="space-y-3 text-gray-700">
                <li><strong>1. Dateien hochladen:</strong> Alle Projektdateien per FTP auf Ionos hochladen</li>
                <li><strong>2. Datenbank einrichten:</strong> SQL-Script (<code class="bg-blue-100 px-2 py-1 rounded">IONOS_SETUP.sql</code>) in phpMyAdmin ausführen</li>
                <li><strong>3. Konfiguration:</strong> DB-Zugangsdaten in <code class="bg-blue-100 px-2 py-1 rounded">config/database.php</code> eintragen</li>
                <li><strong>4. Testen:</strong> Website und Admin-Panel aufrufen</li>
                <li><strong>5. Sichern:</strong> Admin-Passwort sofort nach Setup ändern</li>
            </ol>
        </div>
        
        <div class="grid md:grid-cols-3 gap-4 mb-8">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-3xl font-bold text-blue-600">PHP 8.4</div>
                <div class="text-sm text-gray-600 mt-2">Moderne PHP-Version</div>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-3xl font-bold text-green-600">MySQL 5.7+</div>
                <div class="text-sm text-gray-600 mt-2">Robuste Datenbank</div>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-3xl font-bold text-purple-600">Tailwind CSS</div>
                <div class="text-sm text-gray-600 mt-2">Modernes Design</div>
            </div>
        </div>
        
        <div class="border-t pt-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">📚 Dokumentation</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <a href="/DEPLOYMENT.md" class="flex items-center p-4 bg-white border-2 border-blue-200 rounded-lg hover:border-blue-400 transition">
                    <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div>
                        <div class="font-bold">DEPLOYMENT.md</div>
                        <div class="text-sm text-gray-600">Schritt-für-Schritt Anleitung</div>
                    </div>
                </a>
                
                <a href="/IONOS_SETUP.sql" class="flex items-center p-4 bg-white border-2 border-green-200 rounded-lg hover:border-green-400 transition">
                    <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                    </svg>
                    <div>
                        <div class="font-bold">IONOS_SETUP.sql</div>
                        <div class="text-sm text-gray-600">Datenbank-Setup-Script</div>
                    </div>
                </a>
            </div>
        </div>
        
        <div class="mt-8 p-6 bg-gray-50 rounded-lg">
            <h3 class="font-bold text-gray-800 mb-2">Projektstruktur</h3>
            <pre class="text-sm text-gray-700 overflow-x-auto"><code>blog-system/
├── admin/              Admin-Panel
│   ├── index.php      Dashboard
│   ├── login.php      Login
│   ├── posts/         Posts-Verwaltung
│   ├── categories/    Kategorien
│   └── comments/      Kommentare
├── config/            Konfiguration
├── includes/          Templates
├── assets/            CSS/JS
├── uploads/           Uploads
├── .htaccess          URL Rewriting
└── index.php          Startseite</code></pre>
        </div>
    </div>
</body>
</html>

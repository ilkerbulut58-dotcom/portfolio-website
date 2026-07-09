<?php
/**
 * Database configuration loader.
 * Real credentials belong in config/database.local.php (gitignored).
 */

$localConfig = __DIR__ . '/database.local.php';
if (is_file($localConfig)) {
    require $localConfig;
} else {
    define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
    define('DB_NAME', getenv('DB_NAME') ?: '');
    define('DB_USER', getenv('DB_USER') ?: '');
    define('DB_PASS', getenv('DB_PASS') ?: '');
}

function getDB() {
    if (DB_NAME === '' || DB_USER === '') {
        die('<h1>Database not configured</h1><p>Copy <code>config/database.example.php</code> to <code>config/database.local.php</code> and set your credentials.</p>');
    }

    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die('<h1>Database connection error</h1><p>Check config/database.local.php</p>');
    }
}

<?php
/**
 * Database Connection Test — uses Projekt1/includes/config.php (no hardcoded passwords).
 */

header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/includes/config.php';

echo "=== MySQL Connection Test ===\n\n";

if (DB_NAME === '' || DB_USER === '') {
    echo "ERROR: Database not configured.\n";
    echo "Copy Projekt1/includes/config.example.php to config.local.php and set credentials.\n";
    exit(1);
}

$tests = array(
    array('host' => DB_HOST, 'label' => 'configured DB_HOST'),
    array('host' => '127.0.0.1', 'label' => '127.0.0.1 (TCP)'),
);

$port = DB_PORT;
$dbname = DB_NAME;
$user = DB_USER;
$pass = DB_PASS;

foreach ($tests as $index => $test) {
    $host = $test['host'];
    $label = $test['label'];

    echo '--- Test ' . ($index + 1) . ': ' . $label . " ---\n";
    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        echo "OK: Connected via PDO\n\n";
    } catch (PDOException $e) {
        echo 'FAIL: ' . $e->getMessage() . "\n\n";
    }
}

echo "Done.\n";

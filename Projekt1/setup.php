<?php
/**
 * Database Setup Script
 * Run this ONCE to create all tables and insert sample data
 */

header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/includes/config.php';

echo '<html><head><title>Database Setup</title></head><body>';
echo '<h1>🔧 API Gateway Database Setup</h1>';
echo '<pre>';

try {
    // Connect to database
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_CHARSET
    );
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ));
    
    echo "✅ Connected to database: " . DB_NAME . "\n\n";
    
    // Read SQL file
    $sqlFile = __DIR__ . '/database/schema.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && 
                   strpos($stmt, '--') !== 0 && 
                   strpos($stmt, 'CREATE DATABASE') === false &&
                   strpos($stmt, 'USE ') === false;
        }
    );
    
    echo "📋 Found " . count($statements) . " SQL statements\n\n";
    
    $executed = 0;
    $errors = 0;
    
    foreach ($statements as $statement) {
        try {
            $pdo->exec($statement);
            $executed++;
            
            // Show what was executed
            if (stripos($statement, 'CREATE TABLE') !== false) {
                preg_match('/CREATE TABLE.*?`?(\w+)`?\s/i', $statement, $matches);
                $tableName = $matches[1] ?? 'unknown';
                echo "✅ Created table: $tableName\n";
            } elseif (stripos($statement, 'INSERT INTO') !== false) {
                preg_match('/INSERT INTO\s+`?(\w+)`?/i', $statement, $matches);
                $tableName = $matches[1] ?? 'unknown';
                echo "✅ Inserted data into: $tableName\n";
            }
        } catch (PDOException $e) {
            $errors++;
            // Ignore "table already exists" errors
            if (strpos($e->getMessage(), 'already exists') === false) {
                echo "❌ Error: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n";
    echo "========================================\n";
    echo "✅ Setup Complete!\n";
    echo "========================================\n";
    echo "Executed: $executed statements\n";
    echo "Errors: $errors\n\n";
    
    // Show created tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "📊 Database Tables (" . count($tables) . "):\n";
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        echo "   - $table ($count rows)\n";
    }
    
    echo "\n";
    echo "🎉 SUCCESS! Your database is ready!\n\n";
    echo "➡️ Go to dashboard: <a href='/Projekt1/'>Open Dashboard</a>\n";
    
} catch (Exception $e) {
    echo "❌ FATAL ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString();
}

echo '</pre></body></html>';

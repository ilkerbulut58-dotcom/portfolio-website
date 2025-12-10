<?php
/**
 * Database Connection Test
 * Simple test to verify IONOS MySQL connection
 */

header('Content-Type: text/plain; charset=utf-8');

echo "=== IONOS MySQL Connection Test ===\n\n";

// Database credentials - Testing multiple options
$tests = array(
    array('host' => '127.0.0.1', 'label' => '127.0.0.1 (TCP - RECOMMENDED)'),
    array('host' => 'localhost', 'label' => 'localhost (Unix socket)'),
    array('host' => 'db5018866111.hosting-data.io', 'label' => 'remote hostname')
);

$port = '3306';
$dbname = 'dbs14888922';
$user = 'dbu4055229';
$pass = 'Cemellim!5959:';

// Test all options
foreach ($tests as $index => $test) {
    $host = $test['host'];
    $label = $test['label'];
    
    echo "--- Test " . ($index + 1) . ": Connection using $label ($host) ---\n";
    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ));
        echo "✅ SUCCESS! Connected to database using $label\n";
        
        // Test query
        $stmt = $pdo->query("SELECT DATABASE() as db, VERSION() as version");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Connected to: " . $result['db'] . "\n";
        echo "MySQL Version: " . $result['version'] . "\n";
        echo "\n*** USE THIS HOST: $host ***\n";
        
        $pdo = null;
        break; // Stop after first success
    } catch (PDOException $e) {
        echo "❌ FAILED: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

echo "=== Original Test Code Below ===\n\n";

// Keep original variables for remaining tests
$host = 'localhost';

echo "Host: $host\n";
echo "Port: $port\n";
echo "Database: $dbname\n";
echo "User: $user\n";
echo "Password: " . str_repeat('*', strlen($pass)) . "\n\n";

// Test 1: Try with port
echo "--- Test 1: Connection with port ---\n";
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ));
    echo "✅ SUCCESS! Connected to database.\n";
    
    // Test query
    $stmt = $pdo->query("SELECT DATABASE() as db, VERSION() as version");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Connected to: " . $result['db'] . "\n";
    echo "MySQL Version: " . $result['version'] . "\n";
    
    $pdo = null;
} catch (PDOException $e) {
    echo "❌ FAILED: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Try without port
echo "--- Test 2: Connection without port ---\n";
try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ));
    echo "✅ SUCCESS! Connected to database.\n";
    $pdo = null;
} catch (PDOException $e) {
    echo "❌ FAILED: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Check if mysqli extension is available
echo "--- Test 3: MySQLi Extension ---\n";
if (extension_loaded('mysqli')) {
    echo "✅ MySQLi extension is loaded\n";
    
    $mysqli = new mysqli($host, $user, $pass, $dbname, $port);
    
    if ($mysqli->connect_error) {
        echo "❌ MySQLi Connection failed: " . $mysqli->connect_error . "\n";
    } else {
        echo "✅ MySQLi connected successfully!\n";
        echo "Server info: " . $mysqli->server_info . "\n";
        $mysqli->close();
    }
} else {
    echo "❌ MySQLi extension is NOT loaded\n";
}

echo "\n";

// Test 4: Check PDO drivers
echo "--- Test 4: Available PDO Drivers ---\n";
$drivers = PDO::getAvailableDrivers();
echo "Available: " . implode(', ', $drivers) . "\n";
if (in_array('mysql', $drivers)) {
    echo "✅ PDO MySQL driver is available\n";
} else {
    echo "❌ PDO MySQL driver is NOT available\n";
}

echo "\n=== Test Complete ===\n";

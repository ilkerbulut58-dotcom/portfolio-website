<?php
// Ionos Database Configuration
define('DB_HOST', 'db5018866111.hosting-data.io');
define('DB_NAME', 'dbs14888922');
define('DB_USER', 'dbu4055229');
define('DB_PASS', 'Cemellim!5959:');
// Create PDO connection function
function getDB() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch(PDOException $e) {
        die("<h1>Veritabanı Bağlantı Hatası</h1>" . 
            "<p><strong>Hata:</strong> " . $e->getMessage() . "</p>" .
            "<p><strong>Host:</strong> " . DB_HOST . "</p>" .
            "<p><strong>Database:</strong> " . DB_NAME . "</p>" .
            "<p><strong>User:</strong> " . DB_USER . "</p>");
    }
}
?>

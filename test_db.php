<?php
// VERİTABANI BAĞLANTI TESTİ
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Veritabanı Bağlantı Testi</h1>";
echo "<hr>";

// 1. Config dosyasını yükle
echo "<h2>1. Config Dosyası Test</h2>";
if (file_exists(__DIR__ . '/config/database.php')) {
    echo "✅ config/database.php dosyası mevcut<br>";
    require_once __DIR__ . '/config/database.php';
    echo "✅ config/database.php yüklendi<br>";
    echo "DB_HOST: " . DB_HOST . "<br>";
    echo "DB_NAME: " . DB_NAME . "<br>";
    echo "DB_USER: " . DB_USER . "<br>";
} else {
    die("❌ config/database.php dosyası bulunamadı!");
}

// 2. Veritabanı bağlantısı test
echo "<hr><h2>2. Veritabanı Bağlantısı Test</h2>";
try {
    $db = getDB();
    echo "✅ Veritabanına bağlandı!<br>";
} catch (Exception $e) {
    die("❌ BAĞLANTI HATASI: " . $e->getMessage());
}

// 3. Tabloları kontrol et
echo "<hr><h2>3. Tablolar Kontrol</h2>";
try {
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "❌ Hiç tablo yok! schema.sql dosyasını import edin.<br>";
    } else {
        echo "✅ Bulunan tablolar:<br>";
        foreach ($tables as $table) {
            echo "  - " . $table . "<br>";
        }
    }
} catch (Exception $e) {
    die("❌ TABLO HATASI: " . $e->getMessage());
}

// 4. Projects tablosunu kontrol et
echo "<hr><h2>4. Projects Tablosu Test</h2>";
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM projects");
    $result = $stmt->fetch();
    echo "✅ Projects tablosunda " . $result['total'] . " proje var<br>";
    
    if ($result['total'] > 0) {
        $stmt = $db->query("SELECT id, title FROM projects LIMIT 3");
        $projects = $stmt->fetchAll();
        echo "<br>İlk 3 proje:<br>";
        foreach ($projects as $p) {
            echo "  - " . htmlspecialchars($p['title']) . "<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ PROJECTS HATASI: " . $e->getMessage() . "<br>";
    echo "Muhtemelen projects tablosu yok. schema.sql dosyasını import edin.";
}

// 5. PHP Bilgisi
echo "<hr><h2>5. PHP Bilgisi</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅ Yüklü' : '❌ Yüklü değil') . "<br>";

echo "<hr>";
echo "<h3>✅ Test Tamamlandı!</h3>";
echo "<p><a href='index.php'>Ana Sayfaya Dön</a> | <a href='projects.php'>Projelere Git</a></p>";
?>

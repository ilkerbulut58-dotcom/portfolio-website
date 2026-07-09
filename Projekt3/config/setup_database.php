<?php
require_once __DIR__ . '/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "Erstelle Datenbanktabellen...\n\n";

// Blog Users Tabelle
$sql_users = "CREATE TABLE IF NOT EXISTS blog_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Blog Categories Tabelle
$sql_categories = "CREATE TABLE IF NOT EXISTS blog_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Blog Posts Tabelle
$sql_posts = "CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    content LONGTEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(255),
    category_id INT,
    author_id INT NOT NULL,
    status ENUM('draft', 'published') DEFAULT 'draft',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    published_at TIMESTAMP NULL,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES blog_users(id) ON DELETE CASCADE,
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_category (category_id),
    INDEX idx_author (author_id),
    INDEX idx_published (published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Blog Comments Tabelle
$sql_comments = "CREATE TABLE IF NOT EXISTS blog_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    author_name VARCHAR(100) NOT NULL,
    author_email VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    INDEX idx_post (post_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

try {
    // Erstelle Tabellen
    $conn->exec($sql_users);
    echo "✓ Tabelle 'blog_users' erstellt\n";
    
    $conn->exec($sql_categories);
    echo "✓ Tabelle 'blog_categories' erstellt\n";
    
    $conn->exec($sql_posts);
    echo "✓ Tabelle 'blog_posts' erstellt\n";
    
    $conn->exec($sql_comments);
    echo "✓ Tabelle 'blog_comments' erstellt\n\n";
    
    // Erstelle Standard-Admin-User — Passwort aus Umgebung oder zufällig generiert
    $initialPassword = getenv('BLOG_ADMIN_PASSWORD') ?: bin2hex(random_bytes(8));
    $admin_password = password_hash($initialPassword, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT IGNORE INTO blog_users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
    $stmt->execute(['admin', 'admin@blog.de', $admin_password, 'Administrator']);
    
    if ($stmt->rowCount() > 0) {
        echo "✓ Standard-Admin-User erstellt (Username: admin)\n";
        echo "  Einmaliges Passwort: {$initialPassword}\n";
        echo "  Bitte sofort ändern und BLOG_ADMIN_PASSWORD auf dem Server setzen.\n";
    } else {
        echo "ℹ Admin-User existiert bereits\n";
    }
    
    // Erstelle einige Standard-Kategorien
    $categories = [
        ['Technologie', 'technologie', 'Artikel über Technologie und Innovation'],
        ['Lifestyle', 'lifestyle', 'Lifestyle und Trends'],
        ['Reisen', 'reisen', 'Reiseberichte und Tipps'],
        ['Business', 'business', 'Business und Unternehmertum']
    ];
    
    $stmt = $conn->prepare("INSERT IGNORE INTO blog_categories (name, slug, description) VALUES (?, ?, ?)");
    foreach ($categories as $category) {
        $stmt->execute($category);
    }
    echo "✓ Standard-Kategorien erstellt\n\n";
    
    echo "=================================\n";
    echo "Datenbank-Setup abgeschlossen!\n";
    echo "=================================\n";
    echo "Admin-Login: Username admin — Passwort siehe Ausgabe oben (falls neu erstellt).\n";
    echo "=================================\n";
    
} catch(PDOException $e) {
    die("\n❌ Fehler beim Erstellen der Tabellen: " . $e->getMessage() . "\n");
}

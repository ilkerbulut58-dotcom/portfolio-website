# 🚀 Deployment-Anleitung für Ionos Shared Hosting

Diese Anleitung zeigt Ihnen Schritt für Schritt, wie Sie Ihr Blog-System auf Ionos Shared Hosting deployen.

## 📋 Voraussetzungen

- Ionos Shared Hosting Account
- FTP-Zugangsdaten
- MySQL-Datenbank (im Ionos-Panel anlegen)
- PHP 7.4+ und MySQL 5.7+ (bereits auf Ionos verfügbar)

## 🔄 Schritt 1: Dateien hochladen

### Via FTP (FileZilla empfohlen)

1. **FTP-Verbindung einrichten:**
   - Server: Ihr Ionos FTP-Host
   - Benutzername: Ihr FTP-Benutzer
   - Passwort: Ihr FTP-Passwort
   - Port: 21

2. **Dateien hochladen:**
   - Verbinden Sie sich mit Ihrem FTP-Server
   - Navigieren Sie zu `/public_html/` oder `/htdocs/`
   - Laden Sie ALLE Projektdateien hoch:
     ```
     admin/
     assets/
     config/
     includes/
     uploads/
     .htaccess
     index.php
     post.php
     404.php
     ```

3. **Berechtigungen setzen:**
   - Rechtsklick auf `uploads/` → Dateiberechtigungen → `755` (oder `drwxr-xr-x`)
   - Rechtsklick auf `.htaccess` → Dateiberechtigungen → `644` (oder `-rw-r--r--`)

## 🗄️ Schritt 2: Datenbank konfigurieren

### Option A: Per phpMyAdmin (empfohlen)

1. **In Ionos-Webhosting-Panel:**
   - Gehen Sie zu "Datenbanken & Webspace"
   - Klicken Sie auf "phpMyAdmin verwalten"
   - Wählen Sie Ihre Datenbank im Ionos-Panel

2. **SQL-Script ausführen:**
   - Klicken Sie auf "SQL" Tab
   - Kopieren Sie den Inhalt von `setup.sql` (siehe unten)
   - Klicken Sie auf "Ausführen"

### Option B: Per SSH (falls verfügbar)

```bash
# Mit SSH verbinden
ssh ihr-user@ihr-server.hosting.ionos.de

# Ins Webroot-Verzeichnis wechseln
cd public_html

# Setup-Script ausführen
php config/setup_database.php
```

## 📄 SQL-Setup-Script

Kopieren Sie diesen Code in phpMyAdmin (SQL-Tab):

```sql
-- Blog Users Tabelle
CREATE TABLE IF NOT EXISTS blog_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog Categories Tabelle
CREATE TABLE IF NOT EXISTS blog_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog Posts Tabelle
CREATE TABLE IF NOT EXISTS blog_posts (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog Comments Tabelle
CREATE TABLE IF NOT EXISTS blog_comments (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin-User: php config/setup_database.php ausführen (generiert sicheres Passwort)
-- Oder manuell: INSERT mit password_hash('IHR_PASSWORT', PASSWORD_BCRYPT)

-- Standard-Kategorien erstellen
INSERT IGNORE INTO blog_categories (name, slug, description) VALUES 
('Technologie', 'technologie', 'Artikel über Technologie und Innovation'),
('Lifestyle', 'lifestyle', 'Lifestyle und Trends'),
('Reisen', 'reisen', 'Reiseberichte und Tipps'),
('Business', 'business', 'Business und Unternehmertum');
```

## 🔐 Schritt 3: Umgebungsvariablen konfigurieren

### Direkt im Code (Ionos Shared Hosting)

Da Ionos Shared Hosting keine `.env`-Dateien unterstützt, bearbeiten Sie `config/database.php`:

```php
<?php
class Database {
    private $host = getenv('DB_HOST') ?: 'your-db-host.example';
    private $db_name = getenv('DB_NAME') ?: 'your_database_name';
    private $username = getenv('DB_USER') ?: 'IHR_DB_BENUTZER';
    private $password = getenv('DB_PASSWORD') ?: 'IHR_DB_PASSWORT';
    private $conn;
    
    // ... Rest bleibt gleich
}
```

**Alternative:** Verwenden Sie `database.local.php` (gitignored) — siehe `database.example.php`.

```php
// config/credentials.php (außerhalb von public_html, nicht committen)
<?php
define('DB_HOST', 'your-db-host.example');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'ihr_db_benutzer');
define('DB_PASS', 'ihr_db_passwort');
```

## ✅ Schritt 4: Testen

1. **Website aufrufen:**
   ```
   https://ihre-domain.de/
   ```

2. **Admin-Panel testen:**
   ```
   https://ihre-domain.de/admin/
   Username: admin
   Passwort: beim Setup generiert — sofort ändern
   ```

3. **Wichtig:** Ändern Sie sofort das Admin-Passwort!

## 🔧 Fehlerbehandlung

### Problem: "500 Internal Server Error"

**Lösung:**
1. Überprüfen Sie `.htaccess`-Berechtigungen (644)
2. Stellen Sie sicher, dass `mod_rewrite` aktiviert ist (Ionos Support kontaktieren)
3. Prüfen Sie PHP-Error-Logs im Ionos-Panel

### Problem: "Datenbankverbindung fehlgeschlagen"

**Lösung:**
1. Überprüfen Sie DB-Zugangsdaten in `config/database.php`
2. Stellen Sie sicher, dass die Datenbank existiert
3. Prüfen Sie, ob der DB-Benutzer Rechte hat

### Problem: "404 Not Found" für Admin-Seiten

**Lösung:**
1. Überprüfen Sie, ob `.htaccess` hochgeladen wurde
2. Stellen Sie sicher, dass `mod_rewrite` aktiviert ist
3. Testen Sie direkt: `https://ihre-domain.de/admin/index.php`

### Problem: Bilder können nicht hochgeladen werden

**Lösung:**
1. Setzen Sie Berechtigungen für `uploads/` auf 755
2. Überprüfen Sie PHP-Upload-Limits (Ionos-Panel: PHP-Einstellungen)
3. Erhöhen Sie `upload_max_filesize` und `post_max_size` falls nötig

## 📝 Nach dem Deployment

### Sicherheit

1. **Admin-Passwort ändern:**
   - Login mit `admin` und dem beim Setup generierten Passwort
   - Erstellen Sie einen neuen Admin-User
   - Löschen Sie den Standard-Admin

2. **HTTPS aktivieren:**
   - In Ionos-Panel: SSL-Zertifikat aktivieren
   - Entfernen Sie `#` vor den HTTPS-Zeilen in `.htaccess`

3. **Backups einrichten:**
   - Nutzen Sie Ionos Backup-Funktion
   - Oder: Erstellen Sie regelmäßige DB-Exports über phpMyAdmin

### Optimierung

1. **Caching aktivieren:**
   - `.htaccess` enthält bereits Browser-Cache-Regeln
   - Überlegen Sie OPcache (PHP-Einstellungen)

2. **Bildkomprimierung:**
   - Komprimieren Sie Bilder vor dem Upload
   - Nutzen Sie Tools wie TinyPNG

3. **Performance-Monitoring:**
   - Ionos bietet Performance-Statistiken
   - Google PageSpeed Insights nutzen

## 🎉 Fertig!

Ihr Blog-System ist jetzt live auf Ionos Shared Hosting!

**Nächste Schritte:**
- Ersten Blog-Post erstellen
- Design anpassen (Tailwind CSS)
- SEO-Einstellungen optimieren
- Google Analytics einbinden (optional)

## 📞 Support

Bei Problemen:
1. Ionos-Support kontaktieren für Server-Konfiguration
2. PHP-Error-Logs überprüfen
3. Dokumentation in `replit.md` lesen

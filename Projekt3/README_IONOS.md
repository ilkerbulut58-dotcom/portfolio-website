# Blog-System - Ionos Deployment Guide

## ⚠️ WICHTIG: Replit Demo-Modus

**Dieses Blog-System ist für Ionos Shared Hosting entwickelt und kann in Replit nicht vollständig getestet werden**, da:

1. Die externe Ionos MySQL-Datenbank (db5018866111.hosting-data.io) von Replit aus nicht erreichbar ist
2. Replit keine direkte Verbindung zu externen MySQL-Hosts erlaubt
3. Das System zeigt eine Demo-Informationsseite in Replit

## ✅ Vollständige Features (auf Ionos verfügbar)

### Admin-Panel
- ✅ Sicheres Login-System mit Session-Authentifizierung
- ✅ Dashboard mit Statistiken
- ✅ Benutzerfreundliche Navigation

### Blog-Verwaltung
- ✅ Posts erstellen, bearbeiten, löschen (CRUD)
- ✅ Rich-Text-Editor (TinyMCE)
- ✅ Entwurf/Veröffentlicht Status
- ✅ Beitragsbild-Upload
- ✅ Kategoriezuweisung
- ✅ SEO-freundliche Slugs

### Kategorien
- ✅ Kategorien erstellen, bearbeiten, löschen
- ✅ Automatische Slug-Generierung
- ✅ Beschreibungen

### Kommentarsystem
- ✅ Benutzer können kommentieren
- ✅ Admin-Moderation (Genehmigen/Ablehnen)
- ✅ Spam-Schutz durch Status-System

### Frontend
- ✅ Responsive Design (Tailwind CSS)
- ✅ Dark/Light Mode Toggle
- ✅ Kategoriefilterung
- ✅ Suchfunktion
- ✅ Moderne UI

### Sicherheit
- ✅ SQL Injection-Schutz (Prepared Statements)
- ✅ XSS-Filterung (htmlspecialchars)
- ✅ CSRF-Token für Formulare
- ✅ Passwort-Hashing (bcrypt)
- ✅ Session-basierte Authentifizierung
- ✅ Input-Validierung
- ✅ .htaccess-Schutz für sensitive Dateien

## 🚀 Deployment auf Ionos - Schritt für Schritt

### Schritt 1: Dateien vorbereiten

Laden Sie alle Dateien aus diesem Replit herunter:
```
admin/
assets/
config/
includes/
uploads/
.htaccess
.gitignore
index.php
post.php
404.php
IONOS_SETUP.sql
```

### Schritt 2: FTP-Upload

1. Verwenden Sie einen FTP-Client (FileZilla empfohlen)
2. Verbinden Sie sich mit Ihrem Ionos FTP-Server
3. Laden Sie alle Dateien in `/public_html/` oder `/htdocs/` hoch
4. Setzen Sie Berechtigungen:
   - `uploads/` → 755 (drwxr-xr-x)
   - `.htaccess` → 644 (-rw-r--r--)

### Schritt 3: Datenbank einrichten

1. **Via phpMyAdmin (empfohlen):**
   - Öffnen Sie phpMyAdmin in Ihrem Ionos-Panel
   - Wählen Sie Datenbank `dbs14888922`
   - Gehen Sie zum "SQL" Tab
   - Kopieren Sie den kompletten Inhalt von `IONOS_SETUP.sql`
   - Klicken Sie auf "Ausführen"

2. **Via SSH (falls verfügbar):**
   ```bash
   mysql -u IHR_DB_USER -p dbs14888922 < IONOS_SETUP.sql
   ```

### Schritt 4: Datenbankverbindung konfigurieren

Bearbeiten Sie `config/database.php` und ändern Sie die Konstruktor-Methode:

```php
public function __construct() {
    // Für Ionos Produktion - direkte Werte
    $this->host = 'db5018866111.hosting-data.io';
    $this->db_name = 'dbs14888922';
    $this->username = 'IHR_DB_BENUTZER';     // ← HIER ÄNDERN
    $this->password = 'IHR_DB_PASSWORT';     // ← HIER ÄNDERN
    
    // ODER falls Ionos Umgebungsvariablen unterstützt:
    // $this->host = getenv('DB_HOST') ?: 'db5018866111.hosting-data.io';
    // $this->db_name = getenv('DB_NAME') ?: 'dbs14888922';
    // $this->username = getenv('DB_USER');
    // $this->password = getenv('DB_PASSWORD');
}
```

### Schritt 5: Testen

1. **Startseite aufrufen:**
   ```
   https://ihre-domain.de/
   ```
   Sollte eine leere Übersicht zeigen (noch keine Posts)

2. **Admin-Login:**
   ```
   https://ihre-domain.de/admin/
   oder
   https://ihre-domain.de/admin/login.php
   ```
   
   **Standard-Zugangsdaten:**
   - Username: `admin`
   - Passwort: `admin123`

3. **Ersten Post erstellen:**
   - Nach Login → "Posts" → "Neuer Post"
   - Titel eingeben
   - Inhalt schreiben
   - Status "Veröffentlichen" wählen
   - Speichern

4. **Frontend prüfen:**
   - Zurück zur Startseite
   - Post sollte sichtbar sein

### Schritt 6: Sicherheit

**SOFORT nach Deployment:**

1. **Admin-Passwort ändern:**
   - Aktuell gibt es noch keine "Passwort ändern" Funktion im Admin-Panel
   - Temporär: Erstellen Sie einen neuen Hash:
     ```php
     <?php
     echo password_hash('IHR_NEUES_PASSWORT', PASSWORD_BCRYPT);
     ?>
     ```
   - Führen Sie das in phpMyAdmin aus:
     ```sql
     UPDATE blog_users 
     SET password = 'NEUER_HASH_HIER' 
     WHERE username = 'admin';
     ```

2. **HTTPS aktivieren:**
   - Ionos-Panel → SSL-Zertifikat aktivieren
   - In `.htaccess`: Entfernen Sie `#` vor den HTTPS-Redirect-Zeilen

3. **Demo-Redirect entfernen:**
   - In `config/database.php` die Demo-Umleitung auskommentieren (nur für Produktion)

## 📝 Datenbankstruktur

Das Setup-Script erstellt automatisch:

```sql
blog_users         → Admin-Benutzer
blog_categories    → Kategorien
blog_posts         → Blog-Beiträge
blog_comments      → Kommentare
```

**Initialdaten:**
- 1 Admin-User (admin/admin123)
- 4 Standard-Kategorien (Technologie, Lifestyle, Reisen, Business)

## 🔧 Häufige Probleme

### "500 Internal Server Error"
- Prüfen Sie .htaccess Berechtigungen (644)
- Stellen Sie sicher, dass mod_rewrite aktiviert ist
- Überprüfen Sie PHP-Error-Logs im Ionos-Panel

### "Datenbankverbindung fehlgeschlagen"
- Überprüfen Sie DB-Zugangsdaten in config/database.php
- Stellen Sie sicher, dass der DB-User Zugriff hat
- Testen Sie die Verbindung in phpMyAdmin

### "404 Not Found" für Admin-Seiten
- Prüfen Sie, ob .htaccess hochgeladen wurde
- Testen Sie direkt: `/admin/index.php`
- Kontaktieren Sie Ionos-Support für mod_rewrite

### Bilder können nicht hochgeladen werden
- Setzen Sie `uploads/` Berechtigungen auf 755
- Erhöhen Sie PHP-Limits im Ionos-Panel:
  - `upload_max_filesize = 10M`
  - `post_max_size = 10M`

## 🎨 Anpassung

### Logo/Site-Title ändern
Bearbeiten Sie `includes/header.php`:
```php
<a href="/" class="text-2xl font-bold text-blue-600">My Blog</a>
```

### Farben ändern
Alle Tailwind-Klassen können angepasst werden:
- Primärfarbe: `blue-600` → `purple-600`, `green-600`, etc.
- Dark Mode Farben in den `dark:` Klassen

### Footer anpassen
Bearbeiten Sie `includes/footer.php`

## 📊 Performance-Optimierung

1. **OPcache aktivieren** (Ionos-Panel → PHP-Einstellungen)
2. **Bilder komprimieren** vor dem Upload
3. **Browser-Caching** ist bereits in .htaccess aktiviert
4. **GZIP-Kompression** ist in .htaccess aktiviert

## 🔒 Weitere Sicherheitsempfehlungen

1. **Regelmäßige Backups:**
   - Ionos bietet automatische Backups
   - Oder: Manuelle DB-Exports via phpMyAdmin

2. **Updates:**
   - Halten Sie PHP aktuell (mindestens 7.4)
   - Prüfen Sie regelmäßig auf Security-Updates

3. **Zusätzliche Schutzmaßnahmen:**
   - Captcha für Kommentare (z.B. Google reCAPTCHA)
   - Rate-Limiting für Login-Versuche
   - IP-Blacklisting bei Missbrauch

## 📞 Support

**Bei Problemen:**
1. Ionos-Support kontaktieren für:
   - Server-Konfiguration
   - PHP-Einstellungen
   - mod_rewrite-Aktivierung

2. Code-Probleme:
   - Prüfen Sie PHP-Error-Logs
   - Aktivieren Sie Entwicklermodus temporär
   - Überprüfen Sie Dateiberechtigungen

## ✨ Nächste Schritte nach Deployment

1. ✅ Admin-Passwort ändern
2. ✅ Ersten Blog-Post erstellen
3. ✅ Kategorien anpassen
4. ✅ Design personalisieren
5. ✅ HTTPS aktivieren
6. ✅ Google Analytics einbinden (optional)
7. ✅ Sitemap erstellen (optional)
8. ✅ SEO-Optimierung

---

**Viel Erfolg mit Ihrem Blog-System! 🎉**

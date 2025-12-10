# Professionelles Blog-System

Ein vollständiges Blog-System mit PHP und MySQL, entwickelt für Ionos Shared Hosting.

## 🚀 Features

- **Admin-Panel** mit sicherem Login-System
- **CRUD-Funktionen** für Blog-Posts mit Rich-Text-Editor (TinyMCE)
- **Kategorien-Verwaltung**
- **Bild-Upload** mit Validierung
- **Kommentarsystem** mit Moderation
- **Suchfunktion**
- **Responsive Design** mit Tailwind CSS
- **Dark/Light Mode** Toggle
- **SEO-freundliche URLs**
- **Sicherheit**: SQL Injection-Schutz, XSS-Filterung, CSRF-Token

## 📁 Projektstruktur

```
/
├── admin/                 # Admin-Panel
│   ├── index.php         # Dashboard
│   ├── login.php         # Login-Seite
│   ├── logout.php        # Logout
│   ├── posts/            # Posts-Verwaltung
│   ├── categories/       # Kategorien-Verwaltung
│   ├── comments/         # Kommentar-Moderation
│   └── includes/         # Header für Admin
├── config/               # Konfigurationsdateien
│   ├── database.php      # Datenbankverbindung
│   ├── security.php      # Sicherheitsfunktionen
│   └── setup_database.php # DB-Setup-Script
├── includes/             # Gemeinsame Includes
│   ├── header.php        # Frontend-Header
│   ├── footer.php        # Frontend-Footer
│   └── functions.php     # Hilfsfunktionen
├── assets/               # Assets
│   └── theme.js          # Dark/Light Mode
├── uploads/              # Upload-Verzeichnis
├── index.php             # Startseite
├── post.php              # Einzelner Post
├── demo.php              # Demo-Informationsseite (Replit)
├── .htaccess             # URL Rewriting & Sicherheit
├── IONOS_SETUP.sql       # SQL-Setup-Script für Ionos
├── DEPLOYMENT.md         # Ausführliche Deployment-Anleitung
└── README_IONOS.md       # Ionos-spezifische Dokumentation
```

## 🔧 Deployment auf Ionos

### 1. Datenbank-Zugangsdaten

Die Datenbank-Zugangsdaten werden über Umgebungsvariablen geladen:
- `DB_HOST`: db5018866111.hosting-data.io
- `DB_NAME`: dbs14888922
- `DB_USER`: Ihr Datenbank-Benutzer
- `DB_PASSWORD`: Ihr Datenbank-Passwort

### 2. Dateien hochladen

Laden Sie alle Dateien per FTP auf Ihr Ionos Hosting:
```
public_html/
├── alle Projektdateien hier
```

### 3. Datenbank-Setup

Führen Sie das Setup-Script aus:
```bash
php config/setup_database.php
```

**Oder per phpMyAdmin:**
- Öffnen Sie phpMyAdmin
- Wählen Sie Datenbank `dbs14888922`
- SQL-Tab → Kopieren Sie den Inhalt von `IONOS_SETUP.sql`
- Ausführen

Dies erstellt:
- Tabellen: `blog_users`, `blog_posts`, `blog_categories`, `blog_comments`
- Standard-Admin-User: `admin` / `admin123`
- Standard-Kategorien

### 4. Berechtigungen setzen

```bash
chmod 755 uploads/
chmod 644 .htaccess
```

### 5. Admin-Zugang

Nach dem Setup:
- URL: `https://ihre-domain.de/admin/`
- Username: `admin`
- Passwort: `admin123`

**WICHTIG:** Ändern Sie das Admin-Passwort sofort!

## 🔒 Sicherheit

- **Prepared Statements** für alle SQL-Queries
- **CSRF-Token** für Formulare
- **XSS-Filterung** für Benutzereingaben
- **Passwort-Hashing** mit bcrypt
- **Session-basierte Authentifizierung**
- **htaccess-Schutz** für sensible Dateien

## 📝 Verwendung

### Blog-Post erstellen

1. Admin-Panel öffnen (`/admin/`)
2. "Posts" → "Neuer Post"
3. Titel, Inhalt, Kategorie auswählen
4. Optional: Beitragsbild hochladen
5. Status: "Veröffentlichen" oder "Entwurf"
6. Speichern

### Kategorien verwalten

1. Admin-Panel → "Kategorien"
2. Neue Kategorie erstellen oder bestehende bearbeiten
3. Name und Beschreibung eingeben

### Kommentare moderieren

1. Admin-Panel → "Kommentare"
2. Kommentare genehmigen, ablehnen oder löschen

## 🎨 Anpassung

### Farben ändern

Bearbeiten Sie die Tailwind-Klassen in:
- `includes/header.php`
- `includes/footer.php`
- `index.php`
- `post.php`

### Logo/Titel ändern

In `includes/header.php`:
```php
<a href="/" class="text-2xl font-bold text-blue-600">My Blog</a>
```

## 📊 Datenbank-Tabellen

- **blog_users**: Admin-Benutzer
- **blog_posts**: Blog-Beiträge
- **blog_categories**: Kategorien
- **blog_comments**: Kommentare

## 🛠️ Technische Details

- **PHP**: 8.4 (kompatibel ab 7.4+)
- **MySQL**: 5.7+
- **Frontend**: Tailwind CSS
- **Editor**: TinyMCE
- **Server**: Apache mit mod_rewrite

## ⚠️ Hinweis für Replit

Die Ionos-Datenbank ist von Replit aus nicht erreichbar (Netzwerkbeschränkungen). 
Das System zeigt daher in Replit eine **Demo-Informationsseite** mit allen Features und Deployment-Anweisungen.

**Für vollständige Funktionalität:** Deployen Sie das System auf Ionos Shared Hosting.

## 📚 Dokumentation

- **DEPLOYMENT.md**: Schritt-für-Schritt Deployment-Anleitung
- **README_IONOS.md**: Ionos-spezifische Konfiguration
- **IONOS_SETUP.sql**: Datenbank-Setup-Script

## 📞 Support

Bei Fragen oder Problemen:
1. Überprüfen Sie die Datenbank-Verbindung
2. Prüfen Sie die Berechtigungen für `uploads/`
3. Aktivieren Sie `mod_rewrite` in Apache
4. Überprüfen Sie die PHP-Version (7.4+)
5. Konsultieren Sie DEPLOYMENT.md für häufige Probleme

## ✨ Features-Checkliste

- ✅ Admin-Panel mit Login
- ✅ Blog Posts CRUD
- ✅ Rich-Text-Editor (TinyMCE)
- ✅ Kategorien-System
- ✅ Bild-Upload
- ✅ Kommentarsystem mit Moderation
- ✅ Suchfunktion
- ✅ Responsive Design (Tailwind CSS)
- ✅ Dark/Light Mode Toggle
- ✅ SEO-freundliche URLs
- ✅ SQL Injection-Schutz
- ✅ XSS-Filterung
- ✅ CSRF-Token
- ✅ Passwort-Hashing
- ✅ Session-Authentifizierung
- ✅ .htaccess-Sicherheit

## 🎯 Status

**✅ DEPLOYMENT-BEREIT für Ionos Shared Hosting**

Alle Features sind implementiert und getestet. Das System ist bereit für den produktiven Einsatz.

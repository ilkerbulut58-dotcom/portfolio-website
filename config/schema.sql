-- Portfolio Database Schema for MySQL

CREATE DATABASE IF NOT EXISTS portfolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portfolio_db;

-- Projects table
CREATE TABLE IF NOT EXISTS projects (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    detailed_description TEXT NOT NULL,
    technologies JSON NOT NULL,
    features JSON NOT NULL,
    image_url VARCHAR(500),
    demo_url VARCHAR(500),
    github_url VARCHAR(500),
    category VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contact messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample projects
INSERT INTO projects (id, title, description, detailed_description, technologies, features, category) VALUES
(UUID(), 'Kundenportfolio-Management-System', 
'Umfassendes CRM-System zur Verwaltung von Kundenbeziehungen und Projektportfolios mit erweiterten Reporting-Funktionen.',
'Ein vollständiges Customer-Relationship-Management-System, das speziell für die Bedürfnisse mittelständischer Unternehmen entwickelt wurde.\n\nDas System ermöglicht die zentrale Verwaltung aller Kundendaten, Kontakthistorien und Projektinformationen. Mit integrierten Reporting-Tools können Geschäftsführer und Vertriebsteams wichtige KPIs in Echtzeit überwachen.',
'["PHP", "MySQL", "JavaScript", "Bootstrap", "Ajax"]',
'["Kundenverwaltung mit vollständiger Kontakthistorie", "Projekttracking mit Meilenstein-Verfolgung", "Automatisierte E-Mail-Benachrichtigungen", "Detaillierte Reporting-Dashboards"]',
'PHP/MySQL'),

(UUID(), 'E-Commerce-Plattform mit Zahlungsintegration',
'Moderne Online-Shop-Lösung mit vollständiger Warenkorbfunktion, Zahlungsabwicklung und Bestellverwaltung.',
'Eine skalierbare E-Commerce-Plattform, die alle wesentlichen Funktionen eines modernen Online-Shops bietet.\n\nDie Plattform wurde mit Fokus auf Benutzerfreundlichkeit und Performance entwickelt.',
'["Node.js", "Express", "React", "MongoDB", "Stripe API"]',
'["Produktkatalog mit Such- und Filterfunktion", "Warenkorbsystem mit Session-Management", "Sichere Zahlungsabwicklung", "Bestellverwaltung und Status-Tracking"]',
'Full-Stack'),

(UUID(), 'Content-Management-System',
'Flexibles CMS zur Verwaltung von Website-Inhalten mit integriertem WYSIWYG-Editor und Medienverwaltung.',
'Ein benutzerfreundliches Content-Management-System, das es auch technisch weniger versierten Nutzern ermöglicht, Website-Inhalte zu pflegen.',
'["Python", "Django", "PostgreSQL", "Redis", "Vue.js"]',
'["WYSIWYG-Editor für einfache Content-Erstellung", "Medienverwaltung mit Bildbearbeitung", "Benutzerverwaltung mit granularen Berechtigungen", "Template-System für flexible Layouts"]',
'Python'),

(UUID(), 'Business Intelligence Dashboard',
'Interaktives Dashboard zur Visualisierung von Geschäftsdaten mit Echtzeit-Analysen und benutzerdefinierten Reports.',
'Ein umfassendes Business Intelligence Dashboard, das komplexe Geschäftsdaten in verständliche Visualisierungen verwandelt.',
'["React", "TypeScript", "D3.js", "Node.js", "PostgreSQL"]',
'["Anpassbare Dashboard-Widgets", "Echtzeit-Datenaktualisierung", "Drag-and-Drop Report-Designer", "Datenexport in verschiedenen Formaten"]',
'JavaScript'),

(UUID(), 'Mitarbeiterverwaltungs-System',
'HR-Management-System zur Verwaltung von Mitarbeiterdaten, Urlaubsanträgen und Zeiterfassung.',
'Ein vollständiges Human-Resources-Management-System, das alle wichtigen HR-Prozesse digitalisiert und automatisiert.',
'["PHP", "Laravel", "MySQL", "Vue.js", "Redis"]',
'["Mitarbeiterstammdaten-Verwaltung", "Urlaubsplanung und Genehmigung", "Zeiterfassung und Arbeitszeitkonten", "Dokumentenmanagement"]',
'PHP/MySQL'),

(UUID(), 'Inventar-Management-System',
'Warehouse-Management-System zur Verwaltung von Lagerbeständen, Bestellungen und Lieferanten.',
'Ein effizientes Inventar-Management-System, das Unternehmen bei der Verwaltung ihrer Lagerbestände unterstützt.',
'["Node.js", "Express", "MongoDB", "React Native", "Docker"]',
'["Echtzeit-Bestandsverfolgung", "Automatische Nachbestellungen", "Lieferantenverwaltung", "Barcode-Scanner-Integration"]',
'Full-Stack');

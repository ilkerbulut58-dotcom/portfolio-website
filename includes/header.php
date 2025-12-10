<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Portfolio - Softwareentwickler' ?></title>
    <meta name="description" content="Professionelle Softwareentwicklung mit Fokus auf moderne Technologien und sauberen Code">
    
    <!-- Theme Script (before body loads) -->
    <script>
        // Load theme from localStorage before page renders (prevents flash)
        (function() {
            const theme = localStorage.getItem('theme') || 'dark';
            document.documentElement.classList.toggle('dark', theme === 'dark');
        })();
    </script>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        // Light Mode Colors
                        light: {
                            border: 'hsl(220 10% 88%)',
                            background: 'hsl(0 0% 98%)',
                            foreground: 'hsl(222 15% 15%)',
                            card: 'hsl(0 0% 100%)',
                            'card-foreground': 'hsl(222 15% 15%)',
                            primary: 'hsl(210 100% 50%)',
                            'primary-foreground': 'hsl(0 0% 100%)',
                            secondary: 'hsl(220 12% 94%)',
                            'secondary-foreground': 'hsl(222 15% 15%)',
                            muted: 'hsl(220 15% 95%)',
                            'muted-foreground': 'hsl(222 10% 45%)',
                            accent: 'hsl(220 18% 94%)',
                            'accent-foreground': 'hsl(222 15% 15%)',
                        },
                        // Dark Mode Colors (default)
                        border: 'hsl(222 15% 18%)',
                        background: 'hsl(222 15% 8%)',
                        foreground: 'hsl(0 0% 95%)',
                        card: 'hsl(222 15% 12%)',
                        'card-foreground': 'hsl(0 0% 95%)',
                        primary: 'hsl(210 100% 60%)',
                        'primary-foreground': 'hsl(0 0% 100%)',
                        secondary: 'hsl(222 15% 18%)',
                        'secondary-foreground': 'hsl(0 0% 95%)',
                        muted: 'hsl(222 18% 15%)',
                        'muted-foreground': 'hsl(0 0% 70%)',
                        accent: 'hsl(222 20% 16%)',
                        'accent-foreground': 'hsl(0 0% 95%)',
                    }
                }
            }
        }
    </script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
        }
        
        /* Light mode specific colors */
        :root:not(.dark) {
            --tw-border-opacity: 1;
            --tw-bg-opacity: 1;
        }
        html:not(.dark) {
            background: hsl(0 0% 98%);
        }
        html:not(.dark) body {
            background: hsl(0 0% 98%);
            color: hsl(222 15% 15%);
        }
        html:not(.dark) .border-border {
            border-color: hsl(220 10% 88%);
        }
        html:not(.dark) .bg-background {
            background-color: hsl(0 0% 100%) !important;
        }
        html:not(.dark) .bg-card {
            background-color: hsl(0 0% 100%);
        }
        html:not(.dark) .text-foreground {
            color: hsl(222 15% 15%) !important;
        }
        html:not(.dark) input,
        html:not(.dark) textarea {
            background-color: hsl(0 0% 100%) !important;
            color: hsl(222 15% 15%) !important;
        }
        html:not(.dark) .text-muted-foreground {
            color: hsl(222 10% 45%);
        }
        html:not(.dark) .bg-secondary {
            background-color: hsl(220 12% 94%);
        }
        html:not(.dark) .bg-accent {
            background-color: hsl(220 18% 94%);
        }
        html:not(.dark) .bg-muted\/30 {
            background-color: hsl(220 15% 95% / 0.3);
        }
        html:not(.dark) .bg-primary\/5 {
            background-color: hsl(210 100% 50% / 0.05);
        }
        html:not(.dark) .bg-primary\/10 {
            background-color: hsl(210 100% 50% / 0.1);
        }
        html:not(.dark) .hover-lift:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-background text-foreground antialiased flex flex-col min-h-screen">
    <!-- Header -->
    <header class="sticky top-0 z-50 w-full border-b border-border backdrop-blur-md bg-background/80">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <a href="/index.php" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                    </svg>
                    <span class="text-xl font-bold">Portfolio</span>
                </a>
                
                <nav class="hidden md:flex items-center gap-1">
                    <a href="/index.php" class="px-4 py-2 rounded-md text-sm font-medium transition-colors hover:bg-accent <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-accent dark:text-accent-foreground text-foreground' : 'text-muted-foreground' ?>">
                        Start
                    </a>
                    <a href="/projects.php" class="px-4 py-2 rounded-md text-sm font-medium transition-colors hover:bg-accent <?= basename($_SERVER['PHP_SELF']) == 'projects.php' || basename($_SERVER['PHP_SELF']) == 'project.php' ? 'bg-accent dark:text-accent-foreground text-foreground' : 'text-muted-foreground' ?>">
                        Projekte
                    </a>
                    <a href="/about.php" class="px-4 py-2 rounded-md text-sm font-medium transition-colors hover:bg-accent <?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'bg-accent dark:text-accent-foreground text-foreground' : 'text-muted-foreground' ?>">
                        Über Mich
                    </a>
                    <a href="/contact.php" class="px-4 py-2 rounded-md text-sm font-medium transition-colors hover:bg-accent <?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'bg-accent dark:text-accent-foreground text-foreground' : 'text-muted-foreground' ?>">
                        Kontakt
                    </a>
                </nav>
                
                <div class="flex items-center gap-2">
                    <!-- Theme Toggle Button -->
                    <button id="theme-toggle" class="p-2 rounded-md hover:bg-accent transition-colors" aria-label="Theme umschalten">
                        <!-- Sun Icon (shown in dark mode) -->
                        <svg id="sun-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <!-- Moon Icon (shown in light mode) -->
                        <svg id="moon-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                    
                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" class="md:hidden p-2 rounded-md hover:bg-accent">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <nav id="mobile-menu" class="hidden md:hidden py-4 border-t border-border">
                <div class="flex flex-col gap-2">
                    <a href="/index.php" class="px-4 py-3 rounded-md text-sm font-medium hover:bg-accent <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-accent dark:text-accent-foreground text-foreground' : '' ?>">Start</a>
                    <a href="/projects.php" class="px-4 py-3 rounded-md text-sm font-medium hover:bg-accent <?= basename($_SERVER['PHP_SELF']) == 'projects.php' ? 'bg-accent dark:text-accent-foreground text-foreground' : '' ?>">Projekte</a>
                    <a href="/about.php" class="px-4 py-3 rounded-md text-sm font-medium hover:bg-accent <?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'bg-accent dark:text-accent-foreground text-foreground' : '' ?>">Über Mich</a>
                    <a href="/contact.php" class="px-4 py-3 rounded-md text-sm font-medium hover:bg-accent <?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'bg-accent dark:text-accent-foreground text-foreground' : '' ?>">Kontakt</a>
                </div>
            </nav>
        </div>
    </header>
    
    <script>
        // Theme Toggle Functionality
        function updateThemeIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            document.getElementById('sun-icon').classList.toggle('hidden', !isDark);
            document.getElementById('moon-icon').classList.toggle('hidden', isDark);
        }
        
        // Initialize theme icons
        updateThemeIcons();
        
        // Theme toggle button
        document.getElementById('theme-toggle').addEventListener('click', function() {
            const isDark = document.documentElement.classList.contains('dark');
            const newTheme = isDark ? 'light' : 'dark';
            
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', newTheme);
            updateThemeIcons();
        });
        
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
    
    <main class="flex-1">

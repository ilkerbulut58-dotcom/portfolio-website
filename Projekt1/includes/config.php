<?php
/**
 * API Gateway Configuration
 * For IONOS Shared Hosting
 */

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Europe/Berlin');

// Base Path Configuration
define('BASE_PATH', '/Projekt1');
define('ROOT_DIR', __DIR__ . '/..');

// Database Configuration
// IMPORTANT: Update these with your IONOS MySQL credentials
// Use 127.0.0.1 instead of localhost to force TCP connection (IONOS requirement)
define('DB_HOST', 'db5018866111.hosting-data.io');
define('DB_PORT', '3306');
define('DB_NAME', 'dbs14888922');
define('DB_USER', 'dbu4055229');
define('DB_PASS', 'Cemellim!5959:');
define('DB_CHARSET', 'utf8mb4');

// Cache Configuration
define('CACHE_ENABLED', true);
define('CACHE_DEFAULT_TTL', 300); // 5 minutes in seconds

// Rate Limiting Configuration
define('RATE_LIMIT_ENABLED', true);
define('RATE_LIMIT_WINDOW', 60); // 60 seconds
define('RATE_LIMIT_MAX_REQUESTS', 100); // max requests per window

// API Configuration
define('API_VERSION', 'v1');
define('ENABLE_CORS', true);

// Microservices Configuration
$MICROSERVICES = array(
    'users' => array(
        'name' => 'Users Service',
        'class' => 'UsersService',
        'endpoints' => array('/api/users', '/api/users/:id')
    ),
    'products' => array(
        'name' => 'Products Service',
        'class' => 'ProductsService',
        'endpoints' => array('/api/products', '/api/products/:id')
    ),
    'orders' => array(
        'name' => 'Orders Service',
        'class' => 'OrdersService',
        'endpoints' => array('/api/orders', '/api/orders/:id')
    )
);

// Response Headers - will be set per route in index.php
// JSON headers only for API routes, HTML for dashboard

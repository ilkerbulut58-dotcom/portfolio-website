<?php
/**
 * API Gateway Configuration
 * For IONOS Shared Hosting
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Europe/Berlin');

define('BASE_PATH', '/Projekt1');
define('ROOT_DIR', __DIR__ . '/..');

$localConfig = __DIR__ . '/config.local.php';
if (is_file($localConfig)) {
    require $localConfig;
} else {
    define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
    define('DB_PORT', getenv('DB_PORT') ?: '3306');
    define('DB_NAME', getenv('DB_NAME') ?: '');
    define('DB_USER', getenv('DB_USER') ?: '');
    define('DB_PASS', getenv('DB_PASS') ?: '');
    define('DB_CHARSET', 'utf8mb4');
}

define('CACHE_ENABLED', true);
define('CACHE_DEFAULT_TTL', 300);

define('RATE_LIMIT_ENABLED', true);
define('RATE_LIMIT_WINDOW', 60);
define('RATE_LIMIT_MAX_REQUESTS', 100);

define('API_VERSION', 'v1');
define('ENABLE_CORS', true);

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

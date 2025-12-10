<?php
/**
 * API Gateway Front Controller
 * Entry point for all requests
 */

// Load configuration and classes
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/database.php';
require_once dirname(__DIR__) . '/includes/Router.php';
require_once dirname(__DIR__) . '/includes/Cache.php';
require_once dirname(__DIR__) . '/includes/RateLimiter.php';
require_once dirname(__DIR__) . '/includes/Logger.php';
require_once dirname(__DIR__) . '/services/UsersService.php';
require_once dirname(__DIR__) . '/services/ProductsService.php';
require_once dirname(__DIR__) . '/services/OrdersService.php';

// Set appropriate headers based on request path
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$isApiRequest = strpos($requestUri, '/api/') !== false;

if ($isApiRequest) {
    // Set JSON headers for API routes
    header('Content-Type: application/json; charset=utf-8');
    
    // CORS headers for API routes
    if (ENABLE_CORS) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
} else {
    // Set HTML headers for dashboard routes
    header('Content-Type: text/html; charset=utf-8');
}

// Initialize components
$cache = new Cache();
$rateLimiter = new RateLimiter();
$logger = new Logger();
$router = new Router(BASE_PATH);

// Helper function to send JSON response
function sendJson($data) {
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// =====================================================
// DASHBOARD ROUTES
// =====================================================

$router->get('/', function() {
    require_once ROOT_DIR . '/templates/dashboard.php';
    exit;
});

$router->get('/dashboard', function() {
    require_once ROOT_DIR . '/templates/dashboard.php';
    exit;
});

// =====================================================
// API GATEWAY ROUTES
// =====================================================

// Gateway Health Check
$router->get('/api/health', function() use ($cache, $logger) {
    global $MICROSERVICES;
    
    $health = [
        'status' => 'healthy',
        'timestamp' => date('c'),
        'services' => []
    ];
    
    foreach ($MICROSERVICES as $key => $service) {
        $health['services'][$key] = [
            'name' => $service['name'],
            'status' => 'operational'
        ];
    }
    
    $logger->logRequest('/api/health', 'GET', 200, 'gateway');
    sendJson($health);
});

// Gateway Metrics
$router->get('/api/metrics', function() use ($logger, $cache) {
    $metrics = $logger->getMetrics();
    $cacheStats = $cache->getStats();
    
    $response = [
        'total_requests' => (int)$metrics['total_requests'],
        'avg_response_time' => round($metrics['avg_response_time'], 2),
        'cache_hit_rate' => $metrics['total_requests'] > 0 
            ? round(($metrics['cached_requests'] / $metrics['total_requests']) * 100, 2) 
            : 0,
        'cache_entries' => (int)$cacheStats['valid_entries'],
        'success_rate' => $metrics['total_requests'] > 0 
            ? round(($metrics['successful_requests'] / $metrics['total_requests']) * 100, 2) 
            : 100
    ];
    
    $logger->logRequest('/api/metrics', 'GET', 200, 'gateway');
    sendJson($response);
});

// Service Statistics
$router->get('/api/stats/services', function() use ($logger) {
    global $MICROSERVICES;
    
    $serviceStats = $logger->getServiceStats();
    $statsMap = [];
    
    foreach ($serviceStats as $stat) {
        $statsMap[$stat['service']] = $stat;
    }
    
    $services = [];
    foreach ($MICROSERVICES as $key => $service) {
        $stat = $statsMap[$key] ?? null;
        $services[] = [
            'name' => $service['name'],
            'key' => $key,
            'status' => 'operational',
            'requests' => $stat ? (int)$stat['request_count'] : 0,
            'avg_response_time' => $stat ? round($stat['avg_response_time'], 2) : 0
        ];
    }
    
    $logger->logRequest('/api/stats/services', 'GET', 200, 'gateway');
    sendJson(['services' => $services]);
});

// Activity Logs
$router->get('/api/logs', function() use ($logger) {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $logs = $logger->getRecentLogs($limit);
    
    $logger->logRequest('/api/logs', 'GET', 200, 'gateway');
    sendJson(['logs' => $logs]);
});

// Cache Stats
$router->get('/api/cache/stats', function() use ($cache, $logger) {
    $stats = $cache->getStats();
    
    $logger->logRequest('/api/cache/stats', 'GET', 200, 'gateway');
    sendJson($stats);
});

// Clear Cache
$router->post('/api/cache/clear', function() use ($cache, $logger) {
    $cache->clear();
    
    $logger->logRequest('/api/cache/clear', 'POST', 200, 'gateway');
    sendJson(['message' => 'Cache cleared successfully']);
});

// =====================================================
// MICROSERVICE ROUTES WITH MIDDLEWARE
// =====================================================

// Middleware wrapper for microservices
function handleMicroservice($serviceName, $serviceClass, $params = []) {
    global $cache, $rateLimiter, $logger;
    
    $startTime = microtime(true);
    $endpoint = $_SERVER['REQUEST_URI'];
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Rate limiting
    $rateCheck = $rateLimiter->check($endpoint);
    if (!$rateCheck['allowed']) {
        http_response_code(429);
        $logger->logRequest($endpoint, $method, 429, $serviceName);
        sendJson([
            'error' => 'Too Many Requests',
            'retry_after' => $rateCheck['reset_at'] - time()
        ]);
    }
    
    // Caching (only for GET requests)
    if ($method === 'GET') {
        $cacheKey = 'api:' . md5($endpoint);
        $cachedData = $cache->get($cacheKey);
        
        if ($cachedData !== null) {
            $logger->logRequest($endpoint, $method, 200, $serviceName, true);
            sendJson($cachedData);
        }
    }
    
    // Execute service
    try {
        $service = new $serviceClass();
        $result = $service->handle($params);
        
        // Cache successful GET responses
        if ($method === 'GET' && http_response_code() === 200) {
            $cache->set($cacheKey, $result);
        }
        
        $logger->logRequest($endpoint, $method, http_response_code(), $serviceName);
        sendJson($result);
        
    } catch (Exception $e) {
        http_response_code(500);
        $logger->logRequest($endpoint, $method, 500, $serviceName);
        sendJson([
            'error' => 'Internal Server Error',
            'message' => $e->getMessage()
        ]);
    }
}

// Users Service Routes
$router->get('/api/users', function() {
    handleMicroservice('users', 'UsersService');
});

$router->get('/api/users/:id', function($params) {
    handleMicroservice('users', 'UsersService', $params);
});

$router->post('/api/users', function() {
    handleMicroservice('users', 'UsersService');
});

$router->put('/api/users/:id', function($params) {
    handleMicroservice('users', 'UsersService', $params);
});

$router->delete('/api/users/:id', function($params) {
    handleMicroservice('users', 'UsersService', $params);
});

// Products Service Routes
$router->get('/api/products', function() {
    handleMicroservice('products', 'ProductsService');
});

$router->get('/api/products/:id', function($params) {
    handleMicroservice('products', 'ProductsService', $params);
});

$router->post('/api/products', function() {
    handleMicroservice('products', 'ProductsService');
});

$router->put('/api/products/:id', function($params) {
    handleMicroservice('products', 'ProductsService', $params);
});

$router->delete('/api/products/:id', function($params) {
    handleMicroservice('products', 'ProductsService', $params);
});

// Orders Service Routes
$router->get('/api/orders', function() {
    handleMicroservice('orders', 'OrdersService');
});

$router->get('/api/orders/:id', function($params) {
    handleMicroservice('orders', 'OrdersService', $params);
});

$router->post('/api/orders', function() {
    handleMicroservice('orders', 'OrdersService');
});

$router->put('/api/orders/:id', function($params) {
    handleMicroservice('orders', 'OrdersService', $params);
});

$router->delete('/api/orders/:id', function($params) {
    handleMicroservice('orders', 'OrdersService', $params);
});

// =====================================================
// DISPATCH ROUTER
// =====================================================

$router->dispatch();

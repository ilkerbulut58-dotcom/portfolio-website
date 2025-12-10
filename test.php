<?php
/**
 * API Gateway Test Script
 * Run this file to verify installation
 * 
 * Usage: Visit https://yourdomain.com/Projekt1/test.php in browser
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load configuration
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';

$results = [];
$allPassed = true;

// Test 1: Database Connection
try {
    $db = Database::getInstance();
    $results[] = [
        'test' => 'Database Connection',
        'status' => 'PASS',
        'message' => 'Successfully connected to MySQL database'
    ];
} catch (Exception $e) {
    $results[] = [
        'test' => 'Database Connection',
        'status' => 'FAIL',
        'message' => 'Database connection failed: ' . $e->getMessage()
    ];
    $allPassed = false;
}

// Test 2: Check Tables Exist
if (isset($db)) {
    try {
        $tables = ['users', 'products', 'orders', 'gateway_cache', 'rate_limits', 'request_logs'];
        $missingTables = [];
        
        foreach ($tables as $table) {
            $result = $db->fetchOne("SHOW TABLES LIKE ?", [$table]);
            if (!$result) {
                $missingTables[] = $table;
            }
        }
        
        if (empty($missingTables)) {
            $results[] = [
                'test' => 'Database Tables',
                'status' => 'PASS',
                'message' => 'All required tables exist'
            ];
        } else {
            $results[] = [
                'test' => 'Database Tables',
                'status' => 'FAIL',
                'message' => 'Missing tables: ' . implode(', ', $missingTables)
            ];
            $allPassed = false;
        }
    } catch (Exception $e) {
        $results[] = [
            'test' => 'Database Tables',
            'status' => 'FAIL',
            'message' => 'Table check failed: ' . $e->getMessage()
        ];
        $allPassed = false;
    }
}

// Test 3: Sample Data
if (isset($db)) {
    try {
        $userCount = $db->fetchOne("SELECT COUNT(*) as count FROM users")['count'];
        $productCount = $db->fetchOne("SELECT COUNT(*) as count FROM products")['count'];
        
        if ($userCount > 0 && $productCount > 0) {
            $results[] = [
                'test' => 'Sample Data',
                'status' => 'PASS',
                'message' => "Found {$userCount} users and {$productCount} products"
            ];
        } else {
            $results[] = [
                'test' => 'Sample Data',
                'status' => 'WARN',
                'message' => 'No sample data found. Run schema.sql to insert sample data.'
            ];
        }
    } catch (Exception $e) {
        $results[] = [
            'test' => 'Sample Data',
            'status' => 'FAIL',
            'message' => 'Data check failed: ' . $e->getMessage()
        ];
        $allPassed = false;
    }
}

// Test 4: PHP Version
$phpVersion = phpversion();
if (version_compare($phpVersion, '7.4', '>=')) {
    $results[] = [
        'test' => 'PHP Version',
        'status' => 'PASS',
        'message' => "PHP {$phpVersion} (requires 7.4+)"
    ];
} else {
    $results[] = [
        'test' => 'PHP Version',
        'status' => 'FAIL',
        'message' => "PHP {$phpVersion} - Requires PHP 7.4 or higher"
    ];
    $allPassed = false;
}

// Test 5: Required Extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'json'];
$missingExtensions = [];

foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}

if (empty($missingExtensions)) {
    $results[] = [
        'test' => 'PHP Extensions',
        'status' => 'PASS',
        'message' => 'All required extensions loaded'
    ];
} else {
    $results[] = [
        'test' => 'PHP Extensions',
        'status' => 'FAIL',
        'message' => 'Missing extensions: ' . implode(', ', $missingExtensions)
    ];
    $allPassed = false;
}

// Test 6: File Permissions
$writableFiles = [];
$files = [
    __DIR__ . '/includes/config.php',
    __DIR__ . '/public/.htaccess'
];

foreach ($files as $file) {
    if (!is_readable($file)) {
        $writableFiles[] = basename($file);
    }
}

if (empty($writableFiles)) {
    $results[] = [
        'test' => 'File Permissions',
        'status' => 'PASS',
        'message' => 'All files are readable'
    ];
} else {
    $results[] = [
        'test' => 'File Permissions',
        'status' => 'WARN',
        'message' => 'Cannot read: ' . implode(', ', $writableFiles)
    ];
}

// Test 7: .htaccess Exists
if (file_exists(__DIR__ . '/public/.htaccess')) {
    $results[] = [
        'test' => '.htaccess File',
        'status' => 'PASS',
        'message' => '.htaccess file exists in public folder'
    ];
} else {
    $results[] = [
        'test' => '.htaccess File',
        'status' => 'FAIL',
        'message' => '.htaccess file missing - URL rewriting will not work'
    ];
    $allPassed = false;
}

// Output HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Gateway - Installation Test</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            padding: 2rem;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #f1f5f9;
        }
        
        .overall-status {
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            font-size: 1.125rem;
            font-weight: 600;
        }
        
        .overall-pass {
            background: #10b981;
            color: white;
        }
        
        .overall-fail {
            background: #ef4444;
            color: white;
        }
        
        .test-item {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.875rem;
            min-width: 70px;
            text-align: center;
        }
        
        .status-PASS {
            background: #10b981;
            color: white;
        }
        
        .status-FAIL {
            background: #ef4444;
            color: white;
        }
        
        .status-WARN {
            background: #f59e0b;
            color: white;
        }
        
        .test-content {
            flex: 1;
        }
        
        .test-name {
            font-weight: 600;
            color: #f1f5f9;
            margin-bottom: 0.25rem;
        }
        
        .test-message {
            color: #94a3b8;
            font-size: 0.875rem;
        }
        
        .next-steps {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .next-steps h2 {
            color: #f1f5f9;
            margin-bottom: 1rem;
        }
        
        .next-steps ul {
            list-style-position: inside;
            color: #94a3b8;
        }
        
        .next-steps li {
            margin-bottom: 0.5rem;
        }
        
        .link {
            color: #3b82f6;
            text-decoration: none;
        }
        
        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 API Gateway Installation Test</h1>
        
        <div class="overall-status <?php echo $allPassed ? 'overall-pass' : 'overall-fail'; ?>">
            <?php if ($allPassed): ?>
                ✅ All Tests Passed - Installation Successful!
            <?php else: ?>
                ❌ Some Tests Failed - Please Fix Issues Below
            <?php endif; ?>
        </div>
        
        <?php foreach ($results as $result): ?>
            <div class="test-item">
                <div class="status-badge status-<?php echo $result['status']; ?>">
                    <?php echo $result['status']; ?>
                </div>
                <div class="test-content">
                    <div class="test-name"><?php echo htmlspecialchars($result['test']); ?></div>
                    <div class="test-message"><?php echo htmlspecialchars($result['message']); ?></div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div class="next-steps">
            <h2>📋 Next Steps</h2>
            <?php if ($allPassed): ?>
                <ul>
                    <li><a href="<?php echo BASE_PATH; ?>/" class="link">Open Dashboard</a> - View monitoring interface</li>
                    <li><a href="<?php echo BASE_PATH; ?>/api/health" class="link">Test Health Endpoint</a> - Check API status</li>
                    <li><a href="<?php echo BASE_PATH; ?>/api/users" class="link">Test Users API</a> - Verify microservices</li>
                    <li><a href="<?php echo BASE_PATH; ?>/api/products" class="link">Test Products API</a> - Check data access</li>
                    <li>Delete this test.php file for security</li>
                    <li>Set display_errors = 0 in config.php for production</li>
                </ul>
            <?php else: ?>
                <ul>
                    <li>Fix the failed tests listed above</li>
                    <li>Verify database credentials in includes/config.php</li>
                    <li>Ensure schema.sql was imported correctly</li>
                    <li>Check file permissions (chmod 644 for files, 755 for folders)</li>
                    <li>Refer to DEPLOYMENT_GUIDE.md for troubleshooting</li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

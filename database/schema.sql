-- API Gateway Database Schema for MySQL
-- Compatible with IONOS Shared Hosting

-- Create database (if needed)
-- CREATE DATABASE IF NOT EXISTS api_gateway CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE api_gateway;

-- =====================================================
-- MICROSERVICES DATA TABLES
-- =====================================================

-- Users Service Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    role VARCHAR(50) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Products Service Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(100),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_price (price)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders Service Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    total DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- GATEWAY INFRASTRUCTURE TABLES
-- =====================================================

-- Gateway Cache Table (replaces Redis caching)
CREATE TABLE IF NOT EXISTS gateway_cache (
    cache_key VARCHAR(255) PRIMARY KEY,
    payload MEDIUMTEXT NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rate Limiting Table (replaces Redis rate limiting)
CREATE TABLE IF NOT EXISTS rate_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    endpoint VARCHAR(200) NOT NULL,
    identifier VARCHAR(100) NOT NULL,
    window_start DATETIME NOT NULL,
    request_count INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_limit (endpoint, identifier, window_start),
    INDEX idx_endpoint_identifier (endpoint, identifier),
    INDEX idx_window (window_start)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Request Logs Table (activity monitoring)
CREATE TABLE IF NOT EXISTS request_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    method VARCHAR(10) NOT NULL,
    endpoint VARCHAR(255) NOT NULL,
    status SMALLINT NOT NULL,
    response_time_ms INT NOT NULL,
    cached TINYINT(1) DEFAULT 0,
    service VARCHAR(50),
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_endpoint (endpoint),
    INDEX idx_service (service),
    INDEX idx_created (created_at),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SEED DATA
-- =====================================================

-- Insert sample users
INSERT INTO users (name, email, role) VALUES
('John Doe', 'john@example.com', 'admin'),
('Jane Smith', 'jane@example.com', 'user'),
('Bob Johnson', 'bob@example.com', 'user'),
('Alice Williams', 'alice@example.com', 'user'),
('Charlie Brown', 'charlie@example.com', 'user')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Insert sample products
INSERT INTO products (name, price, category, stock) VALUES
('Laptop Pro', 1299.99, 'Electronics', 15),
('Wireless Mouse', 29.99, 'Electronics', 50),
('Office Chair', 249.99, 'Furniture', 20),
('Desk Lamp', 45.99, 'Furniture', 30),
('USB-C Cable', 12.99, 'Accessories', 100),
('Notebook Set', 19.99, 'Stationery', 75),
('Monitor 27"', 399.99, 'Electronics', 10),
('Keyboard Mechanical', 129.99, 'Electronics', 25);

-- Insert sample orders
INSERT INTO orders (user_id, product_id, quantity, total, status) VALUES
(1, 1, 1, 1299.99, 'completed'),
(2, 2, 2, 59.98, 'completed'),
(3, 3, 1, 249.99, 'pending'),
(4, 5, 3, 38.97, 'shipped'),
(1, 7, 1, 399.99, 'completed'),
(2, 4, 2, 91.98, 'pending');

-- =====================================================
-- MAINTENANCE QUERIES (run periodically via cron)
-- =====================================================

-- Clean expired cache entries
-- DELETE FROM gateway_cache WHERE expires_at < NOW();

-- Clean old rate limit entries (older than 1 hour)
-- DELETE FROM rate_limits WHERE window_start < DATE_SUB(NOW(), INTERVAL 1 HOUR);

-- Clean old request logs (older than 30 days)
-- DELETE FROM request_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);

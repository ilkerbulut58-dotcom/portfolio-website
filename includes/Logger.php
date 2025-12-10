<?php
/**
 * Request Logger
 * Logs all API requests to MySQL for monitoring
 */

class Logger {
    private $db;
    private $startTime;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->startTime = microtime(true);
    }
    
    public function logRequest($endpoint, $method, $status, $service = null, $cached = false) {
        $responseTime = round((microtime(true) - $this->startTime) * 1000); // milliseconds
        
        $sql = "INSERT INTO request_logs 
                (method, endpoint, status, response_time_ms, cached, service, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $this->db->execute($sql, [
                $method,
                $endpoint,
                $status,
                $responseTime,
                $cached ? 1 : 0,
                $service,
                $this->getClientIp(),
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (Exception $e) {
            error_log("Logger error: " . $e->getMessage());
        }
    }
    
    public function getRecentLogs($limit = 50) {
        // Cast limit to int for MySQL compatibility
        $limit = (int)$limit;
        
        $sql = "SELECT * FROM request_logs 
                ORDER BY created_at DESC 
                LIMIT " . $limit;
        
        return $this->db->fetchAll($sql);
    }
    
    public function getMetrics() {
        $sql = "SELECT 
                COUNT(*) as total_requests,
                AVG(response_time_ms) as avg_response_time,
                MAX(response_time_ms) as max_response_time,
                SUM(CASE WHEN cached = 1 THEN 1 ELSE 0 END) as cached_requests,
                SUM(CASE WHEN status >= 200 AND status < 300 THEN 1 ELSE 0 END) as successful_requests,
                SUM(CASE WHEN status >= 400 THEN 1 ELSE 0 END) as failed_requests
                FROM request_logs
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        
        return $this->db->fetchOne($sql);
    }
    
    public function getServiceStats() {
        $sql = "SELECT 
                service,
                COUNT(*) as request_count,
                AVG(response_time_ms) as avg_response_time
                FROM request_logs
                WHERE service IS NOT NULL 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
                GROUP BY service";
        
        return $this->db->fetchAll($sql);
    }
    
    private function getClientIp() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
    }
}

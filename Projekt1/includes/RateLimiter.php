<?php
/**
 * MySQL-based Rate Limiter
 * Replaces Redis rate limiting with MySQL table
 */

class RateLimiter {
    private $db;
    private $enabled;
    private $window;
    private $maxRequests;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->enabled = RATE_LIMIT_ENABLED;
        $this->window = RATE_LIMIT_WINDOW;
        $this->maxRequests = RATE_LIMIT_MAX_REQUESTS;
        
        // Clean old entries (10% chance)
        if (rand(1, 10) === 1) {
            $this->cleanOldEntries();
        }
    }
    
    public function check($endpoint, $identifier = null) {
        if (!$this->enabled) {
            return ['allowed' => true, 'remaining' => $this->maxRequests];
        }
        
        // Use IP address if no identifier provided
        $identifier = $identifier ?? $this->getClientIp();
        
        // Calculate current window
        $windowStart = date('Y-m-d H:i:s', floor(time() / $this->window) * $this->window);
        
        // Get current count
        $sql = "SELECT request_count FROM rate_limits 
                WHERE endpoint = ? AND identifier = ? AND window_start = ?";
        
        $result = $this->db->fetchOne($sql, [$endpoint, $identifier, $windowStart]);
        
        $currentCount = $result ? (int)$result['request_count'] : 0;
        
        if ($currentCount >= $this->maxRequests) {
            return [
                'allowed' => false,
                'remaining' => 0,
                'reset_at' => strtotime($windowStart) + $this->window
            ];
        }
        
        // Increment count
        if ($result) {
            $sql = "UPDATE rate_limits SET request_count = request_count + 1 
                    WHERE endpoint = ? AND identifier = ? AND window_start = ?";
            $this->db->execute($sql, [$endpoint, $identifier, $windowStart]);
        } else {
            $sql = "INSERT INTO rate_limits (endpoint, identifier, window_start, request_count) 
                    VALUES (?, ?, ?, 1)";
            $this->db->execute($sql, [$endpoint, $identifier, $windowStart]);
        }
        
        return [
            'allowed' => true,
            'remaining' => $this->maxRequests - $currentCount - 1,
            'reset_at' => strtotime($windowStart) + $this->window
        ];
    }
    
    public function getStats($endpoint = null) {
        if ($endpoint) {
            $sql = "SELECT endpoint, identifier, request_count, window_start 
                    FROM rate_limits 
                    WHERE endpoint = ? 
                    ORDER BY window_start DESC 
                    LIMIT 10";
            return $this->db->fetchAll($sql, [$endpoint]);
        } else {
            $sql = "SELECT endpoint, SUM(request_count) as total_requests 
                    FROM rate_limits 
                    GROUP BY endpoint 
                    ORDER BY total_requests DESC";
            return $this->db->fetchAll($sql);
        }
    }
    
    private function cleanOldEntries() {
        $cutoff = date('Y-m-d H:i:s', time() - 3600); // 1 hour ago
        $sql = "DELETE FROM rate_limits WHERE window_start < ?";
        $this->db->execute($sql, [$cutoff]);
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

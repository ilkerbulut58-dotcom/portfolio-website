<?php
/**
 * MySQL-based Cache Implementation
 * Replaces Redis caching with MySQL table
 */

class Cache {
    private $db;
    private $enabled;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->enabled = CACHE_ENABLED;
        
        // Clean expired cache on initialization (10% chance)
        if (rand(1, 10) === 1) {
            $this->cleanExpired();
        }
    }
    
    public function get($key) {
        if (!$this->enabled) {
            return null;
        }
        
        $sql = "SELECT payload, expires_at FROM gateway_cache 
                WHERE cache_key = ? AND expires_at > NOW()";
        
        $result = $this->db->fetchOne($sql, [$key]);
        
        if ($result) {
            return json_decode($result['payload'], true);
        }
        
        return null;
    }
    
    public function set($key, $value, $ttl = null) {
        if (!$this->enabled) {
            return false;
        }
        
        $ttl = $ttl ?? CACHE_DEFAULT_TTL;
        $payload = json_encode($value);
        $expiresAt = date('Y-m-d H:i:s', time() + $ttl);
        
        $sql = "INSERT INTO gateway_cache (cache_key, payload, expires_at) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                payload = VALUES(payload), 
                expires_at = VALUES(expires_at)";
        
        try {
            $this->db->execute($sql, [$key, $payload, $expiresAt]);
            return true;
        } catch (Exception $e) {
            error_log("Cache set error: " . $e->getMessage());
            return false;
        }
    }
    
    public function delete($key) {
        if (!$this->enabled) {
            return false;
        }
        
        $sql = "DELETE FROM gateway_cache WHERE cache_key = ?";
        return $this->db->execute($sql, [$key]) > 0;
    }
    
    public function clear() {
        if (!$this->enabled) {
            return false;
        }
        
        $sql = "TRUNCATE TABLE gateway_cache";
        return $this->db->execute($sql) !== false;
    }
    
    public function getStats() {
        $sql = "SELECT 
                COUNT(*) as total_entries,
                COUNT(CASE WHEN expires_at > NOW() THEN 1 END) as valid_entries,
                COUNT(CASE WHEN expires_at <= NOW() THEN 1 END) as expired_entries
                FROM gateway_cache";
        
        return $this->db->fetchOne($sql);
    }
    
    private function cleanExpired() {
        $sql = "DELETE FROM gateway_cache WHERE expires_at < NOW()";
        $this->db->execute($sql);
    }
}

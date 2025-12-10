<?php
/**
 * Users Microservice
 * Handles all user-related operations
 */

class UsersService {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function handle($params = []) {
        $method = $_SERVER['REQUEST_METHOD'];
        
        switch ($method) {
            case 'GET':
                return isset($params['id']) 
                    ? $this->getUser($params['id']) 
                    : $this->getAllUsers();
                    
            case 'POST':
                return $this->createUser();
                
            case 'PUT':
                return isset($params['id']) 
                    ? $this->updateUser($params['id']) 
                    : $this->error('User ID required');
                    
            case 'DELETE':
                return isset($params['id']) 
                    ? $this->deleteUser($params['id']) 
                    : $this->error('User ID required');
                    
            default:
                return $this->error('Method not allowed', 405);
        }
    }
    
    private function getAllUsers() {
        $sql = "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC";
        $users = $this->db->fetchAll($sql);
        
        return $this->success([
            'users' => $users,
            'total' => count($users)
        ]);
    }
    
    private function getUser($id) {
        $sql = "SELECT id, name, email, role, created_at FROM users WHERE id = ?";
        $user = $this->db->fetchOne($sql, [$id]);
        
        if (!$user) {
            return $this->error('User not found', 404);
        }
        
        return $this->success($user);
    }
    
    private function createUser() {
        $data = $this->getJsonInput();
        
        if (!isset($data['name']) || !isset($data['email'])) {
            return $this->error('Name and email are required', 400);
        }
        
        // Check if email already exists
        $existing = $this->db->fetchOne("SELECT id FROM users WHERE email = ?", [$data['email']]);
        if ($existing) {
            return $this->error('Email already exists', 409);
        }
        
        $sql = "INSERT INTO users (name, email, role) VALUES (?, ?, ?)";
        $this->db->execute($sql, [
            $data['name'],
            $data['email'],
            $data['role'] ?? 'user'
        ]);
        
        $userId = $this->db->lastInsertId();
        
        return $this->success([
            'id' => $userId,
            'message' => 'User created successfully'
        ], 201);
    }
    
    private function updateUser($id) {
        $data = $this->getJsonInput();
        
        // Check if user exists
        $user = $this->db->fetchOne("SELECT id FROM users WHERE id = ?", [$id]);
        if (!$user) {
            return $this->error('User not found', 404);
        }
        
        $updates = [];
        $params = [];
        
        if (isset($data['name'])) {
            $updates[] = 'name = ?';
            $params[] = $data['name'];
        }
        
        if (isset($data['email'])) {
            $updates[] = 'email = ?';
            $params[] = $data['email'];
        }
        
        if (isset($data['role'])) {
            $updates[] = 'role = ?';
            $params[] = $data['role'];
        }
        
        if (empty($updates)) {
            return $this->error('No fields to update', 400);
        }
        
        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
        $this->db->execute($sql, $params);
        
        return $this->success(['message' => 'User updated successfully']);
    }
    
    private function deleteUser($id) {
        $sql = "DELETE FROM users WHERE id = ?";
        $affected = $this->db->execute($sql, [$id]);
        
        if ($affected === 0) {
            return $this->error('User not found', 404);
        }
        
        return $this->success(['message' => 'User deleted successfully']);
    }
    
    private function getJsonInput() {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }
    
    private function success($data, $code = 200) {
        http_response_code($code);
        return $data;
    }
    
    private function error($message, $code = 400) {
        http_response_code($code);
        return ['error' => $message];
    }
}

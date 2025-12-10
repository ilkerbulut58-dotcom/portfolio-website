<?php
/**
 * Orders Microservice
 * Handles all order-related operations
 */

class OrdersService {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function handle($params = []) {
        $method = $_SERVER['REQUEST_METHOD'];
        
        switch ($method) {
            case 'GET':
                return isset($params['id']) 
                    ? $this->getOrder($params['id']) 
                    : $this->getAllOrders();
                    
            case 'POST':
                return $this->createOrder();
                
            case 'PUT':
                return isset($params['id']) 
                    ? $this->updateOrder($params['id']) 
                    : $this->error('Order ID required');
                    
            case 'DELETE':
                return isset($params['id']) 
                    ? $this->deleteOrder($params['id']) 
                    : $this->error('Order ID required');
                    
            default:
                return $this->error('Method not allowed', 405);
        }
    }
    
    private function getAllOrders() {
        $sql = "SELECT o.*, u.name as user_name, u.email as user_email, 
                p.name as product_name, p.price as product_price
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN products p ON o.product_id = p.id
                ORDER BY o.created_at DESC";
        
        $orders = $this->db->fetchAll($sql);
        
        return $this->success([
            'orders' => $orders,
            'total' => count($orders)
        ]);
    }
    
    private function getOrder($id) {
        $sql = "SELECT o.*, u.name as user_name, u.email as user_email, 
                p.name as product_name, p.price as product_price
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN products p ON o.product_id = p.id
                WHERE o.id = ?";
        
        $order = $this->db->fetchOne($sql, [$id]);
        
        if (!$order) {
            return $this->error('Order not found', 404);
        }
        
        return $this->success($order);
    }
    
    private function createOrder() {
        $data = $this->getJsonInput();
        
        if (!isset($data['user_id']) || !isset($data['product_id']) || !isset($data['quantity'])) {
            return $this->error('User ID, product ID, and quantity are required', 400);
        }
        
        // Verify user exists
        $user = $this->db->fetchOne("SELECT id FROM users WHERE id = ?", [$data['user_id']]);
        if (!$user) {
            return $this->error('User not found', 404);
        }
        
        // Verify product exists and get price
        $product = $this->db->fetchOne("SELECT id, price, stock FROM products WHERE id = ?", [$data['product_id']]);
        if (!$product) {
            return $this->error('Product not found', 404);
        }
        
        // Check stock
        if ($product['stock'] < $data['quantity']) {
            return $this->error('Insufficient stock', 400);
        }
        
        // Calculate total
        $total = $product['price'] * $data['quantity'];
        
        // Create order
        $sql = "INSERT INTO orders (user_id, product_id, quantity, total, status) 
                VALUES (?, ?, ?, ?, ?)";
        
        $this->db->execute($sql, [
            $data['user_id'],
            $data['product_id'],
            $data['quantity'],
            $total,
            $data['status'] ?? 'pending'
        ]);
        
        $orderId = $this->db->lastInsertId();
        
        // Update product stock
        $newStock = $product['stock'] - $data['quantity'];
        $this->db->execute("UPDATE products SET stock = ? WHERE id = ?", [$newStock, $data['product_id']]);
        
        return $this->success([
            'id' => $orderId,
            'total' => $total,
            'message' => 'Order created successfully'
        ], 201);
    }
    
    private function updateOrder($id) {
        $data = $this->getJsonInput();
        
        // Check if order exists
        $order = $this->db->fetchOne("SELECT id FROM orders WHERE id = ?", [$id]);
        if (!$order) {
            return $this->error('Order not found', 404);
        }
        
        $updates = [];
        $params = [];
        
        if (isset($data['status'])) {
            $updates[] = 'status = ?';
            $params[] = $data['status'];
        }
        
        if (isset($data['quantity'])) {
            $updates[] = 'quantity = ?';
            $params[] = $data['quantity'];
        }
        
        if (empty($updates)) {
            return $this->error('No fields to update', 400);
        }
        
        $params[] = $id;
        $sql = "UPDATE orders SET " . implode(', ', $updates) . " WHERE id = ?";
        $this->db->execute($sql, $params);
        
        return $this->success(['message' => 'Order updated successfully']);
    }
    
    private function deleteOrder($id) {
        $sql = "DELETE FROM orders WHERE id = ?";
        $affected = $this->db->execute($sql, [$id]);
        
        if ($affected === 0) {
            return $this->error('Order not found', 404);
        }
        
        return $this->success(['message' => 'Order deleted successfully']);
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

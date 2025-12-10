<?php
/**
 * Products Microservice
 * Handles all product-related operations
 */

class ProductsService {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function handle($params = []) {
        $method = $_SERVER['REQUEST_METHOD'];
        
        switch ($method) {
            case 'GET':
                return isset($params['id']) 
                    ? $this->getProduct($params['id']) 
                    : $this->getAllProducts();
                    
            case 'POST':
                return $this->createProduct();
                
            case 'PUT':
                return isset($params['id']) 
                    ? $this->updateProduct($params['id']) 
                    : $this->error('Product ID required');
                    
            case 'DELETE':
                return isset($params['id']) 
                    ? $this->deleteProduct($params['id']) 
                    : $this->error('Product ID required');
                    
            default:
                return $this->error('Method not allowed', 405);
        }
    }
    
    private function getAllProducts() {
        // Get query parameters for filtering
        $category = $_GET['category'] ?? null;
        
        if ($category) {
            $sql = "SELECT * FROM products WHERE category = ? ORDER BY name";
            $products = $this->db->fetchAll($sql, [$category]);
        } else {
            $sql = "SELECT * FROM products ORDER BY name";
            $products = $this->db->fetchAll($sql);
        }
        
        return $this->success([
            'products' => $products,
            'total' => count($products)
        ]);
    }
    
    private function getProduct($id) {
        $sql = "SELECT * FROM products WHERE id = ?";
        $product = $this->db->fetchOne($sql, [$id]);
        
        if (!$product) {
            return $this->error('Product not found', 404);
        }
        
        return $this->success($product);
    }
    
    private function createProduct() {
        $data = $this->getJsonInput();
        
        if (!isset($data['name']) || !isset($data['price'])) {
            return $this->error('Name and price are required', 400);
        }
        
        $sql = "INSERT INTO products (name, price, category, stock) VALUES (?, ?, ?, ?)";
        $this->db->execute($sql, [
            $data['name'],
            $data['price'],
            $data['category'] ?? null,
            $data['stock'] ?? 0
        ]);
        
        $productId = $this->db->lastInsertId();
        
        return $this->success([
            'id' => $productId,
            'message' => 'Product created successfully'
        ], 201);
    }
    
    private function updateProduct($id) {
        $data = $this->getJsonInput();
        
        // Check if product exists
        $product = $this->db->fetchOne("SELECT id FROM products WHERE id = ?", [$id]);
        if (!$product) {
            return $this->error('Product not found', 404);
        }
        
        $updates = [];
        $params = [];
        
        if (isset($data['name'])) {
            $updates[] = 'name = ?';
            $params[] = $data['name'];
        }
        
        if (isset($data['price'])) {
            $updates[] = 'price = ?';
            $params[] = $data['price'];
        }
        
        if (isset($data['category'])) {
            $updates[] = 'category = ?';
            $params[] = $data['category'];
        }
        
        if (isset($data['stock'])) {
            $updates[] = 'stock = ?';
            $params[] = $data['stock'];
        }
        
        if (empty($updates)) {
            return $this->error('No fields to update', 400);
        }
        
        $params[] = $id;
        $sql = "UPDATE products SET " . implode(', ', $updates) . " WHERE id = ?";
        $this->db->execute($sql, $params);
        
        return $this->success(['message' => 'Product updated successfully']);
    }
    
    private function deleteProduct($id) {
        $sql = "DELETE FROM products WHERE id = ?";
        $affected = $this->db->execute($sql, [$id]);
        
        if ($affected === 0) {
            return $this->error('Product not found', 404);
        }
        
        return $this->success(['message' => 'Product deleted successfully']);
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

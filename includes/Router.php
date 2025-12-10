<?php
/**
 * Simple Router for API Gateway
 * Handles request routing to microservices
 */

class Router {
    private $routes = [];
    private $basePath;
    
    public function __construct($basePath = '') {
        $this->basePath = rtrim($basePath, '/');
    }
    
    public function add($method, $path, $handler) {
        $pattern = $this->convertToRegex($path);
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'handler' => $handler,
            'path' => $path
        ];
    }
    
    public function get($path, $handler) {
        $this->add('GET', $path, $handler);
    }
    
    public function post($path, $handler) {
        $this->add('POST', $path, $handler);
    }
    
    public function put($path, $handler) {
        $this->add('PUT', $path, $handler);
    }
    
    public function delete($path, $handler) {
        $this->add('DELETE', $path, $handler);
    }
    
    private function convertToRegex($path) {
        // Convert :param to named regex groups
        $pattern = preg_replace('/\/:([a-zA-Z0-9_]+)/', '/(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
    
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove base path
        if ($this->basePath && strpos($uri, $this->basePath) === 0) {
            $uri = substr($uri, strlen($this->basePath));
        }
        
        $uri = rtrim($uri, '/') ?: '/';
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                // Call handler
                return call_user_func($route['handler'], $params);
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        echo json_encode([
            'error' => 'Not Found',
            'message' => 'The requested endpoint does not exist',
            'path' => $uri
        ]);
        exit;
    }
    
    public function getRoutes() {
        return $this->routes;
    }
}

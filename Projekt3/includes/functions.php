<?php

function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-äöüß]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    $string = trim($string, '-');
    
    $replacements = [
        'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss'
    ];
    $string = strtr($string, $replacements);
    
    return $string;
}

function formatDate($date) {
    $timestamp = strtotime($date);
    return date('d.m.Y H:i', $timestamp);
}

function truncateText($text, $length = 150) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

function uploadImage($file, $upload_dir = 'uploads/') {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'error' => 'Ungültiger Dateityp. Nur JPG, PNG, GIF und WebP sind erlaubt.'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'Datei ist zu groß. Maximum 5MB.'];
    }
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename, 'path' => $filepath];
    }
    
    return ['success' => false, 'error' => 'Fehler beim Hochladen der Datei.'];
}

function deleteImage($filepath) {
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

function getCurrentUrl() {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function showAlert($message, $type = 'info') {
    $colors = [
        'success' => 'bg-green-100 border-green-400 text-green-700',
        'error' => 'bg-red-100 border-red-400 text-red-700',
        'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
        'info' => 'bg-blue-100 border-blue-400 text-blue-700'
    ];
    
    $colorClass = $colors[$type] ?? $colors['info'];
    
    return "<div class='border-l-4 p-4 mb-4 {$colorClass}' role='alert'>
                <p>{$message}</p>
            </div>";
}

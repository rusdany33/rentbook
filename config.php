<?php
// Configuration file
define('BASE_PATH', __DIR__);
define('SRC_PATH', BASE_PATH . '/src');
define('STORAGE_PATH', SRC_PATH . '/Storage');

// Autoloader
spl_autoload_register(function ($class) {
    $class = str_replace('App\\', '', $class);
    $class = str_replace('\\', '/', $class);
    $file = SRC_PATH . '/' . $class . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize storage files if not exist
$storageFiles = [
    STORAGE_PATH . '/users.json' => [],
    STORAGE_PATH . '/ebooks.json' => [],
    STORAGE_PATH . '/transactions.json' => []
];

foreach ($storageFiles as $file => $defaultData) {
    if (!file_exists($file)) {
        file_put_contents($file, json_encode($defaultData, JSON_PRETTY_PRINT));
    }
}

// Helper functions
function redirect($url) {
    header("Location: $url");
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'email' => $_SESSION['user_email'],
        'name' => $_SESSION['user_name'],
        'role' => $_SESSION['user_role']
    ];
}

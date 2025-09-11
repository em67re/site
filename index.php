<?php
/**
 * Main entry point for the betting odds prediction application
 */

// Basic autoloader for our classes
spl_autoload_register(function ($className) {
    $paths = [
        'controllers/',
        'models/',
        'libraries/'
    ];
    
    foreach ($paths as $path) {
        $file = __DIR__ . '/' . $path . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Simple routing
$controller = $_GET['controller'] ?? 'BetOddsPrediction';
$action = $_GET['action'] ?? 'index';

$controllerClass = $controller . 'Controller';

if (class_exists($controllerClass)) {
    $controllerInstance = new $controllerClass();
    if (method_exists($controllerInstance, $action)) {
        $controllerInstance->$action();
    } else {
        echo "Action not found: $action";
    }
} else {
    echo "Controller not found: $controllerClass";
}
?>
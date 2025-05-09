<?php
// Start session
session_start();

// Get controller and action from URL
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'event';
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

// Load the appropriate controller
$controllerFile = __DIR__ . '/Controller/' . ucfirst($controller) . 'Controller.php';
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controllerClass = ucfirst($controller) . 'Controller';
    $controllerInstance = new $controllerClass();
    
    // Call the action if it exists
    if (method_exists($controllerInstance, $action)) {
        $controllerInstance->$action();
    } else {
        // Action not found
        header('HTTP/1.0 404 Not Found');
        echo 'Action not found: ' . $action;
    }
} else {
    // Controller not found
    header('HTTP/1.0 404 Not Found');
    echo 'Controller not found: ' . $controllerFile;
}
?>

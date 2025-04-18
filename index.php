<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controller/EventController.php';
// Add more controller files as needed
// index.php

require_once 'Controller/EventController.php';

if ($_GET['controller'] === 'event' && $_GET['action'] === 'showFrontList') {
    $controller = new EventController();
    $controller->showFrontList(); // this calls the method that loads $events
}

// Default controller and action
$controllerName = isset($_GET['controller']) ? $_GET['controller'] : 'event';
$action = isset($_GET['action']) ? $_GET['action'] : 'listEvents';

// Create the controller object based on the URL parameter
switch ($controllerName) {
    case 'event':
        $controller = new EventController();
        break;
    // Add cases for other controllers (e.g., 'user', 'product')
    default:
        die('Controller not found');
}

// Check if the action method exists in the controller
if (!method_exists($controller, $action)) {
    die('Action not found');
}

// Call the action method
$controller->$action();
?>

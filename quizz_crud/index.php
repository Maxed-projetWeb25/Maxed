<?php
require_once 'controllers/QuizzController.php';

$controller = new QuizzController();

$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'create':
        $controller->create();
        break;
    case 'store':
        $controller->store($_POST);
        break;
    case 'edit':
        $controller->edit($id);
        break;
    case 'update':
        $controller->update($id, $_POST);
        break;
    case 'delete':
        $controller->delete($id);
        break;
    default:
        $controller->index();
}


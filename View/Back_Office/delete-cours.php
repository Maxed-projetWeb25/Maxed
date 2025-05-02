<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/launcher/Controller/CoursController.php';

if (isset($_GET['id'])) {
    $coursController = new CoursController();
    $coursController->deleteCours($_GET['id']);
    header('Location: cours-datatable.php');
    exit;
}
?>
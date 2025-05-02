<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/launcher/Controller/ChapitreController.php';

if (isset($_GET['id_chapitre'])) {
    $chapitreController = new ChapitreController();
    $chapitreController->deleteChapitre($_GET['id_chapitre']);
    header('Location: cours-datatable.php');
    exit;
}
?>
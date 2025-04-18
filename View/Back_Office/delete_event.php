<?php
require_once '../../Model/EventModel.php';

if (isset($_GET['id'])) {
    $model = new EventModel();
    
    // Delete the event
    $model->deleteEvent($_GET['id']);

    // Redirect to the correct event list page
    header('Location: http://localhost/projet%20web/View/Back_Office/event-datatable.php');
    exit;
} else {
    echo "Erreur : ID de l'événement non fourni.";
}
?>

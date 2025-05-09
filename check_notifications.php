<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/launcher/Model/Cours.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/launcher/Model/Chapitre.php';

if (!isset($_SESSION['notified_ids'])) {
    $_SESSION['notified_ids'] = ['cours' => [], 'chapitre' => []];
}

$new_notifications = [];

// Cours notifications
$courses = Cours::getAllCours();
foreach ($courses as $course) {
    if (!in_array($course['id_cours'], $_SESSION['notified_ids']['cours'])) {
        $_SESSION['notified_ids']['cours'][] = $course['id_cours'];
        $new_notifications[] = [
            'type' => 'cours',
            'title' => $course['titre'],
            'message' => 'New Course Added: ' . $course['titre']
        ];
    }
}

// Chapitre notifications
$chapters = Chapitre::getAllChapitres();
foreach ($chapters as $chapitre) {
    if (!in_array($chapitre['id_chapitre'], $_SESSION['notified_ids']['chapitre'])) {
        $_SESSION['notified_ids']['chapitre'][] = $chapitre['id_chapitre'];

        // Get course title
        $cours = Cours::getCoursById($chapitre['id_cours']);
        $cours_titre = $cours ? $cours['titre'] : 'Unknown Course';

        $new_notifications[] = [
            'type' => 'chapitre',
            'title' => $chapitre['titre'],
            'message' => 'New Chapter "' . $chapitre['titre'] . '" in Course: ' . $cours_titre
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($new_notifications);

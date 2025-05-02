<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/launcher/Model/Cours.php';

class CoursController {
    public function getAllCours() {
        return Cours::getAllCours();
    }

    public function getCoursById($id) {
        return Cours::getCoursById($id);
    }

    public function createCours($data) {
        return Cours::createCours($data);
    }

    public function updateCours($id, $data) {
        return Cours::updateCours($id, $data);
    }

    public function deleteCours($id) {
        return Cours::deleteCours($id);
    }
}
?>
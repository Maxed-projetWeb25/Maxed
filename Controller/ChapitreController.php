<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/launcher/Model/Chapitre.php';

class ChapitreController {
    public function getAllChapitres() {
        return Chapitre::getAllChapitres();
    }

    public function getChapitreById($id) {
        return Chapitre::getChapitreById($id);
    }

    public function createChapitre($data) {
        return Chapitre::createChapitre($data);
    }

    public function updateChapitre($id, $data) {
        return Chapitre::updateChapitre($id, $data);
    }

    public function deleteChapitre($id) {
        return Chapitre::deleteChapitre($id);
    }

    public function getChapitresByCoursId($id_cours) {
        return Chapitre::getChapitresByCoursId($id_cours);
    }
}
?>
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/launcher/Config.php';

class Cours {
    public static function getAllCours() {
        $db = Database::getConnection();
        $query = $db->prepare("SELECT * FROM cours");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getCoursById($id) {
        $db = Database::getConnection();
        $query = $db->prepare("SELECT * FROM cours WHERE id_cours = ?");
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public static function createCours($data) {
        $db = Database::getConnection();
        $query = $db->prepare("INSERT INTO cours (titre, description) VALUES (?, ?)");
        return $query->execute([$data['titre'], $data['description']]);
    }

    public static function updateCours($id, $data) {
        $db = Database::getConnection();
        $query = $db->prepare("UPDATE cours SET titre = ?, description = ? WHERE id_cours = ?");
        return $query->execute([$data['titre'], $data['description'], $id]);
    }

    public static function deleteCours($id) {
        $db = Database::getConnection();
    
        // First, delete associated chapters
        $query = $db->prepare("DELETE FROM chapitre WHERE id_cours = ?");
        $query->execute([$id]);
    
        // Then, delete the course
        $query = $db->prepare("DELETE FROM cours WHERE id_cours = ?");
        return $query->execute([$id]);
    }
    
}
?>
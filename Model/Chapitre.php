<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/launcher/Config.php';

class Chapitre {
    public static function getAllChapitres() {
        $db = Database::getConnection();
        $query = $db->prepare("SELECT * FROM chapitre");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getChapitreById($id) {
        $db = Database::getConnection();
        $query = $db->prepare("SELECT * FROM chapitre WHERE id_chapitre = ?");
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public static function createChapitre($data) {
        $db = Database::getConnection();
        $query = $db->prepare("INSERT INTO chapitre (titre, contenu, id_cours) VALUES (?, ?, ?)");
        return $query->execute([$data['titre'], $data['contenu'], $data['id_cours']]);
    }

    public static function updateChapitre($id, $data) {
        $db = Database::getConnection();
        $query = $db->prepare("UPDATE chapitre SET titre = ?, contenu = ?, id_cours = ? WHERE id_chapitre = ?");
        return $query->execute([$data['titre'], $data['contenu'], $data['id_cours'], $id]);
    }

    public static function deleteChapitre($id) {
        $db = Database::getConnection();
        $query = $db->prepare("DELETE FROM chapitre WHERE id_chapitre = ?");
        return $query->execute([$id]);
    }

    public static function getChapitresByCoursId($id_cours) {
        $db = Database::getConnection();
        $query = $db->prepare("SELECT * FROM chapitre WHERE id_cours = ?");
        $query->execute([$id_cours]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getNewChapitres($last_check) {
    $db = Database::getConnection();
    $query = $db->prepare("SELECT * FROM chapitre WHERE created_at > ?");
    $query->execute([$last_check]);
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

}
?>
<?php
class CommentController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function addComment($postid, $userid, $contenu) {
        $sql = "INSERT INTO commentaire (postid, userid, contenu) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$postid, $userid, $contenu]);
    }

    public function getComments($postid) {
        $sql = "SELECT * FROM commentaire WHERE postid = ? ORDER BY date_com ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$postid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

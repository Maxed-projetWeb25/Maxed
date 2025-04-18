<?php
require_once __DIR__ . '/../config/databse.php';

class Question {
    private $conn;
    private $table = "questions";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($quizzId, $questionText) {
        $stmt = $this->conn->prepare("INSERT INTO " . $this->table . " (quizz_id, question_text) VALUES (?, ?)");
        $stmt->execute([$quizzId, $questionText]);
    }

    public function getLastInsertId() {
        return $this->conn->lastInsertId();
    }

    public function getByQuizId($quizzId) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE quizz_id = ?");
        $stmt->execute([$quizzId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteByQuizId($quizzId) {
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE quizz_id = ?");
        return $stmt->execute([$quizzId]);
    }
}

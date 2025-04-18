<?php
require_once __DIR__ . '/../config/databse.php';

class Answer {
    private $conn;
    private $table = "answers";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($questionId, $answerText, $isCorrect) {
        $stmt = $this->conn->prepare("INSERT INTO " . $this->table . " (question_id, answer_text, is_correct) VALUES (?, ?, ?)");
        $stmt->execute([$questionId, $answerText, $isCorrect]);
    }

    public function getByQuestionId($questionId) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE question_id = ?");
        $stmt->execute([$questionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteByQuestionId($questionId) {
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE question_id = ?");
        return $stmt->execute([$questionId]);
    }
    
}

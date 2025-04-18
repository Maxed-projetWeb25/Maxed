<?php
require_once __DIR__ . '/../config/databse.php';
require_once 'Question.php';
require_once 'Answer.php';

class Quizz {
    private $conn;
    private $table = "quizz";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        return $this->conn->query($query);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($titre, $description, $date_creation, $categorie) {
        $stmt = $this->conn->prepare("INSERT INTO " . $this->table . " (titre, description, date_creation, categorie) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$titre, $description, $date_creation, $categorie]);
    }

    public function getLastInsertId() {
        return $this->conn->lastInsertId();
    }

    public function getWithDetails($id) {
        $quiz = $this->getById($id);

        $questionModel = new Question();
        $questions = $questionModel->getByQuizId($id);

        foreach ($questions as &$question) {
            $answerModel = new Answer();
            $question['answers'] = $answerModel->getByQuestionId($question['id']);
        }

        $quiz['questions'] = $questions;
        return $quiz;
    }

    public function update($id, $titre, $description, $date_creation, $categorie) {
        $stmt = $this->conn->prepare("UPDATE " . $this->table . " SET titre = ?, description = ?, date_creation = ?, categorie = ? WHERE id = ?");
        return $stmt->execute([$titre, $description, $date_creation, $categorie, $id]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

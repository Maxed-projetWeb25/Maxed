<?php
require_once __DIR__ . '/../models/Quizz.php';
require_once __DIR__ . '/../models/Question.php';
require_once __DIR__ . '/../models/Answer.php';

class QuizzController {
    private $quizzModel;
    private $questionModel;
    private $answerModel;

    public function __construct() {
        $this->quizzModel = new Quizz();
        $this->questionModel = new Question();
        $this->answerModel = new Answer();
    }

    public function index() {
        $quizz = $this->quizzModel->getAll();
        include __DIR__ . '/../views/quizz/index.php';
    }

    public function create() {
        include __DIR__ . '/../views/quizz/create.php';
    }

    public function store($data) {
        // 1. el quizz
        $this->quizzModel->create($data['titre'], $data['description'], $data['date_creation'], $data['categorie']);
        // el new id 
        $quizId = $this->quizzModel->getLastInsertId();

        // 2. save fl data base
        if (isset($data['questions']) && is_array($data['questions'])) {
            foreach ($data['questions'] as $question) {
                $questionText = $question['text'];
                $this->questionModel->create($quizId, $questionText);
                $questionId = $this->questionModel->getLastInsertId();

                // 3. Loop through answers
                if (isset($question['answers']) && is_array($question['answers'])) {
                    foreach ($question['answers'] as $answer) {
                        $answerText = $answer['text'];
                        $isCorrect = isset($answer['is_correct']) ? 1 : 0;
                        $this->answerModel->create($questionId, $answerText, $isCorrect);
                    }
                }
            }
        }

        header("Location: index.php");
        exit;
    }

    public function edit($id) {
        $quizz = $this->quizzModel->getById($id);
        include __DIR__ . '/../views/quizz/edit.php';
    }

    public function update($id, $data) {
        $this->quizzModel->update($id, $data['titre'], $data['description'], $data['date_creation'], $data['categorie']);
        header("Location: index.php");
    }

    public function delete($id) {
        $this->quizzModel->delete($id);
        header("Location: index.php");
    }
}

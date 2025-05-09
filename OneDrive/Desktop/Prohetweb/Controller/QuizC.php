<?php
require_once __DIR__ . '/../config/config.php';

class QuizC {
    // ... existing code ...

    public function getTotalQuizzes() {
        $sql = "SELECT COUNT(*) as total FROM quizzes";
        $db = Config::getInstance()->getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $result = $query->fetch();
            return $result['total'];
        } catch (Exception $e) {
            return 0;
        }
    }
} 
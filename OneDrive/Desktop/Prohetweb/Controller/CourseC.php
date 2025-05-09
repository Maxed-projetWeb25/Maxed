<?php
require_once __DIR__ . '/../config/config.php';

class CourseC {
    // ... existing code ...

    public function getTotalCourses() {
        $sql = "SELECT COUNT(*) as total FROM courses";
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
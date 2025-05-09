<?php
require_once __DIR__ . '/../config/config.php';

class UserC {
    // ... existing code ...

    public function getTotalUsers() {
        $sql = "SELECT COUNT(*) as total FROM users";
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

    public function getMonthlyStats() {
        $sql = "SELECT MONTH(created_at) as month, COUNT(*) as total 
                FROM users 
                WHERE YEAR(created_at) = YEAR(CURRENT_DATE)
                GROUP BY MONTH(created_at)";
        $db = Config::getInstance()->getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $results = $query->fetchAll();
            
            // Initialize all months with 0
            $monthlyStats = array_fill(1, 12, 0);
            
            // Fill in actual values
            foreach ($results as $row) {
                $monthlyStats[$row['month']] = (int)$row['total'];
            }
            
            return $monthlyStats;
        } catch (Exception $e) {
            return array_fill(1, 12, 0);
        }
    }
} 
<?php
require_once __DIR__ . '/../config/config.php';

class EventC {
    // ... existing code ...

    public function getTotalEvents() {
        $sql = "SELECT COUNT(*) as total FROM events";
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
        $sql = "SELECT MONTH(date_event) as month, COUNT(*) as count 
                FROM events 
                WHERE YEAR(date_event) = YEAR(CURRENT_DATE)
                GROUP BY MONTH(date_event)";
        $db = Config::getInstance()->getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $results = $query->fetchAll();
            
            // Initialize all months with 0
            $stats = array_fill(1, 12, 0);
            
            // Fill in actual values
            foreach ($results as $row) {
                $stats[$row['month']] = (int)$row['count'];
            }
            
            return array_values($stats); // Return just the values in order
        } catch (Exception $e) {
            return array_fill(0, 12, 0);
        }
    }

    public function getEventsByStatus() {
        $sql = "SELECT status, COUNT(*) as count 
                FROM events 
                GROUP BY status";
        $db = Config::getInstance()->getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $results = $query->fetchAll();
            
            $stats = array();
            foreach ($results as $row) {
                $stats[$row['status']] = (int)$row['count'];
            }
            return $stats;
        } catch (Exception $e) {
            return array();
        }
    }

    public function getEventTrendsByMonth() {
        $sql = "SELECT status, MONTH(date_event) as month, COUNT(*) as count 
                FROM events 
                WHERE YEAR(date_event) = YEAR(CURRENT_DATE)
                GROUP BY status, MONTH(date_event)
                ORDER BY MONTH(date_event)";
        $db = Config::getInstance()->getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $results = $query->fetchAll();
            
            // Initialize data structure
            $trends = array(
                'upcoming' => array_fill(1, 12, 0),
                'completed' => array_fill(1, 12, 0),
                'cancelled' => array_fill(1, 12, 0)
            );
            
            // Fill in actual values
            foreach ($results as $row) {
                $trends[$row['status']][$row['month']] = (int)$row['count'];
            }
            
            return $trends;
        } catch (Exception $e) {
            return array();
        }
    }

    public function getEventsByLocation() {
        $sql = "SELECT loc_event as location, COUNT(*) as count 
                FROM events 
                GROUP BY loc_event";
        $db = Config::getInstance()->getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $results = $query->fetchAll();
            
            $stats = array();
            foreach ($results as $row) {
                $stats[$row['location']] = (int)$row['count'];
            }
            return $stats;
        } catch (Exception $e) {
            return array();
        }
    }

    public function getEventsByCapacityRange() {
        $sql = "SELECT 
                CASE 
                    WHEN cap_event <= 50 THEN '0-50'
                    WHEN cap_event <= 100 THEN '51-100'
                    WHEN cap_event <= 200 THEN '101-200'
                    WHEN cap_event <= 500 THEN '201-500'
                    ELSE '500+'
                END as capacity_range,
                COUNT(*) as count
                FROM events
                GROUP BY capacity_range
                ORDER BY MIN(cap_event)";
        $db = Config::getInstance()->getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $results = $query->fetchAll();
            
            $stats = array();
            foreach ($results as $row) {
                $stats[$row['capacity_range']] = (int)$row['count'];
            }
            return $stats;
        } catch (Exception $e) {
            return array();
        }
    }

    public function getCapacityByLocation() {
        $sql = "SELECT loc_event as location, SUM(cap_event) as total_capacity 
                FROM events 
                GROUP BY loc_event 
                ORDER BY total_capacity DESC";
        $db = Config::getInstance()->getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $results = $query->fetchAll();
            
            $stats = array();
            foreach ($results as $row) {
                $stats[$row['location']] = (int)$row['total_capacity'];
            }
            return $stats;
        } catch (Exception $e) {
            return array();
        }
    }
} 
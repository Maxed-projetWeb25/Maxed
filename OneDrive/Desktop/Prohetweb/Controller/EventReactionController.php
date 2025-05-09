<?php
require_once 'C:/xampp/htdocs/projet web/config/config.php';

class EventReactionController {
    private $db;

    public function __construct() {
        $this->db = Config::getInstance()->getConnection();
        
        // Create reactions table if it doesn't exist
        $this->createReactionsTable();
    }

    private function createReactionsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS event_reactions (
            id INT PRIMARY KEY AUTO_INCREMENT,
            event_id INT,
            reaction_type ENUM('like', 'dislike'),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (event_id) REFERENCES event(id_event)
        )";
        $this->db->exec($sql);
    }

    public function handleReaction($eventId, $reactionType) {
        try {
            // First, check if any reaction exists for this event
            $stmt = $this->db->prepare("SELECT reaction_type FROM event_reactions WHERE event_id = ?");
            $stmt->execute([$eventId]);
            $existingReaction = $stmt->fetchColumn();

            // If a reaction exists
            if ($existingReaction) {
                // If it's the same type, remove it (toggle off)
                if ($existingReaction === $reactionType) {
                    $stmt = $this->db->prepare("DELETE FROM event_reactions WHERE event_id = ?");
                    $stmt->execute([$eventId]);
                    $response = ['status' => 'removed', 'message' => 'Reaction removed'];
                } else {
                    // If it's a different type, update it
                    $stmt = $this->db->prepare("UPDATE event_reactions SET reaction_type = ? WHERE event_id = ?");
                    $stmt->execute([$reactionType, $eventId]);
                    $response = ['status' => 'changed', 'message' => 'Reaction changed'];
                }
            } else {
                // If no reaction exists, add new one
                $stmt = $this->db->prepare("INSERT INTO event_reactions (event_id, reaction_type) VALUES (?, ?)");
                $stmt->execute([$eventId, $reactionType]);
                $response = ['status' => 'added', 'message' => 'Reaction added'];
            }

            // Get updated counts
            $stmt = $this->db->prepare("SELECT reaction_type, COUNT(*) as count FROM event_reactions WHERE event_id = ? GROUP BY reaction_type");
            $stmt->execute([$eventId]);
            $counts = [
                'likes' => 0,
                'dislikes' => 0
            ];
            while ($row = $stmt->fetch()) {
                $counts[$row['reaction_type'] . 's'] = (int)$row['count'];
            }
            
            // Get the current reaction type for this event (if any)
            $stmt = $this->db->prepare("SELECT reaction_type FROM event_reactions WHERE event_id = ?");
            $stmt->execute([$eventId]);
            $currentReaction = $stmt->fetchColumn();
            
            $response['counts'] = $counts;
            $response['currentReaction'] = $currentReaction ?: null;
            return $response;

        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function getReactionCounts($eventId) {
        try {
            // Get counts
            $stmt = $this->db->prepare("SELECT reaction_type, COUNT(*) as count FROM event_reactions WHERE event_id = ? GROUP BY reaction_type");
            $stmt->execute([$eventId]);
            $counts = [
                'likes' => 0,
                'dislikes' => 0
            ];
            while ($row = $stmt->fetch()) {
                $counts[$row['reaction_type'] . 's'] = (int)$row['count'];
            }

            // Get current reaction for this event
            $stmt = $this->db->prepare("SELECT reaction_type FROM event_reactions WHERE event_id = ?");
            $stmt->execute([$eventId]);
            $currentReaction = $stmt->fetchColumn();

            return [
                'counts' => $counts,
                'currentReaction' => $currentReaction ?: null
            ];
        } catch (PDOException $e) {
            return [
                'counts' => ['likes' => 0, 'dislikes' => 0],
                'currentReaction' => null
            ];
        }
    }
} 
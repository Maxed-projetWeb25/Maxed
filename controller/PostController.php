<?php
class PostController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->verifyTableStructure();
    }

    private function verifyTableStructure() {
        try {
            // Check table structure
            $columns = $this->db->query("SHOW COLUMNS FROM post")->fetchAll(PDO::FETCH_COLUMN);
            error_log("Post table columns: " . print_r($columns, true));
            
            // Update requiredColumns to match your DB schema
            $requiredColumns = ['postid', 'userid', 'description', 'media', 'media_type', 'posttype', 'visibility', 'dateposted'];
            $missingColumns = array_diff($requiredColumns, $columns);
            
            if (!empty($missingColumns)) {
                error_log("Missing columns in post table: " . implode(', ', $missingColumns));
                throw new Exception("Post table is missing required columns: " . implode(', ', $missingColumns));
            }
        } catch (PDOException $e) {
            error_log("Error checking table structure: " . $e->getMessage());
            throw new Exception("Error checking table structure: " . $e->getMessage());
        }
    }

    public function togglePostLike($postId, $userId) {
        try {
            $sql = "SELECT count FROM react WHERE postid = :postid AND userid = :userid";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':postid', $postId, PDO::PARAM_INT);
            $stmt->bindParam(':userid', $userId, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $sql = "DELETE FROM react WHERE postid = :postid AND userid = :userid";
            } else {
                $sql = "INSERT INTO react (userid, postid) VALUES (:userid, :postid)";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':postid', $postId, PDO::PARAM_INT);
            $stmt->bindParam(':userid', $userId, PDO::PARAM_INT);

            $result = $stmt->execute();
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("SQL Error: " . print_r($errorInfo, true));
            }
            return $result;
        } catch (Exception $e) {
            error_log("Error in togglePostLike: " . $e->getMessage());
            return false;
        }
    }

    public function hasUserLikedPost($postId, $userId) {
        try {
            $sql = "SELECT count FROM react WHERE postid = :postid AND userid = :userid";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':postid', $postId, PDO::PARAM_INT);
            $stmt->bindParam(':userid', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Error in hasUserLikedPost: " . $e->getMessage());
            return false;
        }
    }

    public function getPostLikesCount($postId) {
        try {
            $sql = "SELECT COUNT(*) as total FROM react WHERE postid = :postid";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':postid', $postId, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (Exception $e) {
            error_log("Error in getPostLikesCount: " . $e->getMessage());
            return 0;
        }
    }

    public function createPost($postData) {
        try {
            if (!empty($postData['media'])) {
                $sql = "INSERT INTO post (userid, description, media, media_type, posttype, visibility) 
                        VALUES (:userid, :description, :media, :media_type, :posttype, :visibility)";
            } else {
                $sql = "INSERT INTO post (userid, description, posttype, visibility) 
                        VALUES (:userid, :description, :posttype, :visibility)";
            }
            
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':userid', $postData['userid'], PDO::PARAM_INT);
            $stmt->bindParam(':description', $postData['description'], PDO::PARAM_STR);
            $stmt->bindParam(':posttype', $postData['posttype'], PDO::PARAM_STR);
            $stmt->bindParam(':visibility', $postData['visibility'], PDO::PARAM_STR);

            if (!empty($postData['media'])) {
                $stmt->bindParam(':media', $postData['media'], PDO::PARAM_LOB);
                $stmt->bindParam(':media_type', $postData['media_type'], PDO::PARAM_STR);
            }

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error in createPost: " . $e->getMessage());
            return false;
        }
    }

    public function toggleRepost($post_id, $user_id) {
        try {
            $sql = "INSERT INTO repost (postid, userid, reposttime) 
                    VALUES (?, ?, CURRENT_TIMESTAMP) 
                    ON DUPLICATE KEY UPDATE reposttime = IF(reposttime IS NULL, CURRENT_TIMESTAMP, NULL)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$post_id, $user_id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getRepostCount($post_id) {
        try {
            $sql = "SELECT COUNT(*) as count FROM repost 
                    WHERE postid = ? AND reposttime IS NOT NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$post_id]);
            $result = $stmt->fetch();
            return $result['count'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getPosts() {
        try {
            // Use correct column name for ordering
            $sql = "SELECT * FROM post ORDER BY dateposted DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error in getPosts: " . $e->getMessage());
            return [];
        }
    }
    
    public function deletePost($postId) {
        try {
            error_log("Deleting post: $postId");
            // Delete related comments
            $stmt1 = $this->db->prepare("DELETE FROM commentaire WHERE postid = ?");
            $result1 = $stmt1->execute([$postId]);
            if (!$result1) {
                $errorInfo = $stmt1->errorInfo();
                error_log("Delete commentaire error: " . print_r($errorInfo, true));
                return false;
            }
            // Delete related reactions
            $stmt2 = $this->db->prepare("DELETE FROM react WHERE postid = ?");
            $result2 = $stmt2->execute([$postId]);
            if (!$result2) {
                $errorInfo = $stmt2->errorInfo();
                error_log("Delete react error: " . print_r($errorInfo, true));
                return false;
            }
            // Delete the post
            $sql = "DELETE FROM post WHERE postid = ?";
            $stmt3 = $this->db->prepare($sql);
            $result3 = $stmt3->execute([$postId]);
            if (!$result3) {
                $errorInfo = $stmt3->errorInfo();
                error_log("Delete post error: " . print_r($errorInfo, true));
                return false;
            }
            return true;
        } catch (PDOException $e) {
            error_log("Delete error: " . $e->getMessage());
            return false;
        }
    }

    public function getPostById($postId) {
        try {
            $sql = "SELECT * FROM post WHERE postid = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$postId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    public function updatePost($postId, $updatedData) {
        try {
            $sql = "UPDATE post SET description = ?, media = ?, posttype = ?, visibility = ? WHERE postid = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $updatedData['description'],
                $updatedData['media'],
                $updatedData['posttype'],
                $updatedData['visibility'],
                $postId
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>

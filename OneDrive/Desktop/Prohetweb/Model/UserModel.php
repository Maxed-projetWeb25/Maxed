<?php
require_once __DIR__ . '/../config/config.php';

class UserModel {
    private $db;

    public function __construct() {
        try {
            if (!class_exists('Config')) {
                throw new Exception("Config class not found");
            }
            
            $config = Config::getInstance();
            if (!$config) {
                throw new Exception("Failed to get Config instance");
            }
            
            $this->db = $config->getConnection();
            if (!$this->db) {
                throw new Exception("Failed to get database connection");
            }
        } catch (Exception $e) {
            error_log("Error initializing UserModel: " . $e->getMessage());
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function getAllUsers() {
        try {
            $query = "SELECT * FROM user";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAllUsers: " . $e->getMessage());
            return [];
        }
    }

    public function getUserById($id) {
        try {
            $query = "SELECT * FROM user WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getUserById: " . $e->getMessage());
            return null;
        }
    }

    public function createUser($username, $email, $password) {
        try {
            $query = "INSERT INTO user (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in createUser: " . $e->getMessage());
            return false;
        }
    }

    public function updateUser($id, $username, $email, $password = null) {
        try {
            if ($password) {
                $query = "UPDATE user SET username = :username, email = :email, password = :password WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':password', $password);
            } else {
                $query = "UPDATE user SET username = :username, email = :email WHERE id = :id";
                $stmt = $this->db->prepare($query);
            }
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in updateUser: " . $e->getMessage());
            return false;
        }
    }

    public function deleteUser($id) {
        try {
            $query = "DELETE FROM user WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in deleteUser: " . $e->getMessage());
            return false;
        }
    }

    public function getUserByEmail($email) {
        try {
            $query = "SELECT * FROM user WHERE email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getUserByEmail: " . $e->getMessage());
            return null;
        }
    }

    public function getUserByUsername($username) {
        try {
            $query = "SELECT * FROM user WHERE username = :username";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getUserByUsername: " . $e->getMessage());
            return null;
        }
    }
}
?> 
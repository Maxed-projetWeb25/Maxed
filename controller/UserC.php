<?php
require 'C:\xampp\htdocs\projet_web\config.php';
require 'C:\xampp\htdocs\projet_web\model\User.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

class UtilisateurC {
    public static function listUsers() {
        $sql = "SELECT * FROM user"; // Updated table name
        $db = config::getConnexion();
        try {
            $liste = $db->query($sql);
            return $liste;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    function deleteUser($ide) {
        $sql = "DELETE FROM user WHERE id = :id"; // Updated table name
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $ide);

        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function addUser($utilisateur) {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                "INSERT INTO user (nom, prenom, age, tel, role, email, pwd) 
                 VALUES (:nom, :prenom, :age, :tel, :role, :email, :pwd)" // Updated table name
            );
            $success = $query->execute([
                'nom' => $utilisateur->getNom(),
                'prenom' => $utilisateur->getPrenom(),
                'age' => $utilisateur->getAge(),
                'tel' => $utilisateur->getTel(),
                'role' => $utilisateur->getRole(),
                'email' => $utilisateur->getEmail(),
                'pwd' => $utilisateur->getPwd()
            ]);

            if (!$success) {
                throw new PDOException("Failed to execute query: " . implode(", ", $query->errorInfo()));
            }

            return true;
        } catch (PDOException $e) {
            error_log("Erreur SQL: " . $e->getMessage());
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    function showUser($id) {
        $sql = "SELECT * FROM user WHERE id = :id"; // Updated table name
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();
            $user = $query->fetch();
            return $user;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function updateUser($utilisateur, $id) {
        $sql = "UPDATE user SET nom = :nom, prenom = :prenom, age = :age, tel = :tel, role = :role, email = :email, pwd = :pwd WHERE id = :id"; // Updated table name
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id', $id);
            $query->bindValue(':nom', $utilisateur->getNom());
            $query->bindValue(':prenom', $utilisateur->getPrenom());
            $query->bindValue(':age', $utilisateur->getAge());
            $query->bindValue(':tel', $utilisateur->getTel());
            $query->bindValue(':role', $utilisateur->getRole());
            $query->bindValue(':email', $utilisateur->getEmail());
            $query->bindValue(':pwd', $utilisateur->getPwd());
            $query->execute();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // New method to get user by email
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM user WHERE email = :email"; // Updated table name
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':email', $email);
            $query->execute();
            $user = $query->fetch();
            return $user ?: null; // Return null if no user is found
        } catch (Exception $e) {
            throw new Exception("Error fetching user: " . $e->getMessage());
        }
    }
}
?>
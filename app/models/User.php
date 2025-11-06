<?php
// app/models/User.php

class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($username, $email, $password) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, email, password_hash) 
             VALUES (?, ?, ?)"
        );
        try {
            return $stmt->execute([$username, $email, $password_hash]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function findByEmailAndPassword($email, $password) {
        $user = $this->findByEmail($email);
        if ($user) {
            if (password_verify($password, $user['password_hash'])) {
                return $user;
            }
        }
        return false;
    }

    public function findById($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($user_id, $data) {
        $sql = "UPDATE users SET 
                    profile_major = ?, 
                    profile_skills = ?, 
                    profile_interests = ?, 
                    profile_strengths = ?, 
                    profile_weaknesses = ?,
                    profile_role_preference = ?
                WHERE user_id = ?";
        
        $stmt = $this->db->prepare($sql);
        try {
            return $stmt->execute([
                $data['profile_major'], $data['profile_skills'],
                $data['profile_interests'], $data['profile_strengths'],
                $data['profile_weaknesses'], $data['profile_role_preference'],
                $user_id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
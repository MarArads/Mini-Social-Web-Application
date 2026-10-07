<?php

require_once __DIR__ . '/../../config/database.php';

class UserModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT id, username, password, full_name, bio, profile_image, created_at, DATE_FORMAT(created_at, '%M %Y') AS joined FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT id, username, password, full_name, bio, profile_image, created_at, DATE_FORMAT(created_at, '%M %Y') AS joined FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function existsUsername($username, $excludeId = null) {
        if ($excludeId !== null) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $excludeId]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$username]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }

    public function create($username, $password, $fullName, $bio = '', $profileImage = null) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (username, password, full_name, bio, profile_image, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$username, $hashed, $fullName, $bio, $profileImage]);
        return $this->db->lastInsertId();
    }

    public function updateProfile($id, $fullName, $bio, $profileImage = null) {
        if ($profileImage !== null) {
            $stmt = $this->db->prepare("UPDATE users SET full_name = ?, bio = ?, profile_image = ? WHERE id = ?");
            return $stmt->execute([$fullName, $bio, $profileImage, $id]);
        }
        $stmt = $this->db->prepare("UPDATE users SET full_name = ?, bio = ? WHERE id = ?");
        return $stmt->execute([$fullName, $bio, $id]);
    }

    public function search($query) {
        $term = '%' . $query . '%';
        $stmt = $this->db->prepare("SELECT id, username, full_name, bio, profile_image, created_at, DATE_FORMAT(created_at, '%M %Y') AS joined FROM users WHERE username LIKE ? OR full_name LIKE ? ORDER BY username ASC");
        $stmt->execute([$term, $term]);
        return $stmt->fetchAll();
    }
}

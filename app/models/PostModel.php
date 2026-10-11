<?php

require_once __DIR__ . '/../../config/database.php';

class PostModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function create($userId, $content, $image = null) {
        $stmt = $this->db->prepare("INSERT INTO posts (user_id, content, image, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$userId, $content, $image]);
        return $this->db->lastInsertId();
    }

    public function getById($id, $currentUserId = null) {
        $sql = "SELECT p.id, p.user_id, p.content, p.image, p.created_at, u.username, u.full_name, u.profile_image,
            (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) AS likes,
            " . ($currentUserId !== null ? "(SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id AND l.user_id = ?) AS liked" : "0 AS liked") . "
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.id = ?";
        $stmt = $this->db->prepare($sql);
        if ($currentUserId !== null) {
            $stmt->execute([$currentUserId, $id]);
        } else {
            $stmt->execute([$id]);
        }
        return $stmt->fetch();
    }

    public function getAll($search = '', $currentUserId = null) {
        $sql = "SELECT p.id, p.user_id, p.content, p.image, p.created_at, u.username, u.full_name, u.profile_image,
            (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) AS likes,
            " . ($currentUserId !== null ? "(SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id AND l.user_id = " . (int)$currentUserId . ") AS liked" : "0 AS liked") . "
            FROM posts p
            JOIN users u ON p.user_id = u.id";
        
        $params = [];
        if (!empty($search)) {
            $sql .= " WHERE p.content LIKE ? OR u.username LIKE ? OR u.full_name LIKE ?";
            $term = '%' . $search . '%';
            $params = [$term, $term, $term];
        }

        $sql .= " ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getByUserId($userId, $currentUserId = null) {
        $sql = "SELECT p.id, p.user_id, p.content, p.image, p.created_at, u.username, u.full_name, u.profile_image,
            (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) AS likes,
            " . ($currentUserId !== null ? "(SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id AND l.user_id = " . (int)$currentUserId . ") AS liked" : "0 AS liked") . "
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.user_id = ?
            ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function countByUserId($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function update($id, $userId, $content, $image = null) {
        if ($image !== null) {
            $stmt = $this->db->prepare("UPDATE posts SET content = ?, image = ? WHERE id = ? AND user_id = ?");
            return $stmt->execute([$content, $image, $id, $userId]);
        }
        $stmt = $this->db->prepare("UPDATE posts SET content = ? WHERE id = ? AND user_id = ?");
        return $stmt->execute([$content, $id, $userId]);
    }

    public function delete($id, $userId) {
        $stmt = $this->db->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }
}

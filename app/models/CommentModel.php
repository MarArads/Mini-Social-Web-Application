<?php

require_once __DIR__ . '/../../config/database.php';

class CommentModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function create($postId, $userId, $content) {
        $stmt = $this->db->prepare("INSERT INTO comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$postId, $userId, $content]);
        return $this->db->lastInsertId();
    }

    public function getByPostId($postId) {
        $stmt = $this->db->prepare("SELECT c.id, c.post_id, c.user_id, c.content, c.created_at, u.username, u.full_name, u.profile_image
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.post_id = ?
            ORDER BY c.created_at ASC");
        $stmt->execute([$postId]);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT c.id, c.post_id, c.user_id, c.content, c.created_at, u.username, u.full_name, u.profile_image
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $userId, $content) {
        $stmt = $this->db->prepare("UPDATE comments SET content = ? WHERE id = ? AND user_id = ?");
        return $stmt->execute([$content, $id, $userId]);
    }

    public function delete($id, $userId) {
        $stmt = $this->db->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }
}

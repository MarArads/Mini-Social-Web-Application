<?php

require_once __DIR__ . '/../../config/database.php';

class LikeModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function isLiked($postId, $userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$postId, $userId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function toggleLike($postId, $userId) {
        if ($this->isLiked($postId, $userId)) {
            $stmt = $this->db->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
            $stmt->execute([$postId, $userId]);
            $liked = false;
        } else {
            $stmt = $this->db->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
            $stmt->execute([$postId, $userId]);
            $liked = true;
        }
        $count = $this->countByPostId($postId);
        return ['liked' => $liked, 'count' => $count];
    }

    public function countByPostId($postId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
        $stmt->execute([$postId]);
        return (int) $stmt->fetchColumn();
    }
}

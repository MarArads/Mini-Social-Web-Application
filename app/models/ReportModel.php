<?php

require_once __DIR__ . '/../../config/database.php';


class ReportModel {
    private $db;

        private static $sortOptions = [
        'newest'   => 'report.created_at DESC',
        'oldest'   => 'report.created_at ASC',
        'likes'    => 'report.like_count DESC, report.created_at DESC',
        'comments' => 'report.comment_count DESC, report.created_at DESC',
    ];

    public function __construct() {
        $this->db = Database::connect();
    }

    public static function sortOptions() {
        return array_keys(self::$sortOptions);
    }

    
    public function getSummary() {
        $sql = "SELECT
                (SELECT COUNT(*) FROM users)    AS total_users,
                (SELECT COUNT(*) FROM posts)    AS total_posts,
                (SELECT COUNT(*) FROM comments) AS total_comments,
                (SELECT COUNT(*) FROM likes)    AS total_likes,
                (SELECT COUNT(*) FROM posts WHERE image IS NOT NULL AND image <> '') AS posts_with_photo,
                (SELECT ROUND(COUNT(*) / NULLIF((SELECT COUNT(*) FROM users), 0), 2) FROM posts)    AS avg_posts_per_user,
                (SELECT ROUND(COUNT(*) / NULLIF((SELECT COUNT(*) FROM posts), 0), 2) FROM likes)    AS avg_likes_per_post,
                (SELECT ROUND(COUNT(*) / NULLIF((SELECT COUNT(*) FROM posts), 0), 2) FROM comments) AS avg_comments_per_post,
                (SELECT MAX(created_at) FROM posts) AS latest_post_at";
        return $this->db->query($sql)->fetch();
    }

    
    public function getUserEngagement() {
        $sql = "SELECT u.id, u.username, u.full_name,
                    (SELECT COUNT(*) FROM posts p WHERE p.user_id = u.id)    AS posts,
                    (SELECT COUNT(*) FROM comments c WHERE c.user_id = u.id) AS comments_written,
                    (SELECT COUNT(*) FROM likes l
                        JOIN posts p ON p.id = l.post_id
                        WHERE p.user_id = u.id)                             AS likes_received
                FROM users u
                ORDER BY likes_received DESC, posts DESC, u.username ASC";
        return $this->db->query($sql)->fetchAll();
    }

    
    public function getTopPosters() {
        $sql = "SELECT u.id, u.username, u.full_name,
                       COUNT(p.id) AS post_count,
                       MAX(p.created_at) AS last_post_at
                FROM users u
                LEFT JOIN posts p ON p.user_id = u.id
                GROUP BY u.id, u.username, u.full_name
                HAVING COUNT(p.id) >= ?
                ORDER BY post_count DESC, last_post_at DESC
                LIMIT 5";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([1]);
        return $stmt->fetchAll();
    }

    
    public function getMostLikedPosts() {
        $sql = "SELECT p.id, p.content, p.created_at, u.username, COUNT(l.id) AS like_count
                FROM posts p
                JOIN users u ON u.id = p.user_id
                LEFT JOIN likes l ON l.post_id = p.id
                GROUP BY p.id, p.content, p.created_at, u.username
                ORDER BY like_count DESC, p.created_at DESC
                LIMIT 5";
        return $this->db->query($sql)->fetchAll();
    }

    
    public function getAboveAverageLikedPosts() {
        $sql = "SELECT p.id, p.content, u.username, COUNT(l.id) AS like_count
                FROM posts p
                JOIN users u ON u.id = p.user_id
                JOIN likes l ON l.post_id = p.id
                GROUP BY p.id, p.content, u.username
                HAVING COUNT(l.id) > (
                    SELECT AVG(per_post.cnt)
                    FROM (SELECT COUNT(*) AS cnt FROM likes GROUP BY post_id) AS per_post
                )
                ORDER BY like_count DESC";
        return $this->db->query($sql)->fetchAll();
    }

    
    public function getPostsPerDay() {
        $sql = "SELECT DATE(created_at) AS post_day, COUNT(*) AS post_count
                FROM posts
                GROUP BY DATE(created_at)
                ORDER BY post_day DESC
                LIMIT 7";
        return $this->db->query($sql)->fetchAll();
    }

    
    public function getActivitySegments() {
        $sql = "SELECT CASE
                           WHEN t.post_count = 0 THEN 'No posts yet'
                           WHEN t.post_count < 3 THEN '1 to 2 posts'
                           ELSE '3 or more posts'
                       END AS segment,
                       COUNT(*) AS user_count
                FROM (
                    SELECT u.id, COUNT(p.id) AS post_count
                    FROM users u
                    LEFT JOIN posts p ON p.user_id = u.id
                    GROUP BY u.id
                ) AS t
                GROUP BY segment
                ORDER BY MIN(t.post_count)";
        return $this->db->query($sql)->fetchAll();
    }

        public function getUsersWithoutPosts() {
        $sql = "SELECT u.id, u.username, u.full_name, u.created_at
                FROM users u
                WHERE NOT EXISTS (SELECT 1 FROM posts p WHERE p.user_id = u.id)
                ORDER BY u.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    
    public function getDetailedPosts(array $filters, $sortKey, $limit100 = true) {
        $sql = "SELECT report.*
                FROM (
                    SELECT p.id, p.created_at, u.username, u.full_name, p.content,
                           (p.image IS NOT NULL AND p.image <> '') AS has_photo,
                           (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id)    AS like_count,
                           (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comment_count
                    FROM posts p
                    JOIN users u ON u.id = p.user_id
                    WHERE 1 = 1";
        $params = [];

        if ($filters['from'] !== '') {
            $sql .= " AND p.created_at >= ?";
            $params[] = $filters['from'] . ' 00:00:00';
        }
        if ($filters['to'] !== '') {
            $sql .= " AND p.created_at <= ?";
            $params[] = $filters['to'] . ' 23:59:59';
        }
        if ($filters['author'] !== '') {
            $sql .= " AND (u.username LIKE ? OR u.full_name LIKE ?)";
            $term = '%' . addcslashes($filters['author'], '%_\\') . '%';
            array_push($params, $term, $term);
        }
        if ($filters['keyword'] !== '') {
            $sql .= " AND p.content LIKE ?";
            $params[] = '%' . addcslashes($filters['keyword'], '%_\\') . '%';
        }

        $sql .= ") AS report WHERE report.like_count >= ?";
        $params[] = (int) $filters['min_likes'];

        if (!empty($filters['only_photos'])) {
            $sql .= " AND report.has_photo = 1";
        }

        $order = self::$sortOptions[$sortKey] ?? self::$sortOptions['newest'];
        $sql .= " ORDER BY " . $order;
        if ($limit100) {
            $sql .= " LIMIT 100";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
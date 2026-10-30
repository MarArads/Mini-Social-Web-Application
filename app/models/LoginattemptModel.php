<?php

require_once __DIR__ . '/../../config/database.php';


class LoginAttemptModel {
    const MAX_ATTEMPTS = 5;      
    const WINDOW_MINUTES = 5;    
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    private function cleanName($username) {
        return mb_substr(trim($username), 0, 50);
    }

    
    public function secondsLocked($username) {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) AS failures,
                        TIMESTAMPDIFF(SECOND, NOW(), DATE_ADD(MAX(attempted_at), INTERVAL ? MINUTE)) AS wait_seconds
                 FROM login_attempts
                 WHERE username = ? AND attempted_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)"
            );
            $stmt->execute([self::WINDOW_MINUTES, $this->cleanName($username), self::WINDOW_MINUTES]);
            $row = $stmt->fetch();

            if ($row && (int) $row['failures'] >= self::MAX_ATTEMPTS) {
                return max(1, (int) $row['wait_seconds']);
            }
        } catch (PDOException $e) {
            
            error_log('LoginAttemptModel::secondsLocked failed: ' . $e->getMessage());
        }
        return 0;
    }

    public function recordFailure($username) {
        try {
            $stmt = $this->db->prepare("INSERT INTO login_attempts (username, ip_address) VALUES (?, ?)");
            $stmt->execute([$this->cleanName($username), substr($_SERVER['REMOTE_ADDR'] ?? 'unknown', 0, 45)]);

            
            $this->db->query("DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 1 DAY)");
        } catch (PDOException $e) {
            error_log('LoginAttemptModel::recordFailure failed: ' . $e->getMessage());
        }
    }

    public function clear($username) {
        try {
            $stmt = $this->db->prepare("DELETE FROM login_attempts WHERE username = ?");
            $stmt->execute([$this->cleanName($username)]);
        } catch (PDOException $e) {
            error_log('LoginAttemptModel::clear failed: ' . $e->getMessage());
        }
    }
}
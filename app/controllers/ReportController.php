<?php

require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../models/ReportModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/AuthController.php';

class ReportController {
    private $reportModel;
    private $userModel;
    private $authController;

    public function __construct() {
        start_secure_session();
        $this->reportModel = new ReportModel();
        $this->userModel = new UserModel();
        $this->authController = new AuthController();
    }

    private function requireAdmin() {
        $this->authController->requireAuth();
        if (!$this->userModel->isAdmin($this->authController->getCurrentUserId())) {
            show_error_page(403, 'Access denied', 'Only the administrator can view the reports.');
        }
    }
    private function readFilters(&$problem) {
        $problem = '';

        $filters = [
            'from'        => $this->cleanDate($_GET['from'] ?? ''),
            'to'          => $this->cleanDate($_GET['to'] ?? ''),
            'author'      => mb_substr(trim($_GET['author'] ?? ''), 0, 50),
            'keyword'     => mb_substr(trim($_GET['keyword'] ?? ''), 0, 100),
            'min_likes'   => max(0, min(1000, (int) ($_GET['min_likes'] ?? 0))),
            'only_photos' => !empty($_GET['only_photos']),
        ];

        if (($_GET['from'] ?? '') !== '' && $filters['from'] === '') {
            $problem = 'The "from" date is not a valid date, so it was ignored.';
        }
        if (($_GET['to'] ?? '') !== '' && $filters['to'] === '') {
            $problem = 'The "to" date is not a valid date, so it was ignored.';
        }
        if ($filters['from'] !== '' && $filters['to'] !== '' && $filters['from'] > $filters['to']) {
            $problem = 'The "from" date must be on or before the "to" date. The date filter was ignored.';
            $filters['from'] = '';
            $filters['to'] = '';
        }

        return $filters;
    }

    private function cleanDate($value) {
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m)) {
            return '';
        }
        return checkdate((int) $m[2], (int) $m[3], (int) $m[1]) ? $value : '';
    }

    private function readSort() {
        $sort = $_GET['sort'] ?? 'newest';
        return in_array($sort, ReportModel::sortOptions(), true) ? $sort : 'newest';
    }

    public function index() {
        $this->requireAdmin();
        $currentUser = $this->userModel->findById($this->authController->getCurrentUserId());

        $filters = $this->readFilters($filterProblem);
        $sort = $this->readSort();
        $loadError = '';
        $summary = [];
        $detailed = [];

        try {
            $summary = $this->reportModel->getSummary();
            $engagement = $this->reportModel->getUserEngagement();
            $topPosters = $this->reportModel->getTopPosters();
            $mostLiked = $this->reportModel->getMostLikedPosts();
            $aboveAverage = $this->reportModel->getAboveAverageLikedPosts();
            $postsPerDay = $this->reportModel->getPostsPerDay();
            $segments = $this->reportModel->getActivitySegments();
            $noPosts = $this->reportModel->getUsersWithoutPosts();
            $detailed = $this->reportModel->getDetailedPosts($filters, $sort);
        } catch (PDOException $e) {
            
            error_log('Report failed: ' . $e->getMessage());
            $loadError = 'Sorry, the reports could not be loaded right now. Please try again later.';
            $engagement = $topPosters = $mostLiked = $aboveAverage = $postsPerDay = $segments = $noPosts = [];
        }

        include __DIR__ . '/../views/reports.php';
    }

    
    public function export() {
        $this->requireAdmin();

        $filters = $this->readFilters($ignored);
        $sort = $this->readSort();

        try {
            $rows = $this->reportModel->getDetailedPosts($filters, $sort, false);
        } catch (PDOException $e) {
            error_log('Report export failed: ' . $e->getMessage());
            set_flash('Sorry, the report could not be exported right now. Please try again later.');
            header('Location: reports.php');
            exit;
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="ured_posts_report_' . date('Y-m-d') . '.csv"');

        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); 
        fputcsv($out, ['Post ID', 'Date posted', 'Username', 'Full name', 'Post text', 'Has photo', 'Likes', 'Comments']);

        foreach ($rows as $row) {
            fputcsv($out, [
                $row['id'],
                $row['created_at'],
                $this->safeCell($row['username']),
                $this->safeCell($row['full_name']),
                $this->safeCell($row['content']),
                $row['has_photo'] ? 'Yes' : 'No',
                $row['like_count'],
                $row['comment_count'],
            ]);
        }
        fclose($out);
        exit;
    }

    
    private function safeCell($text) {
        $text = (string) $text;
        if ($text !== '' && strpos("=+-@\t\r", $text[0]) !== false) {
            return "'" . $text;
        }
        return $text;
    }
}
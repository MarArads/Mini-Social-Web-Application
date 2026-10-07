<?php

require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/AuthController.php';

class CommentController {
    private $commentModel;
    private $userModel;
    private $authController;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->commentModel = new CommentModel();
        $this->userModel = new UserModel();
        $this->authController = new AuthController();
    }

    public function add() {
        $this->authController->requireAuth();
        $currentUserId = $this->authController->getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postId = (int) ($_POST['post_id'] ?? 0);
            $content = trim($_POST['content'] ?? '');

            if ($postId > 0 && !empty($content)) {
                $content = mb_substr($content, 0, 300);
                $this->commentModel->create($postId, $currentUserId, $content);
            }
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? 'Newsfeed.php';
        header('Location: ' . $referer);
        exit;
    }

    public function edit() {
        $this->authController->requireAuth();
        $currentUserId = $this->authController->getCurrentUserId();

        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $comment = $this->commentModel->getById($id);

        if (!$comment || (int) $comment['user_id'] !== (int) $currentUserId) {
            header('Location: Newsfeed.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = trim($_POST['content'] ?? '');
            if (!empty($content)) {
                $content = mb_substr($content, 0, 300);
                $this->commentModel->update($id, $currentUserId, $content);
            }
            header('Location: Newsfeed.php');
            exit;
        }

        include __DIR__ . '/../views/comment_edit.php';
    }

    public function delete() {
        $this->authController->requireAuth();
        $currentUserId = $this->authController->getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $commentId = (int) ($_POST['comment_id'] ?? 0);
            if ($commentId > 0) {
                $this->commentModel->delete($commentId, $currentUserId);
            }
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? 'Newsfeed.php';
        header('Location: ' . $referer);
        exit;
    }
}

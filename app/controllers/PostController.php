<?php

require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/LikeModel.php';
require_once __DIR__ . '/AuthController.php';

class PostController {
    private $postModel;
    private $commentModel;
    private $userModel;
    private $likeModel;
    private $authController;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->postModel = new PostModel();
        $this->commentModel = new CommentModel();
        $this->userModel = new UserModel();
        $this->likeModel = new LikeModel();
        $this->authController = new AuthController();
    }

    public function newsfeed() {
        $this->authController->requireAuth();
        $currentUserId = $this->authController->getCurrentUserId();
        $currentUser = $this->userModel->findById($currentUserId);

        $search = trim($_GET['q'] ?? '');
        $rawPosts = $this->postModel->getAll($search, $currentUserId);

        $posts = [];
        foreach ($rawPosts as $post) {
            $post['comments'] = $this->commentModel->getByPostId($post['id']);
            $posts[] = $post;
        }

        include __DIR__ . '/../views/Newsfeed.php';
    }

    public function create() {
        $this->authController->requireAuth();
        $currentUserId = $this->authController->getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = trim($_POST['content'] ?? '');
            if (!empty($content)) {
                $imagePath = null;
                if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $fileTmp = $_FILES['image']['tmp_name'];
                    $fileSize = $_FILES['image']['size'];
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $fileTmp);
                    finfo_close($finfo);

                    $allowedMimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
                    if (isset($allowedMimes[$mime]) && $fileSize <= 5 * 1024 * 1024) {
                        $ext = $allowedMimes[$mime];
                        $uploadDir = __DIR__ . '/../../public/assets/uploads/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $filename = 'post_' . uniqid() . '.' . $ext;
                        if (move_uploaded_file($fileTmp, $uploadDir . $filename)) {
                            $imagePath = 'assets/uploads/' . $filename;
                        }
                    }
                }

                $this->postModel->create($currentUserId, $content, $imagePath);
            }
            header('Location: Newsfeed.php');
            exit;
        }

        $post = null;
        include __DIR__ . '/../views/post_form.php';
    }

    public function edit() {
        $this->authController->requireAuth();
        $currentUserId = $this->authController->getCurrentUserId();

        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $post = $this->postModel->getById($id, $currentUserId);

        if (!$post || (int) $post['user_id'] !== (int) $currentUserId) {
            header('Location: Newsfeed.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = trim($_POST['content'] ?? '');
            if (!empty($content)) {
                $imagePath = $post['image'];
                if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $fileTmp = $_FILES['image']['tmp_name'];
                    $fileSize = $_FILES['image']['size'];
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $fileTmp);
                    finfo_close($finfo);

                    $allowedMimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
                    if (isset($allowedMimes[$mime]) && $fileSize <= 5 * 1024 * 1024) {
                        $ext = $allowedMimes[$mime];
                        $uploadDir = __DIR__ . '/../../public/assets/uploads/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $filename = 'post_' . uniqid() . '.' . $ext;
                        if (move_uploaded_file($fileTmp, $uploadDir . $filename)) {
                            $imagePath = 'assets/uploads/' . $filename;
                        }
                    }
                }
                $this->postModel->update($id, $currentUserId, $content, $imagePath);
            }
            header('Location: Newsfeed.php');
            exit;
        }

        include __DIR__ . '/../views/post_form.php';
    }

    public function delete() {
        $this->authController->requireAuth();
        $currentUserId = $this->authController->getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) ($_POST['post_id'] ?? 0);
            $this->postModel->delete($id, $currentUserId);
        }

        header('Location: Newsfeed.php');
        exit;
    }

    public function like() {
        $this->authController->requireAuth();
        $currentUserId = $this->authController->getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postId = (int) ($_POST['post_id'] ?? 0);
            if ($postId > 0) {
                $result = $this->likeModel->toggleLike($postId, $currentUserId);
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode($result);
                    exit;
                }
            }
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? 'Newsfeed.php';
        header('Location: ' . $referer);
        exit;
    }
}

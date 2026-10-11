<?php

require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/ImageUpload.php';
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
        start_secure_session();
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
            $content = mb_substr(trim($_POST['content'] ?? ''), 0, 500);
            if (!empty($content)) {
                $upload = ImageUpload::save('image', 'post');
                if ($upload['error']) {
                    set_flash('Your post was not published. ' . $upload['error']);
                    header('Location: Newsfeed.php');
                    exit;
                }
                $this->postModel->create($currentUserId, $content, $upload['path']);
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
            $content = mb_substr(trim($_POST['content'] ?? ''), 0, 500);
            if (!empty($content)) {
                $imagePath = $post['image'];
                $upload = ImageUpload::save('image', 'post');
                if ($upload['error']) {
                    set_flash('Your changes were not saved. ' . $upload['error']);
                    header('Location: post_form.php?id=' . $id);
                    exit;
                }
                if ($upload['path']) {
                    $imagePath = $upload['path'];
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

        redirect_back('Newsfeed.php');
    }
}
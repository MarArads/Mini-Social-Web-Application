<?php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/AuthController.php';

class ProfileController {
    private $userModel;
    private $postModel;
    private $commentModel;
    private $authController;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->userModel = new UserModel();
        $this->postModel = new PostModel();
        $this->commentModel = new CommentModel();
        $this->authController = new AuthController();
    }

    public function show() {
        $this->authController->requireAuth();
        $currentUserId = $this->authController->getCurrentUserId();

        $id = (int) ($_GET['id'] ?? $currentUserId);
        $user = $this->userModel->findById($id);

        if (!$user) {
            header('Location: Newsfeed.php');
            exit;
        }

        $postCount = $this->postModel->countByUserId($id);
        $rawPosts = $this->postModel->getByUserId($id, $currentUserId);

        $posts = [];
        foreach ($rawPosts as $post) {
            $post['comments'] = $this->commentModel->getByPostId($post['id']);
            $posts[] = $post;
        }

        $currentUser = $this->userModel->findById($currentUserId);
        include __DIR__ . '/../views/profile.php';
    }

    public function edit() {
        $this->authController->requireAuth();
        $currentUserId = $this->authController->getCurrentUserId();
        $user = $this->userModel->findById($currentUserId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullName = trim($_POST['full_name'] ?? '');
            $bio = trim($_POST['bio'] ?? '');

            if (empty($fullName)) {
                $fullName = $user['username'];
            }

            $profileImage = $user['profile_image'];
            if (!empty($_FILES['profile_image']['name']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['profile_image']['tmp_name'];
                $fileSize = $_FILES['profile_image']['size'];
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
                    $filename = 'avatar_' . uniqid() . '.' . $ext;
                    if (move_uploaded_file($fileTmp, $uploadDir . $filename)) {
                        $profileImage = 'assets/uploads/' . $filename;
                    }
                }
            }

            $this->userModel->updateProfile($currentUserId, $fullName, $bio, $profileImage);
            header('Location: Profile.php?id=' . $currentUserId);
            exit;
        }

        $currentUser = $user;
        include __DIR__ . '/../views/profile_edit.php';
    }
}

<?php

require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/ImageUpload.php';
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
        start_secure_session();
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

            if (mb_strlen($fullName) > 100 || mb_strlen($bio) > 255) {
                set_flash('Name must be 100 characters or fewer, and bio 255 or fewer.');
                header('Location: profile_edit.php');
                exit;
            }

            $profileImage = $user['profile_image'];
            $upload = ImageUpload::save('profile_image', 'avatar');
            if ($upload['error']) {
                set_flash('Your profile was not updated. ' . $upload['error']);
                header('Location: profile_edit.php');
                exit;
            }
            if ($upload['path']) {
                $profileImage = $upload['path'];
            }

            $this->userModel->updateProfile($currentUserId, $fullName, $bio, $profileImage);
            header('Location: Profile.php?id=' . $currentUserId);
            exit;
        }

        $currentUser = $user;
        include __DIR__ . '/../views/profile_edit.php';
    }
}
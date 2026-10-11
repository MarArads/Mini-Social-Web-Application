<?php

require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/ImageUpload.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private $userModel;

    public function __construct() {
        start_secure_session();
        $this->userModel = new UserModel();
    }

    public function isLoggedIn() {
        return !empty($_SESSION['user_id']);
    }

    public function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: Login.php');
            exit;
        }
    }

    
    private function startUserSession($userId, $username) {
        session_regenerate_id(true);
        unset($_SESSION['csrf_token']);
        $_SESSION['user_id'] = (int) $userId;
        $_SESSION['username'] = $username;
    }

    public function login() {
        if ($this->isLoggedIn()) {
            header('Location: Newsfeed.php');
            exit;
        }

        $error = '';
        $success = '';

        if (isset($_GET['registered'])) {
            $success = 'Account created successfully! Please log in.';
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $error = 'Please enter both username and password.';
            } else {
                $user = $this->userModel->findByUsername($username);
                if ($user && password_verify($password, $user['password'])) {
                    $this->startUserSession($user['id'], $user['username']);
                    header('Location: Newsfeed.php');
                    exit;
                } else {
                    $error = 'Invalid username or password.';
                }
            }
        }

        include __DIR__ . '/../views/Login.php';
    }

    public function register() {
        if ($this->isLoggedIn()) {
            header('Location: Newsfeed.php');
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $fullName = trim($_POST['full_name'] ?? '');
            $bio = trim($_POST['bio'] ?? '');

            if (empty($username) || empty($password)) {
                $error = 'Username and password are required.';
            } elseif (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) {
                $error = 'Username must be 3-50 characters: letters, numbers, and underscores only.';
            } elseif (strlen($password) < 6 || strlen($password) > 72) {
                $error = 'Password must be between 6 and 72 characters.';
            } elseif ($password !== ($_POST['confirm_password'] ?? '')) {
                $error = 'Passwords do not match.';
            } elseif (mb_strlen($fullName) > 100) {
                $error = 'Full name must be 100 characters or fewer.';
            } elseif (mb_strlen($bio) > 255) {
                $error = 'Bio must be 255 characters or fewer.';
            } elseif ($this->userModel->existsUsername($username)) {
                $error = 'Username is already taken. Please choose another.';
            } else {
                if (empty($fullName)) {
                    $fullName = $username;
                }

                $upload = ImageUpload::save('profile_image', 'avatar');
                if ($upload['error']) {
                    $error = $upload['error'];
                } else {
                    $userId = $this->userModel->create($username, $password, $fullName, $bio, $upload['path']);
                    if ($userId) {
                        $this->startUserSession($userId, $username);
                        header('Location: Newsfeed.php');
                        exit;
                    } else {
                        $error = 'Registration failed. Please try again.';
                    }
                }
            }
        }

        include __DIR__ . '/../views/Register.php';
    }

    public function logout() {
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ($this->isLoggedIn() ? 'Newsfeed.php' : 'Login.php'));
            exit;
        }

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
        header('Location: Login.php');
        exit;
    }
}
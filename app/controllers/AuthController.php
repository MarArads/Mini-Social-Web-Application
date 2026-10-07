<?php

require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private $userModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
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
                    $_SESSION['user_id'] = (int) $user['id'];
                    $_SESSION['username'] = $user['username'];
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
            } elseif (strlen($username) < 3 || strlen($username) > 50) {
                $error = 'Username must be between 3 and 50 characters.';
            } elseif (strlen($password) < 6) {
                $error = 'Password must be at least 6 characters.';
            } elseif ($this->userModel->existsUsername($username)) {
                $error = 'Username is already taken. Please choose another.';
            } else {
                if (empty($fullName)) {
                    $fullName = $username;
                }

                $profileImage = null;
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
                    } else {
                        $error = 'Invalid image file. Only JPG, PNG, GIF, and WEBP under 5MB are allowed.';
                    }
                }

                if (empty($error)) {
                    $userId = $this->userModel->create($username, $password, $fullName, $bio, $profileImage);
                    if ($userId) {
                        $_SESSION['user_id'] = (int) $userId;
                        $_SESSION['username'] = $username;
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

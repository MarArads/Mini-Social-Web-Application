<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/PostController.php';
require_once __DIR__ . '/../app/controllers/CommentController.php';
require_once __DIR__ . '/../app/controllers/ProfileController.php';

$authController = new AuthController();
$postController = new PostController();
$commentController = new CommentController();
$profileController = new ProfileController();

$route = $_GET['action'] ?? $_GET['page'] ?? $_GET['route'] ?? '';
if (empty($route)) {
    $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    $filename = basename($uriPath);
    if (!empty($filename) && $filename !== 'index.php') {
        $route = $filename;
    }
}

$route = strtolower(trim($route));
$route = preg_replace('/\.php$/', '', $route);

switch ($route) {
    case 'login':
        $authController->login();
        break;

    case 'register':
        $authController->register();
        break;

    case 'logout':
        $authController->logout();
        break;

    case 'post_form':
    case 'post-form':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['id'])) {
                $postController->edit();
            } else {
                $postController->create();
            }
        } else {
            if (!empty($_GET['id'])) {
                $postController->edit();
            } else {
                $postController->create();
            }
        }
        break;

    case 'post_delete':
    case 'post-delete':
        $postController->delete();
        break;

    case 'like':
        $postController->like();
        break;

    case 'comment_add':
    case 'comment-add':
        $commentController->add();
        break;

    case 'comment_edit':
    case 'comment-edit':
        $commentController->edit();
        break;

    case 'comment_delete':
    case 'comment-delete':
        $commentController->delete();
        break;

    case 'profile':
        $profileController->show();
        break;

    case 'profile_edit':
    case 'profile-edit':
        $profileController->edit();
        break;

    case 'newsfeed':
    default:
        $postController->newsfeed();
        break;
}

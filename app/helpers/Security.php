<?php


function start_secure_session() {
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']),
        'httponly' => true,      
        'samesite' => 'Lax',    
    ]);
    session_start();
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function csrf_is_valid() {
    $sent = $_POST['csrf_token'] ?? '';
    $real = $_SESSION['csrf_token'] ?? '';
    return is_string($sent) && $real !== '' && hash_equals($real, $sent);
}


function csrf_require_valid_token() {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return;
    }
    if (csrf_is_valid()) {
        return;
    }

    http_response_code(419);
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">'
       . '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
       . '<title>Session expired</title></head>'
       . '<body style="font-family: Arial, sans-serif; text-align: center; padding: 3rem 1rem;">'
       . '<h1>Your session expired</h1>'
       . '<p>For your safety this request was blocked. Please go back, refresh the page, and try again.</p>'
       . '<p><a href="Newsfeed.php">Back to the newsfeed</a></p>'
       . '</body></html>';
    exit;
}


function redirect_back($default = 'Newsfeed.php') {
    $target = $default;
    $referer = $_SERVER['HTTP_REFERER'] ?? '';

    if ($referer !== '') {
        $parts = parse_url($referer);
        $host = $_SERVER['HTTP_HOST'] ?? '';
        if (!empty($parts['host'])) {
            $refHost = $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : '');
            if (strcasecmp($refHost, $host) === 0) {
                $target = ($parts['path'] ?? '/') . (isset($parts['query']) ? '?' . $parts['query'] : '');
            }
        }
    }

    header('Location: ' . $target);
    exit;
}

function set_flash($message, $type = 'error') {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function pull_flash() {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}
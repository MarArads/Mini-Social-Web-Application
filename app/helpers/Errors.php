<?php



function show_error_page($code, $title, $message) {
    
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    http_response_code($code);
    $errorCode = $code;
    $errorTitle = $title;
    $errorMessage = $message;
    include __DIR__ . '/../views/error.php';
    exit;
}

function register_error_handlers() {
    
    set_exception_handler(function ($e) {
        error_log('URED error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        show_error_page(
            500,
            'Something went wrong',
            'We could not finish that request. Please try again in a moment.'
        );
    });
}
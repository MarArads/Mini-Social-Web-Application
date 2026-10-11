<?php


class ImageUpload {
    const MAX_BYTES = 5242880; // 5 MB

    private static $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];

    public static function save($fileKey, $prefix) {
        if (empty($_FILES[$fileKey]['name'])) {
            return ['path' => null, 'error' => null];
        }

        $file = $_FILES[$fileKey];
        $problem = 'Invalid image file. Only JPG, PNG, GIF, and WEBP under 5MB are allowed.';

        if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            return ['path' => null, 'error' => $problem];
        }
        if ($file['size'] > self::MAX_BYTES) {
            return ['path' => null, 'error' => $problem];
        }

       
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!isset(self::$allowed[$mime]) || @getimagesize($file['tmp_name']) === false) {
            return ['path' => null, 'error' => $problem];
        }

        $uploadDir = __DIR__ . '/../../public/assets/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = $prefix . '_' . bin2hex(random_bytes(8)) . '.' . self::$allowed[$mime];
        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            return ['path' => null, 'error' => 'Could not save the image. Please try again.'];
        }

        return ['path' => 'assets/uploads/' . $filename, 'error' => null];
    }
}
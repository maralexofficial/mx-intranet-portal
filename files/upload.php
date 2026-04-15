<?php

$uploadDir = __DIR__ . '/uploads/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0775, true);
}

if (!isset($_FILES['file'])) {
    http_response_code(400);
    exit('No file uploaded');
}

$file = $_FILES['file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(500);
    exit('Upload error');
}

$filename = basename($file['name']);
$target = $uploadDir . $filename;

if (move_uploaded_file($file['tmp_name'], $target)) {
    echo 'OK';
} else {
    http_response_code(500);
    echo 'Failed';
}

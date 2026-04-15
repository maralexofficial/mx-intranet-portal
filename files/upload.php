<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$uploadDir = __DIR__ . '/uploads/';

echo "UPLOAD DEBUG START\n";
echo "Target dir: $uploadDir\n";

// 1. Check directory
if (!is_dir($uploadDir)) {
    echo "Directory missing, creating...\n";
    if (!mkdir($uploadDir, 0775, true)) {
        http_response_code(500);
        die("FAILED: cannot create upload dir\n");
    }
}

echo "Directory exists\n";
echo "Writable? " . (is_writable($uploadDir) ? "YES" : "NO") . "\n";

// 2. Check file
if (!isset($_FILES['file'])) {
    http_response_code(400);
    die("No file uploaded\n");
}

$file = $_FILES['file'];

echo "File info:\n";
print_r($file);

// 3. Upload error check
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(500);
    die("Upload error code: " . $file['error'] . "\n");
}

// 4. Filename
$filename = basename($file['name']);
$target = $uploadDir . $filename;

echo "Target file: $target\n";

// 5. Move file
if (move_uploaded_file($file['tmp_name'], $target)) {
    echo "OK - uploaded\n";
} else {
    $lastError = error_get_last();
    echo "FAILED move_uploaded_file\n";
    print_r($lastError);

    http_response_code(500);
}<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$uploadDir = __DIR__ . '/uploads/';

echo "UPLOAD DEBUG START\n";
echo "Target dir: $uploadDir\n";

if (!is_dir($uploadDir)) {
    echo "Directory missing, creating...\n";
    if (!mkdir($uploadDir, 0775, true)) {
        http_response_code(500);
        die("FAILED: cannot create upload dir\n");
    }
}

echo "Directory exists\n";
echo "Writable? " . (is_writable($uploadDir) ? "YES" : "NO") . "\n";

if (!isset($_FILES['file'])) {
    http_response_code(400);
    die("No file uploaded\n");
}

$file = $_FILES['file'];

echo "File info:\n";
print_r($file);

if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(500);
    die("Upload error code: " . $file['error'] . "\n");
}

$filename = basename($file['name']);
$target = $uploadDir . $filename;

echo "Target file: $target\n";

if (move_uploaded_file($file['tmp_name'], $target)) {
    echo "OK - uploaded\n";
} else {
    $lastError = error_get_last();
    echo "FAILED move_uploaded_file\n";
    print_r($lastError);

    http_response_code(500);
}
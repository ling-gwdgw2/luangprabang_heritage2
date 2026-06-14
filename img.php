<?php
// Serve images from filesystem (local) or MySQL (Railway)
header('Cache-Control: public, max-age=86400');
include_once 'config/database.php';

$filename = isset($_GET['f']) ? basename($_GET['f']) : '';
if (empty($filename)) { http_response_code(400); exit; }

// Try filesystem first (works locally)
$path = __DIR__ . '/uploads/' . $filename;
if (file_exists($path) && is_file($path)) {
    $mime = function_exists('mime_content_type') ? mime_content_type($path) : 'image/jpeg';
    header("Content-Type: $mime");
    readfile($path);
    exit;
}

// Fall back to DB (works on Railway after re-upload)
$safe = mysqli_real_escape_string($connect, $filename);
$result = mysqli_query($connect, "SELECT image_data, image_mime FROM image_store WHERE filename='$safe'");
if ($result && $row = mysqli_fetch_assoc($result)) {
    header("Content-Type: " . ($row['image_mime'] ?: 'image/jpeg'));
    echo base64_decode($row['image_data']);
    exit;
}

http_response_code(404);
exit;
?>

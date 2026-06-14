<?php
header('Content-Type: text/html; charset=utf-8');
include_once 'config/database.php';

echo "<h1>Image Debug</h1>";

// Check DB for house QR=001
$result = mysqli_query($connect, "SELECT house_id, qr_code, image_main FROM heritage_houses WHERE qr_code='001'");
if ($row = mysqli_fetch_assoc($result)) {
    echo "<p>DB image_main: <strong>" . htmlspecialchars($row['image_main']) . "</strong></p>";
    $file = 'uploads/' . $row['image_main'];
    echo "<p>File path checked: <strong>$file</strong></p>";
    echo "<p>File exists: <strong>" . (file_exists($file) ? 'YES ✅' : 'NO ❌') . "</strong></p>";
    echo "<p>Full server path: <strong>" . realpath($file) . "</strong></p>";
    echo "<hr><p>Direct image test:</p>";
    echo "<img src='$file' style='max-width:300px;border:1px solid red'><br><small>$file</small>";

    // Additional images
    $imgs = mysqli_query($connect, "SELECT image_path FROM heritage_images WHERE house_id=" . $row['house_id']);
    echo "<h2>Additional images</h2>";
    while ($img = mysqli_fetch_assoc($imgs)) {
        $path = 'uploads/' . $img['image_path'];
        $exists = file_exists($path) ? '✅' : '❌';
        echo "<p>$exists " . htmlspecialchars($img['image_path']) . "</p>";
        echo "<img src='$path' style='max-width:150px;border:1px solid blue'>";
    }
} else {
    echo "<p style='color:red'>No house with QR=001 found in DB</p>";
}

// List actual files in uploads/
echo "<h2>Files in uploads/ folder</h2>";
$files = glob('uploads/*');
if ($files) {
    foreach ($files as $f) echo "<p>" . htmlspecialchars($f) . "</p>";
} else {
    echo "<p style='color:red'>No files in uploads/ folder!</p>";
}
?>

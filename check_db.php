<?php
// ຟາຍ: check_db.php
include_once 'config/database.php';

echo "<h1>ກວດສອບຖານຂໍ້ມູນ</h1>";

if ($connect) {
    echo "<p style='color:green'>✅ ເຊື່ອມຕໍ່ຖານຂໍ້ມູນສຳເລັດ!</p>";
    
    $result = mysqli_query($connect, "SELECT COUNT(*) as count FROM heritage_houses");
    $row = mysqli_fetch_assoc($result);
    
    echo "<p>ຈຳນວນເຮືອນ: <strong>" . $row['count'] . "</strong></p>";
    
    if ($row['count'] > 0) {
        echo "<p style='color:green'>✅ ພ້ອມໃຊ້ງານແລ້ວ! ລອງເປີດ: <a href='heritage_detail.php?id=LP_H001'>heritage_detail.php?id=LP_H001</a></p>";
    } else {
        echo "<p style='color:red'>❌ ຍັງບໍ່ມີຂໍ້ມູນ! ກະລຸນານຳເຂົ້າຖານຂໍ້ມູນກ່ອນ</p>";
    }

    // Create image_store table for persistent image storage on Railway
    mysqli_query($connect, "CREATE TABLE IF NOT EXISTS image_store (
        id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255) UNIQUE NOT NULL,
        image_mime VARCHAR(100),
        image_data LONGBLOB,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p style='color:green'>✅ image_store table ready</p>";

    // Auto-import any images already in the uploads/ folder into image_store
    echo "<h2>ນຳເຂົ້າຮູບພາບຈາກ uploads/...</h2>";
    $upload_dir = __DIR__ . '/uploads/';
    $imported = 0;
    $skipped = 0;
    if (is_dir($upload_dir)) {
        foreach (glob($upload_dir . '*') as $filepath) {
            if (!is_file($filepath)) continue;
            $fname = basename($filepath);
            $check = mysqli_fetch_assoc(mysqli_query($connect, "SELECT id FROM image_store WHERE filename='" . mysqli_real_escape_string($connect, $fname) . "'"));
            if ($check) { $skipped++; continue; }
            $mime = function_exists('mime_content_type') ? mime_content_type($filepath) : 'image/jpeg';
            $data = base64_encode(file_get_contents($filepath));
            $fn_s = mysqli_real_escape_string($connect, $fname);
            $mime_s = mysqli_real_escape_string($connect, $mime);
            mysqli_query($connect, "INSERT INTO image_store (filename, image_mime, image_data) VALUES ('$fn_s','$mime_s','$data') ON DUPLICATE KEY UPDATE image_data='$data'");
            $imported++;
        }
    }
    echo "<p style='color:green'>✅ ນຳເຂົ້າ: <strong>$imported</strong> ຮູບ, ຂ້າມ: <strong>$skipped</strong> (ມີແລ້ວ)</p>";

    // Fix visit_logs schema: add missing columns if they don't exist
    echo "<h2>ກວດສອບ visit_logs...</h2>";
    $fixes = [
        "ALTER TABLE visit_logs ADD COLUMN IF NOT EXISTS visitor_device VARCHAR(255)",
        "ALTER TABLE visit_logs ADD COLUMN IF NOT EXISTS visit_time TIME",
    ];
    foreach ($fixes as $q) {
        if (mysqli_query($connect, $q)) {
            echo "<p style='color:green'>✅ " . htmlspecialchars($q) . "</p>";
        } else {
            echo "<p style='color:orange'>ℹ️ " . mysqli_error($connect) . "</p>";
        }
    }

    $vlog = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as c FROM visit_logs"));
    echo "<p>visit_logs records: <strong>" . $vlog['c'] . "</strong></p>";
} else {
    echo "<p style='color:red'>❌ ເຊື່ອມຕໍ່ບໍ່ສຳເລັດ! ກວດສອບ config/database.php</p>";
}
?>
<?php
// test_db.php
include_once 'config/database.php';

if ($connect) {
    echo "✅ ເຊື່ອມຕໍ່ຖານຂໍ້ມູນສຳເລັດ!";
    // ສະແດງຂໍ້ມູນການເຊື່ອມຕໍ່ (ເພື່ອໃຫ້ແນ່ໃຈ)
    echo "<br>Host: " . $host;
    echo "<br>DB Name: " . $dbname;
} else {
    echo "❌ ເຊື່ອມຕໍ່ບໍ່ສຳເລັດ!";
}
?>
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
} else {
    echo "<p style='color:red'>❌ ເຊື່ອມຕໍ່ບໍ່ສຳເລັດ! ກວດສອບ config/database.php</p>";
}
?>
<?php
// ໄຟລ໌: check_database.php
include_once 'config/database.php';

echo "<h1>🔍 ກວດສອບຂໍ້ມູນໃນຖານຂໍ້ມູນ</h1>";

// ກວດສອບຕາຕະລາງ heritage_houses
$query = "SELECT * FROM heritage_houses WHERE status = 'active'";
$result = mysqli_query($connect, $query);

echo "<h2>📋 ລາຍຊື່ເຮືອນມໍລະດົກທີ່ເປີດໃຊ້ງານ:</h2>";

if (mysqli_num_rows($result) > 0) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #2d6a4f; color: white;'>
            <th>ເຮືອນກູອັນນີ້</th>
            <th>QR Code</th>
            <th>ຊື່ເຮືອນ (ລາວ)</th>
            <th>ຊື່ເຮືອນ (ອັງກິດ)</th>
            <th>ສະຖານະ</th>
            <th>ທົດສອບລິ້ງ</th>
          </tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        $test_url = "heritage_detail.php?id=" . $row['qr_code'];
        echo "<tr>
                <td>{$row['house_id']}</td>
                <td><code>{$row['qr_code']}</code></td>
                <td>{$row['house_name_lo']}</td>
                <td>{$row['house_name_en']}</td>
                <td><span style='background: green; color: white; padding: 3px 10px; border-radius: 20px;'>{$row['status']}</span></td>
                <td><a href='{$test_url}' target='_blank'>ທົດສອບລິ້ງ</a></td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>⚠️ ບໍ່ມີຂໍ້ມູນເຮືອນມໍລະດົກ! ກະລຸນາເພີ່ມຂໍ້ມູນກ່ອນ.</p>";
}

// ສະແດງ QR Code ທັງໝົດທີ່ມີ
echo "<h2>📱 ສ້າງ QR Code ສຳລັບທົດສອບ:</h2>";
echo "<div style='display: flex; flex-wrap: wrap; gap: 20px;'>";

// ດຶງຂໍ້ມູນມາສ້າງ QR Code
$qr_query = "SELECT qr_code, house_name_lo FROM heritage_houses WHERE status = 'active'";
$qr_result = mysqli_query($connect, $qr_query);

if (mysqli_num_rows($qr_result) > 0) {
    while ($row = mysqli_fetch_assoc($qr_result)) {
        $url = "http://localhost/luangprabang_heritage/heritage_detail.php?id=" . $row['qr_code'];
        $qr_img = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . urlencode($url);
        echo "
        <div style='text-align: center; border: 1px solid #ccc; padding: 10px; border-radius: 10px;'>
            <h4>{$row['house_name_lo']}</h4>
            <img src='{$qr_img}' style='width: 120px;'>
            <p><code>{$row['qr_code']}</code></p>
            <a href='{$url}' target='_blank'>ເບິ່ງຂໍ້ມູນ</a>
        </div>";
    }
}
echo "</div>";

// ຄຳແນະນຳ
echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 10px; margin-top: 20px;'>
        <h3>📝 ຄຳແນະນຳ:</h3>
        <p>1. ຖ້າບໍ່ມີຂໍ້ມູນ, ໃຫ້ເພີ່ມຂໍ້ມູນທີ່ <a href='admin/add_house.php'>admin/add_house.php</a></p>
        <p>2. ຖ້າມີຂໍ້ມູນແລ້ວ, ໃຫ້ສະແກນ QR Code ຂ້າງເທິງນີ້ເພື່ອທົດສອບ</p>
        <p>3. QR Code ຕ້ອງມີຄ່າເປັນ <code>LP_H001</code> ຫຼື ຄ່າທີ່ກົງກັບໃນຖານຂໍ້ມູນ</p>
      </div>";
?>
<?php
// ໄຟລ໌: debug_delete.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 ກວດສອບການລຶບຂໍ້ມູນ</h1>";

// ກວດສອບການເຊື່ອມຕໍ່ຖານຂໍ້ມູນ
include_once 'config/database.php';
if ($connect) {
    echo "<p style='color:green'>✅ ການເຊື່ອມຕໍ່ຖານຂໍ້ມູນສຳເລັດ</p>";
} else {
    echo "<p style='color:red'>❌ ການເຊື່ອມຕໍ່ຖານຂໍ້ມູນລົ້ມເຫຼວ</p>";
}

// ສະແດງລາຍຊື່ເຮືອນ
$query = "SELECT house_id, house_name_lo, qr_code FROM heritage_houses ORDER BY house_id DESC";
$result = mysqli_query($connect, $query);

echo "<h2>📋 ລາຍຊື່ເຮືອນມໍລະດົກ:</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr style='background: #2d6a4f; color: white;'>
        <th>ID</th>
        <th>ຊື່ເຮືອນ</th>
        <th>QR Code</th>
        <th>ການຈັດການ</th>
      </tr>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>{$row['house_id']}</td>";
    echo "<td>{$row['house_name_lo']}</td>";
    echo "<td><code>{$row['qr_code']}</code></td>";
    echo "<td>
            <form method='POST' style='display:inline;' onsubmit='return confirm(\"ຕ້ອງການລຶບ {$row['house_name_lo']} ແທ້ບໍ່?\")'>
                <input type='hidden' name='delete_id' value='{$row['house_id']}'>
                <button type='submit' name='delete_submit' style='background:#dc3545; color:white; border:none; padding:5px 15px; border-radius:5px; cursor:pointer;'>
                    🗑️ ລຶບ ID: {$row['house_id']}
                </button>
            </form>
          </td>";
    echo "</tr>";
}
echo "</table>";

// ຈັດການການລຶບ
if (isset($_POST['delete_submit']) && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    echo "<hr><h2>📝 ກຳລັງລຶບ ID: $delete_id</h2>";
    
    // 1. ດຶງຊື່ຮູບພາບ
    $img_query = "SELECT image_main FROM heritage_houses WHERE house_id = $delete_id";
    $img_result = mysqli_query($connect, $img_query);
    $house_data = mysqli_fetch_assoc($img_result);
    
    if ($house_data) {
        // ລຶບຮູບພາບ
        if (!empty($house_data['image_main']) && file_exists('uploads/' . $house_data['image_main'])) {
            if (unlink('uploads/' . $house_data['image_main'])) {
                echo "<p style='color:green'>✅ ລຶບຮູບພາບສຳເລັດ: " . $house_data['image_main'] . "</p>";
            } else {
                echo "<p style='color:orange'>⚠️ ບໍ່ສາມາດລຶບຮູບພາບ: " . $house_data['image_main'] . "</p>";
            }
        }
        
        // ລຶບຂໍ້ມູນໃນຕາຕະລາງທີ່ກ່ຽວຂ້ອງ
        mysqli_query($connect, "DELETE FROM heritage_images WHERE house_id = $delete_id");
        mysqli_query($connect, "DELETE FROM visit_logs WHERE house_id = $delete_id");
        
        // ລຶບເຮືອນ
        $delete_query = "DELETE FROM heritage_houses WHERE house_id = $delete_id";
        if (mysqli_query($connect, $delete_query)) {
            echo "<p style='color:green; font-size:18px;'>✅ ລຶບຂໍ້ມູນສຳເລັດ!</p>";
            echo "<script>setTimeout(function() { window.location.href = 'debug_delete.php'; }, 1500);</script>";
        } else {
            echo "<p style='color:red'>❌ ຜິດພາດ: " . mysqli_error($connect) . "</p>";
        }
    } else {
        echo "<p style='color:red'>❌ ບໍ່ພົບຂໍ້ມູນເຮືອນ ID: $delete_id</p>";
    }
}

mysqli_close($connect);
?>
<?php
// ໄຟລ໌: mobile_qr.php
include_once 'config/database.php';

// ເອົາ IP ຂອງຄອມພິວເຕີອັດຕະໂນມັດ
$server_ip = $_SERVER['SERVER_ADDR'];
$base_url = "http://{$server_ip}/luangprabang_heritage/";

// ຖ້າ IP ເປັນ 127.0.0.1 ຫຼື ::1, ໃຫ້ໃຊ້ IP ຈາກຄູ່ມື
if ($server_ip == '127.0.0.1' || $server_ip == '::1') {
    $server_ip = '192.168.1.100'; // ປ່ຽນເປັນ IP ຂອງທ່ານ
    $base_url = "http://{$server_ip}/luangprabang_heritage/";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code ສຳລັບໂທລະສັບ</title>
    <style>
        body {
            font-family: 'Noto Sans Lao', 'Phetsarath OT', sans-serif;
            background: #f5f0e8;
            text-align: center;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
        }
        .info-box {
            background: #2d6a4f;
            color: white;
            padding: 15px;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        .info-box code {
            background: white;
            color: #2d6a4f;
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 18px;
        }
        .qr-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .qr-card img {
            width: 200px;
            height: 200px;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 10px;
        }
        .btn {
            display: inline-block;
            background: #2d6a4f;
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            margin: 5px;
        }
        .step {
            background: #e9ecef;
            border-radius: 15px;
            padding: 15px;
            text-align: left;
            margin-top: 20px;
        }
        .step-number {
            background: #2d6a4f;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-block;
            text-align: center;
            line-height: 28px;
            margin-right: 10px;
        }
        @media print {
            .no-print { display: none; }
            .qr-card { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📱 QR Code ສຳລັບໂທລະສັບ</h1>
        
        <div class="info-box">
            <p>🔗 ໃຊ້ລິ້ງນີ້ໃນໂທລະສັບຂອງທ່ານ:</p>
            <code><?php echo $base_url; ?></code>
        </div>
        
        <?php
        $query = "SELECT qr_code, house_name_lo, house_name_en FROM heritage_houses WHERE status = 'active'";
        $result = mysqli_query($connect, $query);
        
        if (mysqli_num_rows($result) > 0) {
            while ($house = mysqli_fetch_assoc($result)) {
                $url = $base_url . "heritage_detail.php?id=" . $house['qr_code'];
                $qr_url = "https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=" . urlencode($url);
                ?>
                <div class="qr-card">
                    <h3><?php echo htmlspecialchars($house['house_name_lo']); ?></h3>
                    <p><?php echo htmlspecialchars($house['house_name_en']); ?></p>
                    <img src="<?php echo $qr_url; ?>" alt="QR Code">
                    <p><code><?php echo $house['qr_code']; ?></code></p>
                    <a href="<?php echo $url; ?>" target="_blank" class="btn">🔗 ທົດສອບລິ້ງ</a>
                    <a href="<?php echo $qr_url; ?>" download class="btn">💾 ດາວໂຫຼດ QR Code</a>
                </div>
                <?php
            }
        } else {
            echo '<div class="qr-card" style="color:red;">
                    <p>⚠️ ຍັງບໍ່ມີຂໍ້ມູນເຮືອນມໍລະດົກ</p>
                    <p>ກະລຸນາເພີ່ມຂໍ້ມູນທີ່ <a href="admin/add_house.php">admin/add_house.php</a></p>
                  </div>';
        }
        ?>
        
        <div class="step no-print">
            <h3>📋 ຂັ້ນຕອນການໃຊ້ງານ</h3>
            <p><span class="step-number">1</span> ເຊື່ອມຕໍ່ Wi-Fi ດຽວກັນລະຫວ່າງໂທລະສັບ ແລະ ຄອມພິວເຕີ</p>
            <p><span class="step-number">2</span> ເປີດ Browser ໃນໂທລະສັບ ແລ້ວພິມ: <code><?php echo $base_url; ?></code></p>
            <p><span class="step-number">3</span> ກົດ "ເປີດກ້ອງສະແກນ" ແລະ ອະນຸຍາດໃຊ້ກ້ອງ</p>
            <p><span class="step-number">4</span> ນຳກ້ອງໄປສະແກນ QR Code ຂ້າງເທິງ ຫຼື ທີ່ຕິດຕາມເຮືອນ</p>
        </div>
        
        <div class="step no-print">
            <h3>🖨️ ວິທີພິມ QR Code</h3>
            <p>1. ກົດຂວາໃສ່ຮູບ QR Code → "Save image as..."</p>
            <p>2. ຫຼື ກົດປຸ່ມ "ດາວໂຫຼດ QR Code"</p>
            <p>3. ນຳໄປພິມ ແລະ ຕິດຕາມປ້າຍເຮືອນມໍລະດົກ</p>
        </div>
        
        <div class="no-print">
            <button class="btn" onclick="window.location.href='index.php'">🏠 ກັບໄປໜ້າຫຼັກ</button>
            <button class="btn" onclick="window.print()">🖨️ ພິມໜ້ານີ້</button>
        </div>
    </div>
</body>
</html>
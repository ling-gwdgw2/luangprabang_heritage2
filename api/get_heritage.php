<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

include_once __DIR__ . '/../config/database.php';

$response = ['success' => false, 'message' => '', 'data' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qr_code = isset($_POST['qr_code']) ? trim($_POST['qr_code']) : '';
    $lang = isset($_POST['lang']) ? $_POST['lang'] : 'lo';
    
    if (empty($qr_code)) {
        $response['message'] = 'QR Code ບໍ່ຖືກຕ້ອງ';
        echo json_encode($response);
        exit;
    }
    
    if (strpos($qr_code, 'heritage_detail.php') !== false) {
        preg_match('/[?&]id=([^&]+)/', $qr_code, $matches);
        if (isset($matches[1])) $qr_code = $matches[1];
    }
    
    $qr_code = mysqli_real_escape_string($connect, $qr_code);
    $query = "SELECT * FROM heritage_houses WHERE qr_code = '$qr_code' AND status = 'active'";
    $result = mysqli_query($connect, $query);
    
    if (mysqli_num_rows($result) == 0 && is_numeric($qr_code)) {
        $query = "SELECT * FROM heritage_houses WHERE house_id = " . intval($qr_code) . " AND status = 'active'";
        $result = mysqli_query($connect, $query);
    }
    
    if (mysqli_num_rows($result) > 0) {
        $house = mysqli_fetch_assoc($result);
        $imgQuery = "SELECT * FROM heritage_images WHERE house_id = " . $house['house_id'] . " ORDER BY display_order";
        $imgResult = mysqli_query($connect, $imgQuery);
        $images = [];
        while ($img = mysqli_fetch_assoc($imgResult)) $images[] = $img;
        $house['images'] = $images;
        $response['success'] = true;
        $response['data'] = $house;
    } else {
        $response['message'] = 'ບໍ່ພົບຂໍ້ມູນເຮືອນມໍລະດົກນີ້';
    }
}
echo json_encode($response);
?>
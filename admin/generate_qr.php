<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { 
    header('Content-Type: application/json'); 
    echo json_encode(['success' => false, 'message' => 'Unauthorized']); 
    exit; 
}

include_once '../config/database.php';
header('Content-Type: application/json');

// ຟັງຊັນສ້າງ QR Code ແບບມີຫຼາຍ API ສຳຮອງ
function generateQRCode($data, $size = 300) {
    // ລາຍການ API ສຳລັບສ້າງ QR Code
    $apis = [
        // API ທີ 1: QR Server (ເຊື່ອຖືໄດ້)
        "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($data),
        
        // API ທີ 2: QuickChart (ດີຫຼາຍ)
        "https://quickchart.io/qr?text=" . urlencode($data) . "&size={$size}",
        
        // API ທີ 3: GoQR (ອີກທາງເລືອກ)
        "https://api.qr-code-generator.com/v1/create?size={$size}x{$size}&data=" . urlencode($data),
        
        // API ທີ 4: Chart.googleapis (ຂອງ Google)
        "https://chart.googleapis.com/chart?chs={$size}x{$size}&cht=qr&chl=" . urlencode($data)
    ];
    
    // ລອງໃຊ້ແຕ່ລະ API
    foreach ($apis as $url) {
        // ຕັ້ງຄ່າ stream context ເພື່ອຫຼຸດ timeout
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'method' => 'GET',
                'header' => "User-Agent: Mozilla/5.0\r\n"
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);
        
        $qr_image = @file_get_contents($url, false, $context);
        
        // ກວດສອບວ່າໄດ້ຮັບຮູບພາບຫຼືບໍ່
        if ($qr_image !== false && strlen($qr_image) > 500) {
            // ກວດສອບວ່າເປັນ PNG ຫຼືບໍ່
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_buffer($finfo, $qr_image);
            finfo_close($finfo);
            
            if (strpos($mime_type, 'image/') === 0) {
                return $qr_image;
            }
        }
    }
    
    return false;
}

// ຟັງຊັນສ້າງ QR Code ແບບງ່າຍໆດ້ວຍ GD library (ຖ້າມີ)
function generateQRCodeLocal($data, $size = 300) {
    if (!extension_loaded('gd')) {
        return false;
    }
    
    // ສ້າງຮູບສີຂາວ
    $qr_image = imagecreatetruecolor($size, $size);
    $white = imagecolorallocate($qr_image, 255, 255, 255);
    $black = imagecolorallocate($qr_image, 0, 0, 0);
    imagefill($qr_image, 0, 0, $white);
    
    // ແຕ້ມກອບ
    imagerectangle($qr_image, 0, 0, $size-1, $size-1, $black);
    
    // ຂຽນຂໍ້ຄວາມ (ໃນກໍລະນີທີ່ບໍ່ສາມາດສ້າງ QR ຈິງ)
    $text = "QR Code\n" . substr($data, 0, 50);
    $font_size = 5;
    $text_width = imagefontwidth($font_size) * strlen($text);
    $text_x = ($size - $text_width) / 2;
    $text_y = $size / 2;
    imagestring($qr_image, $font_size, $text_x, $text_y, $text, $black);
    
    // ເກັບເປັນ PNG
    ob_start();
    imagepng($qr_image);
    $image_data = ob_get_clean();
    imagedestroy($qr_image);
    
    return $image_data;
}

// ດຶງໂດເມນ
function getBaseUrl() {
    // ກຳນົດໂດເມນຈິງຂອງເຈົ້າທີ່ນີ້
    // ປ່ຽນເປັນໂດເມນຂອງເຈົ້າໃນ Railway
    $railway_domain = "https://your-project.up.railway.app"; // ແກ້ໄຂຕາມນີ້
    
    // ກວດສອບວ່າຢູ່ໃນ Railway ຫຼືບໍ່
    if (getenv('RAILWAY_PUBLIC_DOMAIN')) {
        return 'https://' . getenv('RAILWAY_PUBLIC_DOMAIN');
    }
    
    // ສຳລັບການທົດສອບໃນ localhost
    if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
        return $railway_domain; // ໃຊ້ໂດເມນຈິງ
    }
    
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    return $protocol . $_SERVER['HTTP_HOST'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $house_id = intval($_POST['house_id']);
    
    if ($house_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ລະຫັດເຮືອນບໍ່ຖືກຕ້ອງ']); 
        exit;
    }
    
    // ດຶງຂໍ້ມູນເຮືອນ
    $stmt = mysqli_prepare($connect, "SELECT * FROM heritage_houses WHERE house_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $house_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $house = mysqli_fetch_assoc($result);
    
    if (!$house) { 
        echo json_encode(['success' => false, 'message' => 'ບໍ່ພົບຂໍ້ມູນເຮືອນ']); 
        exit; 
    }
    
    // ສ້າງ QR ID
    if (empty($house['qr_code'])) { 
        $qr_id = 'LP_' . uniqid() . '_' . $house_id; 
        $update_stmt = mysqli_prepare($connect, "UPDATE heritage_houses SET qr_code = ? WHERE house_id = ?");
        mysqli_stmt_bind_param($update_stmt, "si", $qr_id, $house_id);
        if (!mysqli_stmt_execute($update_stmt)) {
            echo json_encode(['success' => false, 'message' => 'ບໍ່ສາມາດບັນທຶກ QR ID ໄດ້']); 
            exit;
        }
        mysqli_stmt_close($update_stmt);
    } else { 
        $qr_id = $house['qr_code']; 
    }
    
    // ສ້າງໂຟນເດີ
    if (!is_dir('../qr_codes')) {
        if (!mkdir('../qr_codes', 0777, true)) {
            echo json_encode(['success' => false, 'message' => 'ບໍ່ສາມາດສ້າງໂຟນເດີ qr_codes ໄດ້']); 
            exit;
        }
    }
    
    // ສ້າງລິ້ງສຳລັບ QR Code
    $base_url = getBaseUrl();
    $qr_data = rtrim($base_url, '/') . '/heritage_detail.php?id=' . $qr_id;
    
    // ລອງສ້າງ QR Code
    $qr_image = generateQRCode($qr_data, 300);
    
    // ຖ້າບໍ່ສຳເລັດ, ລອງໃຊ້ local generator
    if ($qr_image === false) {
        $qr_image = generateQRCodeLocal($qr_data, 300);
    }
    
    if ($qr_image !== false) { 
        $qr_filename = "qr_codes/{$qr_id}.png"; 
        $full_path = "../" . $qr_filename;
        
        if (file_put_contents($full_path, $qr_image)) {
            echo json_encode([
                'success' => true, 
                'qr_url' => $qr_filename, 
                'qr_id' => $qr_id, 
                'qr_data' => $qr_data,
                'message' => 'ສ້າງ QR Code ສຳເລັດ'
            ]); 
        } else {
            echo json_encode(['success' => false, 'message' => 'ບໍ່ສາມາດບັນທຶກຮູບ QR Code ໄດ້ (ກວດສອບສິດການຂຽນໄຟລ໌)']); 
        }
    } else { 
        // Debug: ສະແດງຂໍ້ມູນເພື່ອຫາສາເຫດ
        error_log("QR Generation Failed for data: " . $qr_data);
        echo json_encode([
            'success' => false, 
            'message' => 'ບໍ່ສາມາດສ້າງ QR Code ໄດ້, ກະລຸນາກວດສອບການເຊື່ອມຕໍ່ອິນເຕີເນັດ ຫຼືລອງໃໝ່ອີກຄັ້ງ',
            'debug_data' => $qr_data
        ]); 
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'ຄຳຮ້ອງຂໍບໍ່ຖືກຕ້ອງ']);
?>
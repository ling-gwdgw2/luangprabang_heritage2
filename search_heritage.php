<?php
header('Content-Type: application/json');
include_once 'config/database.php';

$keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';
$lang = isset($_POST['lang']) ? trim($_POST['lang']) : 'lo';

if (empty($keyword)) {
    echo json_encode([]);
    exit;
}

$escaped = mysqli_real_escape_string($connect, $keyword);

// Search in qr_code, house_number, house_name_lo, house_name_en
$sql = "SELECT house_id, qr_code, house_number, house_name_lo, house_name_en, architectural_style_lo, architectural_style_en 
        FROM heritage_houses 
        WHERE status = 'active' AND (
            qr_code LIKE '%$escaped%' OR 
            house_number LIKE '%$escaped%' OR 
            house_name_lo LIKE '%$escaped%' OR 
            house_name_en LIKE '%$escaped%'
        ) LIMIT 20";

$result = mysqli_query($connect, $sql);
$data = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

echo json_encode($data);
mysqli_close($connect);
?>

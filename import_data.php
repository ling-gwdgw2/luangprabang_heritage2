<?php
include_once __DIR__ . '/config/database.php';

// ข้อมูลตัวอย่าง (หรืออ่านจาก JSON)
$heritage_data = [
    ['qr_code' => 'TEST001', 'house_number' => 'T001', 'house_name_lo' => 'ເຮືອນມໍລະດົກ 1', 'house_name_en' => 'Heritage House 1'],
    ['qr_code' => 'TEST002', 'house_number' => 'T002', 'house_name_lo' => 'ເຮືອນມໍລະດົກ 2', 'house_name_en' => 'Heritage House 2'],
];

foreach ($heritage_data as $item) {
    $qr = mysqli_real_escape_string($connect, $item['qr_code']);
    $num = mysqli_real_escape_string($connect, $item['house_number']);
    $lo = mysqli_real_escape_string($connect, $item['house_name_lo']);
    $en = mysqli_real_escape_string($connect, $item['house_name_en']);
    
    $sql = "INSERT INTO heritage_houses (qr_code, house_number, house_name_lo, house_name_en) 
            VALUES ('$qr', '$num', '$lo', '$en')
            ON DUPLICATE KEY UPDATE house_number='$num', house_name_lo='$lo', house_name_en='$en'";
    
    if (mysqli_query($connect, $sql)) {
        echo "✔ ເພີ່ມ/ອັບເດດ: $lo<br>";
    } else {
        echo "✖ ຜິດພາດ: " . mysqli_error($connect) . "<br>";
    }
}

mysqli_close($connect);
?>
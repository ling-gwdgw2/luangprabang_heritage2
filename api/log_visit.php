<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $house_id = isset($_POST['house_id']) ? intval($_POST['house_id']) : 0;
    $visitor_ip = mysqli_real_escape_string($connect, $_SERVER['REMOTE_ADDR']);
    $visitor_device = isset($_SERVER['HTTP_USER_AGENT']) ? mysqli_real_escape_string($connect, substr($_SERVER['HTTP_USER_AGENT'], 0, 255)) : '';
    $visit_date = date('Y-m-d');
    $visit_time = date('H:i:s');
    if ($house_id > 0) {
        $query = "INSERT INTO visit_logs (house_id, visitor_ip, visitor_device, visit_date, visit_time) VALUES ($house_id, '$visitor_ip', '$visitor_device', '$visit_date', '$visit_time')";
        mysqli_query($connect, $query);
    }
}
echo json_encode(['success' => true]);
?>
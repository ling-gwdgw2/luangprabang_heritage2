<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// ເອີ້ນຫາຟາຍເຊື່ອມຕໍ່ຖານຂໍ້ມູນຢູ່ Root
include_once '../config/database.php'; 
$conn = $connect; 

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ດຶງຄ່າການເຊື່ອມຕໍ່ $conn ມາໃຊ້
    $username = isset($_POST['username']) ? mysqli_real_escape_string($conn, $_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($username) || empty($password)) {
        $response['message'] = 'ກະລຸນາປ້ອນຊື່ຜູ້ໃຊ້ ແລະ ລະຫັດຜ່ານ';
        echo json_encode($response);
        exit;
    }
    
    // ເອີ້ນດຶງຂໍ້ມູນຈາກ Table ທີ່ຊື່ heritage_houses ຕາມຮູບຖານຂໍ້ມູນຂອງເຈົ້າ
$query = "SELECT * FROM users WHERE username = '$username' AND status = 'active'";    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // ກວດສອບລະຫັດຜ່ານທີ່ເຂົ້າລະຫັດໄວ້
        if (password_verify($password, $user['password'])) {
            
            // ເກັບ Session ເທົ່າທີ່ຈຳເປັນ ແລະ ກວດເຊັກຄ່າ NULL ຢ່າງປອດໄພ
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = isset($user['user_id']) ? $user['user_id'] : 1;
            $_SESSION['admin_name'] = !empty($user['fullname_lo']) ? $user['fullname_lo'] : $user['username'];
            $_SESSION['admin_role'] = isset($user['role']) ? $user['role'] : 'admin';  
            
            $response['success'] = true;
            $response['message'] = 'ເຂົ້າສູ່ລະບົບສຳເລັດ';
        } else {
            $response['message'] = 'ລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ';
        }
    } else {
        $response['message'] = 'ບໍ່ພົບຊື່ຜູ້ໃຊ້ນີ້ໃນລະບົບ';
    }
}

echo json_encode($response);
?>
<?php
session_start();
header('Content-Type: application/json');

include_once '../config/database.php';
include_once 'check_permission.php';

// ກວດສອບສິດທິ
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true || !canDelete()) {
    echo json_encode(['success' => false, 'message' => 'ທ່ານບໍ່ມີສິດລຶບຂໍ້ມູນ']);
    exit;
}

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delete_id = isset($_POST['delete_id']) ? intval($_POST['delete_id']) : 0;

    if ($delete_id <= 0) {
        $response['message'] = 'ລະຫັດບໍ່ຖືກຕ້ອງ';
        echo json_encode($response);
        exit;
    }

    // 1. ດຶງຂໍ້ມູນຮູບພາບຫຼັກ
    $query = "SELECT image_main FROM heritage_houses WHERE house_id = $delete_id";
    $result = mysqli_query($connect, $query);
    $house = mysqli_fetch_assoc($result);

    if ($house) {
        // 2. ລຶບຮູບພາບຫຼັກ
        if (!empty($house['image_main']) && file_exists('../uploads/' . $house['image_main'])) {
            @unlink('../uploads/' . $house['image_main']);
        }

        // 3. ລຶບຮູບພາບເພີ່ມເຕີມ
        $extra = mysqli_query($connect, "SELECT image_path FROM heritage_images WHERE house_id = $delete_id");
        while ($row = mysqli_fetch_assoc($extra)) {
            if (!empty($row['image_path']) && file_exists('../uploads/' . $row['image_path'])) {
                @unlink('../uploads/' . $row['image_path']);
            }
        }

        // 4. ລຶບຂໍ້ມູນທີ່ກ່ຽວຂ້ອງ
        mysqli_query($connect, "DELETE FROM heritage_images WHERE house_id = $delete_id");
        mysqli_query($connect, "DELETE FROM visit_logs WHERE house_id = $delete_id");

        // 5. ລຶບເຮືອນ
        $delete = mysqli_query($connect, "DELETE FROM heritage_houses WHERE house_id = $delete_id");

        if ($delete) {
            $response['success'] = true;
            $response['message'] = 'ລຶບຂໍ້ມູນສຳເລັດ';
        } else {
            $response['message'] = 'ຜິດພາດ: ' . mysqli_error($connect);
        }
    } else {
        $response['message'] = 'ບໍ່ພົບຂໍ້ມູນ';
    }
}

echo json_encode($response);
?>
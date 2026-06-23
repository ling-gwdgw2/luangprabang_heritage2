<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { 
    header('Location: login.php'); 
    exit; 
}
include_once '../config/database.php';
include_once 'check_permission.php';

if (!canEdit()) {
    header('Location: houses.php');
    exit;
}

$house_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = "SELECT * FROM heritage_houses WHERE house_id = $house_id";
$result = mysqli_query($connect, $query);
$house = mysqli_fetch_assoc($result);
if (!$house) { header('Location: houses.php'); exit; }

$imgQuery = "SELECT * FROM heritage_images WHERE house_id = $house_id ORDER BY display_order";
$imgResult = mysqli_query($connect, $imgQuery);
$images = [];
while ($img = mysqli_fetch_assoc($imgResult)) { $images[] = $img; }

// ດຶງປະເພດທັງໝົດ (safe: table may not exist on all envs)
$allCategories = [];
$catResult = mysqli_query($connect, "SELECT * FROM heritage_categories ORDER BY category_id");
if ($catResult) { while ($cat = mysqli_fetch_assoc($catResult)) { $allCategories[] = $cat; } }

// ດຶງປະເພດຂອງເຮືອນນີ້
$houseCatIds = [];
$hcResult = mysqli_query($connect, "SELECT category_id FROM house_categories WHERE house_id = $house_id");
if ($hcResult) { while ($hc = mysqli_fetch_assoc($hcResult)) { $houseCatIds[] = $hc['category_id']; } }


$message = ''; 
$message_type = '';
$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$upload_dir = '../uploads/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qr_code               = isset($_POST['qr_code'])                  ? mysqli_real_escape_string($connect, $_POST['qr_code'])                  : $house['qr_code'];
    $house_number          = isset($_POST['house_number'])              ? mysqli_real_escape_string($connect, $_POST['house_number'])              : '';
    $house_name_lo         = isset($_POST['house_name_lo'])             ? mysqli_real_escape_string($connect, $_POST['house_name_lo'])             : '';
    $house_name_en         = isset($_POST['house_name_en'])             ? mysqli_real_escape_string($connect, $_POST['house_name_en'])             : '';
    $owner_name_lo         = isset($_POST['owner_name_lo'])             ? mysqli_real_escape_string($connect, $_POST['owner_name_lo'])             : '';
    $owner_name_en         = isset($_POST['owner_name_en'])             ? mysqli_real_escape_string($connect, $_POST['owner_name_en'])             : '';
    $construction_year     = isset($_POST['construction_year']) && !empty($_POST['construction_year']) ? intval($_POST['construction_year']) : 'NULL';
    $architectural_style_lo= isset($_POST['architectural_style_lo'])    ? mysqli_real_escape_string($connect, $_POST['architectural_style_lo'])    : '';
    $architectural_style_en= isset($_POST['architectural_style_en'])    ? mysqli_real_escape_string($connect, $_POST['architectural_style_en'])    : '';
    $historical_significance_lo = isset($_POST['historical_significance_lo']) ? mysqli_real_escape_string($connect, $_POST['historical_significance_lo']) : '';
    $historical_significance_en = isset($_POST['historical_significance_en']) ? mysqli_real_escape_string($connect, $_POST['historical_significance_en']) : '';
    $description_lo        = isset($_POST['description_lo'])            ? mysqli_real_escape_string($connect, $_POST['description_lo'])            : '';
    $description_en        = isset($_POST['description_en'])            ? mysqli_real_escape_string($connect, $_POST['description_en'])            : '';
    $latitude              = isset($_POST['latitude'])  && $_POST['latitude']  !== '' ? floatval($_POST['latitude'])  : 'NULL';
    $longitude             = isset($_POST['longitude']) && $_POST['longitude'] !== '' ? floatval($_POST['longitude']) : 'NULL';
    $status                = isset($_POST['status'])                    ? mysqli_real_escape_string($connect, $_POST['status'])                   : 'active';
    $house_type            = isset($_POST['house_type'])                ? mysqli_real_escape_string($connect, $_POST['house_type'])                : '';
    $building_material     = isset($_POST['building_material'])         ? mysqli_real_escape_string($connect, $_POST['building_material'])         : '';

    // ຈັດການຮູບຫຼັກ
    $image_main = $house['image_main'];
    if (!empty($_POST['remove_image_main']) && $_POST['remove_image_main'] == '1') {
        if ($image_main && file_exists($upload_dir . $image_main)) unlink($upload_dir . $image_main);
        $image_main = '';
    } elseif (isset($_FILES['image_main']) && $_FILES['image_main']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image_main']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            if ($image_main && file_exists($upload_dir . $image_main)) unlink($upload_dir . $image_main);
            $new_filename = time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image_main']['tmp_name'], $upload_dir . $new_filename)) {
                $image_main = $new_filename;
                $img_data = base64_encode(file_get_contents($upload_dir . $new_filename));
                $img_mime = function_exists('mime_content_type') ? mime_content_type($upload_dir . $new_filename) : 'image/' . $ext;
                $stmt = mysqli_prepare($connect, "INSERT INTO image_store (filename, image_mime, image_data) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE image_data=VALUES(image_data)");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'sss', $new_filename, $img_mime, $img_data);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }
        }
    }

    // ລຶບຮູບເພີ່ມເຕີມ
    if (isset($_POST['delete_image_ids']) && is_array($_POST['delete_image_ids'])) {
        foreach ($_POST['delete_image_ids'] as $del_id) {
            $del_id = intval($del_id);
            $delRes = mysqli_query($connect, "SELECT image_path FROM heritage_images WHERE image_id=$del_id AND house_id=$house_id");
            $delRow = $delRes ? mysqli_fetch_assoc($delRes) : null;
            if ($delRow) {
                if ($delRow['image_path'] && file_exists($upload_dir . $delRow['image_path'])) unlink($upload_dir . $delRow['image_path']);
                mysqli_query($connect, "DELETE FROM heritage_images WHERE image_id=$del_id AND house_id=$house_id");
            }
        }
        // ໂຫຼດຮູບໃໝ່ຫຼັງລຶບ
        $imgResult2 = mysqli_query($connect, "SELECT * FROM heritage_images WHERE house_id = $house_id ORDER BY display_order");
        $images = [];
        if ($imgResult2) { while ($img2 = mysqli_fetch_assoc($imgResult2)) { $images[] = $img2; } }
    }

    $updateQuery = "UPDATE heritage_houses SET 
        qr_code='$qr_code', 
        house_number='$house_number', 
        house_name_lo='$house_name_lo', 
        house_name_en='$house_name_en', 
        owner_name_lo='$owner_name_lo', 
        owner_name_en='$owner_name_en', 
        construction_year=$construction_year, 
        architectural_style_lo='$architectural_style_lo', 
        architectural_style_en='$architectural_style_en', 
        historical_significance_lo='$historical_significance_lo', 
        historical_significance_en='$historical_significance_en', 
        description_lo='$description_lo', 
        description_en='$description_en', 
        latitude=$latitude, 
        longitude=$longitude, 
        image_main='$image_main',
        status='$status',
        house_type='$house_type',
        building_material='$building_material'
    WHERE house_id=$house_id";
    
    if (mysqli_query($connect, $updateQuery)) {
        // ອັບເດດ house_categories (safe: table may not exist)
        @mysqli_query($connect, "DELETE FROM house_categories WHERE house_id=$house_id");
        if (isset($_POST['categories']) && is_array($_POST['categories'])) {
            foreach ($_POST['categories'] as $cat_id) {
                $cat_id = intval($cat_id);
                @mysqli_query($connect, "INSERT IGNORE INTO house_categories (house_id, category_id) VALUES ($house_id, $cat_id)");
            }
        }

        // ອັບໂຫຼດຮູບເພີ່ມເຕີມ
        if (isset($_FILES['additional_images']) && !empty($_FILES['additional_images']['name'][0])) {
            $cntRes = mysqli_query($connect, "SELECT COUNT(*) as cnt FROM heritage_images WHERE house_id = $house_id");
            $cntRow = mysqli_fetch_assoc($cntRes);
            $slots = max(0, 3 - intval($cntRow['cnt']));
            if ($slots > 0) {
                $orderRes = mysqli_query($connect, "SELECT COALESCE(MAX(display_order), -1) + 1 as next_order FROM heritage_images WHERE house_id = $house_id");
                $orderRow = mysqli_fetch_assoc($orderRes);
                $order = intval($orderRow['next_order']);
                $total = min(count($_FILES['additional_images']['name']), $slots);
                for ($i = 0; $i < $total; $i++) {
                    if ($_FILES['additional_images']['error'][$i] === UPLOAD_ERR_OK) {
                        $ext = strtolower(pathinfo($_FILES['additional_images']['name'][$i], PATHINFO_EXTENSION));
                        if (in_array($ext, $allowed)) {
                            $new_filename = time() . '_' . uniqid() . '_' . $i . '.' . $ext;
                            if (move_uploaded_file($_FILES['additional_images']['tmp_name'][$i], $upload_dir . $new_filename)) {
                                $cap_lo = isset($_POST['image_caption_lo'][$i]) ? mysqli_real_escape_string($connect, $_POST['image_caption_lo'][$i]) : '';
                                $cap_en = isset($_POST['image_caption_en'][$i]) ? mysqli_real_escape_string($connect, $_POST['image_caption_en'][$i]) : '';
                                mysqli_query($connect, "INSERT INTO heritage_images (house_id, image_path, image_caption_lo, image_caption_en, display_order) VALUES ($house_id, '$new_filename', '$cap_lo', '$cap_en', $order)");
                                $order++;
                                $img_data = base64_encode(file_get_contents($upload_dir . $new_filename));
                                $img_mime = function_exists('mime_content_type') ? mime_content_type($upload_dir . $new_filename) : 'image/' . $ext;
                                $stmt2 = mysqli_prepare($connect, "INSERT INTO image_store (filename, image_mime, image_data) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE image_data=VALUES(image_data)");
                                if ($stmt2) {
                                    mysqli_stmt_bind_param($stmt2, 'sss', $new_filename, $img_mime, $img_data);
                                    mysqli_stmt_execute($stmt2);
                                    mysqli_stmt_close($stmt2);
                                }
                            }
                        }
                    }
                }
            }
        }

        // ອັບເດດ houseCatIds ຫຼັງ save
        $houseCatIds = [];
        $hcResult2 = mysqli_query($connect, "SELECT category_id FROM house_categories WHERE house_id=$house_id");
        if ($hcResult2) { while ($hc2 = mysqli_fetch_assoc($hcResult2)) { $houseCatIds[] = $hc2['category_id']; } }

        $message = 'ອັບເດດຂໍ້ມູນສຳເລັດ!';
        $message_type = 'success';
        // Refresh house data
        $houseRefresh = mysqli_query($connect, "SELECT * FROM heritage_houses WHERE house_id = $house_id");
        if ($houseRefresh) { $house = mysqli_fetch_assoc($houseRefresh); }
    } else {
        $message = 'ຜິດພາດ: ' . mysqli_error($connect);
        $message_type = 'danger';
    }
}

$slots = max(0, 3 - count($images));
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ແກ້ໄຂຂໍ້ມູນເຮືອນມໍລະດົກ</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { font-family: 'Noto Sans Lao', 'Phetsarath OT', sans-serif; }
        body { background: #f5f0e8; }
        .sidebar { background: #1a472a; min-height: 100vh; color: white; position: fixed; width: 260px; top: 0; left: 0; z-index: 100; overflow-y: auto; }
        .sidebar .brand { padding: 20px 16px; border-bottom: 1px solid rgba(255,255,255,0.15); text-align: center; }
        .sidebar .nav-link { color: rgba(255,255,255,0.85); padding: 12px 20px; border-radius: 10px; margin: 4px 10px; transition: all 0.2s; display: flex; align-items: center; gap: 12px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #2d6a4f; color: white; }
        .sidebar .nav-link i { width: 20px; text-align: center; }
        .main-content { margin-left: 260px; padding: 28px 32px; }
        .page-header { display: flex; align-items: center; gap: 14px; margin-bottom: 28px; }
        .page-header h2 { margin: 0; font-size: 1.5rem; color: #1a472a; font-weight: 700; }
        .card-custom { background: white; border-radius: 20px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.07); margin-bottom: 24px; }
        .card-custom .card-header-custom { padding: 18px 24px 0; }
        .card-custom .card-body { padding: 20px 24px 24px; }
        .section-title { font-size: 1rem; font-weight: 700; color: #1a472a; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; padding-bottom: 10px; border-bottom: 2px solid #e8f5e9; }
        .form-label { font-weight: 600; color: #2d4a3e; font-size: 0.875rem; margin-bottom: 6px; }
        .form-control, .form-select { border-radius: 10px; border: 1.5px solid #d1e8da; padding: 10px 14px; font-size: 0.9rem; transition: border-color 0.2s, box-shadow 0.2s; }
        .form-control:focus, .form-select:focus { border-color: #2d6a4f; box-shadow: 0 0 0 3px rgba(45,106,79,0.12); }
        .required-star { color: #e74c3c; margin-left: 3px; }

        /* Map Picker */
        #map-picker { height: 320px; border-radius: 14px; border: 2px solid #d1e8da; overflow: hidden; cursor: crosshair; }
        #map-picker.has-marker { border-color: #2d6a4f; }
        .map-coord-display { background: #f0f7f2; border-radius: 10px; padding: 12px 16px; margin-top: 12px; display: flex; gap: 16px; flex-wrap: wrap; }
        .coord-field { flex: 1; min-width: 160px; }
        .coord-hint { font-size: 0.78rem; color: #666; margin-top: 6px; }
        .coord-hint i { color: #2d6a4f; }
        .btn-clear-pin { background: none; border: 1px solid #dc3545; color: #dc3545; border-radius: 8px; padding: 4px 12px; font-size: 0.8rem; cursor: pointer; transition: all 0.2s; }
        .btn-clear-pin:hover { background: #dc3545; color: white; }

        /* Image area */
        .image-preview { width: 100px; height: 100px; object-fit: cover; border-radius: 10px; }
        .img-wrap { position: relative; display: inline-block; margin: 5px; }
        .img-remove-btn { position: absolute; top: -6px; right: -6px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 24px; height: 24px; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.3); transition: background 0.2s; }
        .img-remove-btn:hover { background: #a71d2a; }

        /* Category checkboxes */
        .cat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 10px; }
        .cat-item { background: #f0f7f2; border: 1.5px solid #d1e8da; border-radius: 10px; padding: 10px 14px; display: flex; align-items: center; gap: 10px; cursor: pointer; transition: all 0.2s; }
        .cat-item:hover { border-color: #2d6a4f; background: #e3f2e9; }
        .cat-item input[type=checkbox] { width: 18px; height: 18px; accent-color: #2d6a4f; cursor: pointer; }
        .cat-item input[type=checkbox]:checked + .cat-label-wrap { color: #1a472a; font-weight: 700; }
        .cat-item.selected { border-color: #2d6a4f; background: #e8f5e9; }
        .cat-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }

        /* Action buttons */
        .action-bar { background: white; border-radius: 20px; padding: 20px 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.07); display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 28px; flex-wrap: wrap; }
        .btn-save { background: #2d6a4f; color: white; border: none; border-radius: 50px; padding: 12px 36px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px; }
        .btn-save:hover { background: #1a472a; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(45,106,79,0.35); }
        .btn-cancel-link { background: #f1f3f5; color: #495057; border: none; border-radius: 50px; padding: 12px 28px; font-size: 1rem; font-weight: 600; text-decoration: none; transition: all 0.2s; display: flex; align-items: center; gap: 8px; }
        .btn-cancel-link:hover { background: #dee2e6; color: #343a40; }
        .status-toggle { display: flex; align-items: center; gap: 10px; }
        .toggle-switch { position: relative; width: 52px; height: 28px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: #ccc; border-radius: 28px; transition: 0.3s; }
        .toggle-slider:before { position: absolute; content: ''; height: 22px; width: 22px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: 0.3s; }
        input:checked + .toggle-slider { background: #2d6a4f; }
        input:checked + .toggle-slider:before { transform: translateX(24px); }

        @media (max-width: 768px) {
            .sidebar { width: 64px; }
            .sidebar .nav-link span { display: none; }
            .sidebar .brand h6 { display: none; }
            .main-content { margin-left: 64px; padding: 16px; }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="brand">
        <i class="fas fa-landmark fa-2x mb-2" style="color: #81c784;"></i>
        <h6 class="mb-0" style="font-size:0.9rem;">ມໍລະດົກຫຼວງພະບາງ</h6>
    </div>
    <nav class="nav flex-column mt-2">
        <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>ໜ້າຫຼັກ</span></a>
        <a class="nav-link active" href="houses.php"><i class="fas fa-home"></i><span>ຈັດການເຮືອນ</span></a>
        <a class="nav-link" href="add_house.php"><i class="fas fa-plus-circle"></i><span>ເພີ່ມຂໍ້ມູນ</span></a>
        <a class="nav-link" href="../map.php" target="_blank"><i class="fas fa-map-marked-alt"></i><span>ແຜນທີ່ມໍລະດົກ</span></a>
        <a class="nav-link" href="../api/logout.php"><i class="fas fa-sign-out-alt"></i><span>ອອກຈາກລະບົບ</span></a>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="page-header">
        <a href="houses.php" style="color:#2d6a4f; text-decoration:none;"><i class="fas fa-chevron-left"></i></a>
        <i class="fas fa-edit" style="color:#2d6a4f; font-size:1.4rem;"></i>
        <h2>ແກ້ໄຂຂໍ້ມູນ — <?php echo htmlspecialchars($house['house_name_lo'] ?: $house['house_number'] ?: "ເຮືອນ #$house_id"); ?></h2>
    </div>

    <form method="POST" enctype="multipart/form-data" id="editForm">

        <!-- ===== ຂໍ້ມູນທົ່ວໄປ + ຮູບຫຼັກ ===== -->
        <div class="row">
            <!-- ຂໍ້ມູນທົ່ວໄປ -->
            <div class="col-lg-7">
                <div class="card-custom">
                    <div class="card-body">
                        <div class="section-title"><i class="fas fa-info-circle"></i> ຂໍ້ມູນທົ່ວໄປ</div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">QR Code</label>
                                <input type="text" name="qr_code" class="form-control" value="<?php echo htmlspecialchars($house['qr_code']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ເລກທີ່ / House Number</label>
                                <input type="text" name="house_number" class="form-control" value="<?php echo htmlspecialchars($house['house_number']); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ຊື່ເຮືອນ (ລາວ)<span class="required-star">*</span></label>
                                <input type="text" name="house_name_lo" class="form-control" value="<?php echo htmlspecialchars($house['house_name_lo']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ຊື່ເຮືອນ (ອັງກິດ)</label>
                                <input type="text" name="house_name_en" class="form-control" value="<?php echo htmlspecialchars($house['house_name_en']); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ເຈົ້າຂອງ (ລາວ)</label>
                                <input type="text" name="owner_name_lo" class="form-control" value="<?php echo htmlspecialchars($house['owner_name_lo']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Owner Name (En)</label>
                                <input type="text" name="owner_name_en" class="form-control" value="<?php echo htmlspecialchars($house['owner_name_en']); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">ປີກໍ່ສ້າງ</label>
                                <input type="number" name="construction_year" class="form-control" value="<?php echo $house['construction_year']; ?>" min="1800" max="2100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">ສະຖາປັດຕະຍະກຳ (ລາວ)</label>
                                <input type="text" name="architectural_style_lo" class="form-control" value="<?php echo htmlspecialchars($house['architectural_style_lo']); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Architectural Style (En)</label>
                                <input type="text" name="architectural_style_en" class="form-control" value="<?php echo htmlspecialchars($house['architectural_style_en']); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ປະເພດເຮືອນ</label>
                                <select name="house_type" class="form-select">
                                    <option value="">-- ເລືອກປະເພດ --</option>
                                    <?php foreach ([
                                        'ຫຼັງຄາດ່ຽວ (Single-Pitch Roof)',
                                        'ຫຼັງຄາດ່ຽວມີເຊຍ (Single-Pitch Roof with Gable)',
                                        'ຫຼັງຄາດ່ຽວເຮືອນຄົວຂວາງ (Single-Pitch Roof with Detached Kitchen)',
                                        'ເຮືອນເປັນຫ້ອງແຖວ (Row House)',
                                        'ອາຄານຫ້ອງແຖວເປັນລະບົບ (Systematic Row House Building)',
                                        'ເຮືອນແບບປະສົມ (Mixed-Style House)',
                                    ] as $ht): ?>
                                        <option value="<?php echo $ht; ?>" <?php echo $house['house_type'] === $ht ? 'selected' : ''; ?>><?php echo $ht; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ວັດສະດຸກໍ່ສ້າງ</label>
                                <select name="building_material" class="form-select">
                                    <option value="">-- ເລືອກວັດສະດຸ --</option>
                                    <?php foreach ([
                                        'ໄມ້ (Bois)',
                                        'ໄມ້/ຕ໋ອກຊີ (Bois/Torchis)',
                                        'ໄມ້/ດິນຈີ່ກໍ່ປະທາຍປູນ (Bois/Brique Chaux)',
                                        'ດິນຈີ່ກໍ່ປະທາຍປູນ (Brique/Chaux)',
                                        'ດິນຈີ່ກໍ່ປະທາຍປູນ/ຕ໋ອກຊີ (Brique Chaux/Torchis)',
                                        'ໄມ້/ຕ໋ອກຊີ/ດິນຈີ່ກໍ່ປະທາຍປູນ (Bois/Torchis et Brique Chaux)',
                                    ] as $bm): ?>
                                        <option value="<?php echo $bm; ?>" <?php echo $house['building_material'] === $bm ? 'selected' : ''; ?>><?php echo $bm; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <!-- Status toggle -->
                        <div class="mb-2">
                            <label class="form-label d-block">ສະຖານະ</label>
                            <div class="status-toggle">
                                <label class="toggle-switch">
                                    <input type="checkbox" name="status_toggle" id="statusToggle" <?php echo $house['status'] === 'active' ? 'checked' : ''; ?> onchange="document.getElementById('statusHidden').value = this.checked ? 'active' : 'inactive'">
                                    <span class="toggle-slider"></span>
                                </label>
                                <input type="hidden" name="status" id="statusHidden" value="<?php echo htmlspecialchars($house['status']); ?>">
                                <span id="statusLabel" style="font-size:0.9rem; color:#2d6a4f; font-weight:600;"><?php echo $house['status'] === 'active' ? '✅ ເປີດໃຊ້ງານ' : '⛔ ປິດໃຊ້ງານ'; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ຮູບຫຼັກ -->
            <div class="col-lg-5">
                <div class="card-custom">
                    <div class="card-body">
                        <div class="section-title"><i class="fas fa-image"></i> ຮູບພາບຫຼັກ</div>
                        <?php if ($house['image_main'] && file_exists('../uploads/' . $house['image_main'])): ?>
                            <div class="mb-3 text-center">
                                <div class="img-wrap" id="currentImageWrap">
                                    <img src="../uploads/<?php echo htmlspecialchars($house['image_main']); ?>" class="image-preview" style="width:100%; max-width:260px; height:180px;" id="currentImage">
                                    <button type="button" class="img-remove-btn" onclick="removeMainImage()" title="ລຶບຮູບ">&times;</button>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center mb-3" style="background:#f0f7f2; border-radius:14px; padding:28px; color:#999;">
                                <i class="fas fa-image fa-3x mb-2" style="color:#c8e6c9;"></i>
                                <div style="font-size:0.85rem;">ຍັງບໍ່ມີຮູບຫຼັກ</div>
                            </div>
                        <?php endif; ?>
                        <input type="hidden" name="remove_image_main" id="remove_image_main" value="0">
                        <label class="form-label">ອັບໂຫຼດຮູບໃໝ່</label>
                        <input type="file" name="image_main" class="form-control" accept="image/*" id="main_image">
                        <div id="main_preview" class="mt-2 text-center"></div>
                        <small class="text-muted"><i class="fas fa-info-circle"></i> JPG, PNG, WebP — ສູງສຸດ 5MB</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== ປະເພດ (Categories) ===== -->
        <?php if (!empty($allCategories)): ?>
        <div class="card-custom">
            <div class="card-body">
                <div class="section-title"><i class="fas fa-tags"></i> ປະເພດເຮືອນມໍລະດົກ</div>
                <div class="cat-grid">
                    <?php
                    $catColors = [1=>'#b5835a',2=>'#dfb26a',3=>'#22577a',4=>'#38a3a5',5=>'#e07a5f'];
                    foreach ($allCategories as $cat):
                        $checked = in_array($cat['category_id'], $houseCatIds);
                        $color = $catColors[$cat['category_id']] ?? '#2d6a4f';
                    ?>
                    <label class="cat-item <?php echo $checked ? 'selected' : ''; ?>" id="catlabel_<?php echo $cat['category_id']; ?>">
                        <input type="checkbox" name="categories[]" value="<?php echo $cat['category_id']; ?>" <?php echo $checked ? 'checked' : ''; ?> onchange="toggleCatStyle(this)">
                        <span class="cat-dot" style="background:<?php echo $color; ?>;"></span>
                        <div class="cat-label-wrap" style="font-size:0.85rem;"><?php echo htmlspecialchars($cat['category_name_lo']); ?></div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ===== ແຜນທີ່ (Map Picker) ===== -->
        <div class="card-custom">
            <div class="card-body">
                <div class="section-title"><i class="fas fa-map-marker-alt"></i> ຕຳແໜ່ງ GPS ໃນແຜນທີ່</div>
                <p class="text-muted" style="font-size:0.85rem; margin-bottom:12px;"><i class="fas fa-hand-pointer" style="color:#2d6a4f;"></i> ຄລິກໃສ່ແຜນທີ່ເພື່ອເລືອກຕຳແໜ່ງ ຫຼື ພິມພິກັດດ້ານລຸ່ມ</p>
                <div id="map-picker"></div>
                <div class="map-coord-display">
                    <div class="coord-field">
                        <label class="form-label" style="font-size:0.82rem;">Latitude</label>
                        <input type="number" name="latitude" id="lat_input" class="form-control" step="0.0000001" value="<?php echo $house['latitude']; ?>" placeholder="19.8973" oninput="updatePinFromInputs()">
                    </div>
                    <div class="coord-field">
                        <label class="form-label" style="font-size:0.82rem;">Longitude</label>
                        <input type="number" name="longitude" id="lng_input" class="form-control" step="0.0000001" value="<?php echo $house['longitude']; ?>" placeholder="102.1432" oninput="updatePinFromInputs()">
                    </div>
                    <div style="display:flex; align-items:flex-end; padding-bottom:4px;">
                        <button type="button" class="btn-clear-pin" onclick="clearPin()"><i class="fas fa-times"></i> ລ້າງພິກັດ</button>
                    </div>
                </div>
                <div class="coord-hint"><i class="fas fa-lightbulb"></i> ຫຼວງພະບາງ: ≈ 19.8967°N, 102.1350°E</div>
            </div>
        </div>

        <!-- ===== ລາຍລະອຽດ ===== -->
        <div class="card-custom">
            <div class="card-body">
                <div class="section-title"><i class="fas fa-history"></i> ຂໍ້ມູນລາຍລະອຽດ</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ຂໍ້ມູນປະຫວັດ (ລາວ)</label>
                        <textarea name="historical_significance_lo" class="form-control" rows="4"><?php echo htmlspecialchars($house['historical_significance_lo']); ?></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Historical Significance (En)</label>
                        <textarea name="historical_significance_en" class="form-control" rows="4"><?php echo htmlspecialchars($house['historical_significance_en']); ?></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ລາຍລະອຽດ (ລາວ)</label>
                        <textarea name="description_lo" class="form-control" rows="4"><?php echo htmlspecialchars($house['description_lo']); ?></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Description (En)</label>
                        <textarea name="description_en" class="form-control" rows="4"><?php echo htmlspecialchars($house['description_en']); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== ຮູບເພີ່ມເຕີມ ===== -->
        <div class="card-custom">
            <div class="card-body">
                <div class="section-title"><i class="fas fa-images"></i> ຮູບພາບເພີ່ມເຕີມ <small class="text-muted fw-normal">(ສູງສຸດ 3 ຮູບ)</small></div>

                <?php if (!empty($images)): ?>
                <div class="mb-4">
                    <label class="form-label">ຮູບທີ່ມີຢູ່ — ຄລິກ ❌ ເພື່ອລຶບ</label>
                    <div class="d-flex flex-wrap gap-3">
                        <?php foreach ($images as $img): ?>
                        <div class="img-wrap" id="imgwrap_<?php echo $img['image_id']; ?>">
                            <img src="../uploads/<?php echo htmlspecialchars($img['image_path']); ?>" class="image-preview" onerror="this.src='https://placehold.co/100x100/e8f5e9/2d6a4f?text=IMG'">
                            <button type="button" class="img-remove-btn" onclick="markDeleteImage(<?php echo $img['image_id']; ?>)" title="ລຶບຮູບ">&times;</button>
                            <input type="hidden" name="delete_image_ids_pending[]" id="delpend_<?php echo $img['image_id']; ?>" value="" disabled>
                            <?php if ($img['image_caption_lo']): ?><div style="font-size:0.75rem; text-align:center; max-width:100px; color:#555; margin-top:4px;"><?php echo htmlspecialchars($img['image_caption_lo']); ?></div><?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Hidden array ສຳລັບ IDs ທີ່ຈະລຶບ -->
                <div id="delete-ids-container"></div>

                <?php if ($slots > 0): ?>
                <div>
                    <label class="form-label">ເພີ່ມຮູບໃໝ່ (ໄດ້ອີກ <?php echo $slots; ?> ຮູບ)</label>
                    <div id="add-img-rows">
                        <div class="row mb-2 add-img-row align-items-center">
                            <div class="col-md-5"><input type="file" name="additional_images[]" class="form-control" accept="image/*"></div>
                            <div class="col-md-3"><input type="text" name="image_caption_lo[]" class="form-control" placeholder="ຄຳອະທິບາຍ (ລາວ)"></div>
                            <div class="col-md-3"><input type="text" name="image_caption_en[]" class="form-control" placeholder="Caption (En)"></div>
                            <div class="col-md-1"><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></div>
                        </div>
                    </div>
                    <?php if ($slots > 1): ?>
                    <button type="button" class="btn btn-outline-success btn-sm mt-2" onclick="addRow()" id="addRowBtn">
                        <i class="fas fa-plus"></i> ເພີ່ມຮູບ
                    </button>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <p class="text-muted mb-0"><i class="fas fa-check-circle text-success"></i> ຮູບເພີ່ມເຕີມເຕັມແລ້ວ (3/3)</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- ===== Action Bar ===== -->
        <div class="action-bar">
            <div class="d-flex gap-3 align-items-center flex-wrap">
                <button type="button" class="btn-save" id="submitBtn">
                    <i class="fas fa-save"></i> ບັນທຶກການແກ້ໄຂ
                </button>
                <a href="houses.php" class="btn-cancel-link">
                    <i class="fas fa-times"></i> ຍົກເລີກ
                </a>
            </div>
            <div style="font-size:0.82rem; color:#888;">
                <i class="fas fa-clock"></i> ແກ້ໄຂຫຼ້າສຸດ: <?php echo htmlspecialchars($house['updated_at'] ?? $house['created_at'] ?? '—'); ?>
            </div>
        </div>

    </form>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ===== Map Picker =====
const luangPrabangCenter = [19.8967, 102.1350];
const initLat = <?php echo ($house['latitude'] && $house['latitude'] != 0) ? floatval($house['latitude']) : 'null'; ?>;
const initLng = <?php echo ($house['longitude'] && $house['longitude'] != 0) ? floatval($house['longitude']) : 'null'; ?>;

const pickerMap = L.map('map-picker', { zoomControl: true }).setView(
    (initLat && initLng) ? [initLat, initLng] : luangPrabangCenter, 16
);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 19
}).addTo(pickerMap);

let pickerMarker = null;

function setPin(lat, lng) {
    if (pickerMarker) { pickerMarker.setLatLng([lat, lng]); }
    else {
        pickerMarker = L.marker([lat, lng], { draggable: true }).addTo(pickerMap);
        pickerMarker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            document.getElementById('lat_input').value = pos.lat.toFixed(7);
            document.getElementById('lng_input').value = pos.lng.toFixed(7);
        });
    }
    document.getElementById('lat_input').value = lat.toFixed ? lat.toFixed(7) : lat;
    document.getElementById('lng_input').value = lng.toFixed ? lng.toFixed(7) : lng;
    document.getElementById('map-picker').classList.add('has-marker');
}

if (initLat && initLng) { setPin(initLat, initLng); }

pickerMap.on('click', function(e) {
    setPin(e.latlng.lat, e.latlng.lng);
    pickerMap.setView(e.latlng, pickerMap.getZoom());
});

function updatePinFromInputs() {
    const lat = parseFloat(document.getElementById('lat_input').value);
    const lng = parseFloat(document.getElementById('lng_input').value);
    if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
        setPin(lat, lng);
        pickerMap.setView([lat, lng], pickerMap.getZoom());
    }
}

function clearPin() {
    if (pickerMarker) { pickerMap.removeLayer(pickerMarker); pickerMarker = null; }
    document.getElementById('lat_input').value = '';
    document.getElementById('lng_input').value = '';
    document.getElementById('map-picker').classList.remove('has-marker');
}

// ===== Status Toggle Label =====
document.getElementById('statusToggle').addEventListener('change', function() {
    document.getElementById('statusLabel').textContent = this.checked ? '✅ ເປີດໃຊ້ງານ' : '⛔ ປິດໃຊ້ງານ';
    document.getElementById('statusHidden').value = this.checked ? 'active' : 'inactive';
});

// ===== Category style =====
function toggleCatStyle(cb) {
    const label = cb.closest('.cat-item');
    if (cb.checked) label.classList.add('selected');
    else label.classList.remove('selected');
}

// ===== Additional Images =====
var maxSlots = <?php echo $slots; ?>;
var rowCount = 1;
function addRow() {
    if (rowCount >= maxSlots) return;
    var row = '<div class="row mb-2 add-img-row align-items-center">' +
        '<div class="col-md-5"><input type="file" name="additional_images[]" class="form-control" accept="image/*"></div>' +
        '<div class="col-md-3"><input type="text" name="image_caption_lo[]" class="form-control" placeholder="ຄຳອະທິບາຍ (ລາວ)"></div>' +
        '<div class="col-md-3"><input type="text" name="image_caption_en[]" class="form-control" placeholder="Caption (En)"></div>' +
        '<div class="col-md-1"><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></div>' +
        '</div>';
    document.getElementById('add-img-rows').insertAdjacentHTML('beforeend', row);
    rowCount++;
    const btn = document.getElementById('addRowBtn');
    if (btn && rowCount >= maxSlots) btn.disabled = true;
}
function removeRow(btn) {
    var rows = document.querySelectorAll('#add-img-rows .add-img-row');
    if (rows.length <= 1) return;
    btn.closest('.add-img-row').remove();
    rowCount--;
    const addBtn = document.getElementById('addRowBtn');
    if (addBtn) addBtn.disabled = false;
}

// ===== ລຶບຮູບ Gallery =====
const pendingDeletes = new Set();
function markDeleteImage(imgId) {
    const wrap = document.getElementById('imgwrap_' + imgId);
    if (pendingDeletes.has(imgId)) {
        // Undo
        pendingDeletes.delete(imgId);
        wrap.style.opacity = '1';
        wrap.querySelector('.img-remove-btn').innerHTML = '&times;';
        wrap.querySelector('.img-remove-btn').style.background = '#dc3545';
        // Remove from delete container
        const existing = document.getElementById('del_' + imgId);
        if (existing) existing.remove();
    } else {
        pendingDeletes.add(imgId);
        wrap.style.opacity = '0.45';
        wrap.querySelector('.img-remove-btn').innerHTML = '<i class="fas fa-undo" style="font-size:10px;"></i>';
        wrap.querySelector('.img-remove-btn').style.background = '#6c757d';
        // Add hidden input
        const container = document.getElementById('delete-ids-container');
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'delete_image_ids[]';
        inp.id = 'del_' + imgId;
        inp.value = imgId;
        container.appendChild(inp);
    }
}

// ===== ຕົວຢ່າງຮູບຫຼັກ =====
document.getElementById('main_image').addEventListener('change', function(e) {
    const preview = document.getElementById('main_preview');
    preview.innerHTML = '';
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            preview.innerHTML = `<img src="${ev.target.result}" class="image-preview" style="width:160px;height:120px;">`;
        };
        reader.readAsDataURL(this.files[0]);
    }
});

function removeMainImage() {
    document.getElementById('currentImageWrap') && (document.getElementById('currentImageWrap').style.display = 'none');
    document.getElementById('remove_image_main').value = '1';
}

// ===== Submit with Confirm =====
document.getElementById('submitBtn').addEventListener('click', function() {
    const houseName = document.querySelector('input[name="house_name_lo"]').value.trim();
    const houseNameDisplay = houseName || 'ເຮືອນມໍລະດົກ';
    const deleteCount = pendingDeletes.size;
    const warningText = deleteCount > 0 ? `\n⚠️ ຈະລຶບຮູບ ${deleteCount} ຮູບ` : '';
    
    Swal.fire({
        title: 'ຢືນຢັນການແກ້ໄຂ',
        html: `ທ່ານຕ້ອງການອັບເດດຂໍ້ມູນ <b>"${houseNameDisplay}"</b> ແທ້ ຫຼື ບໍ່?${warningText ? '<br><span style="color:#e74c3c;font-size:0.9rem;">'+warningText+'</span>' : ''}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2d6a4f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-save"></i> ບັນທຶກ',
        cancelButtonText: 'ຍົກເລີກ'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'ກຳລັງອັບເດດ...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            document.getElementById('editForm').submit();
        }
    });
});

// ===== ຜົນການອັບເດດ =====
<?php if ($message_type == 'success'): ?>
Swal.fire({
    icon: 'success',
    title: 'ສຳເລັດ!',
    text: '<?php echo $message; ?>',
    confirmButtonColor: '#2d6a4f',
    confirmButtonText: 'ຕົກລົງ'
}).then(() => { window.location.href = 'houses.php'; });
<?php elseif ($message_type == 'danger' && $message): ?>
Swal.fire({
    icon: 'error',
    title: 'ຜິດພາດ',
    text: '<?php echo addslashes($message); ?>',
    confirmButtonText: 'ຕົກລົງ'
});
<?php endif; ?>
</script>
</body>
</html>
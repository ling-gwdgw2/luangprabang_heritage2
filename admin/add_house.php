<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { 
    header('Location: login.php'); 
    exit; 
}
include_once '../config/database.php';
include_once 'check_permission.php';

if (!canAdd()) {
    header('Location: houses.php');
    exit;
}

$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$upload_dir = '../uploads/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ເອົາຄ່າຂໍ້ມູນມາ escape ກ່ອນ
    $qr_code = mysqli_real_escape_string($connect, trim($_POST['qr_code'] ?? ''));
    $house_number = mysqli_real_escape_string($connect, trim($_POST['house_number'] ?? ''));
    $house_name_lo = mysqli_real_escape_string($connect, trim($_POST['house_name_lo'] ?? ''));
    $house_name_en = mysqli_real_escape_string($connect, trim($_POST['house_name_en'] ?? ''));
    $owner_name_lo = mysqli_real_escape_string($connect, trim($_POST['owner_name_lo'] ?? ''));
    $owner_name_en = mysqli_real_escape_string($connect, trim($_POST['owner_name_en'] ?? ''));
    $construction_year = !empty($_POST['construction_year']) ? intval($_POST['construction_year']) : 'NULL';
    $architectural_style_lo = mysqli_real_escape_string($connect, trim($_POST['architectural_style_lo'] ?? ''));
    $architectural_style_en = mysqli_real_escape_string($connect, trim($_POST['architectural_style_en'] ?? ''));
    $historical_significance_lo = mysqli_real_escape_string($connect, trim($_POST['historical_significance_lo'] ?? ''));
    $historical_significance_en = mysqli_real_escape_string($connect, trim($_POST['historical_significance_en'] ?? ''));
    $description_lo = mysqli_real_escape_string($connect, trim($_POST['description_lo'] ?? ''));
    $description_en = mysqli_real_escape_string($connect, trim($_POST['description_en'] ?? ''));
    $status = mysqli_real_escape_string($connect, $_POST['status'] ?? 'active');
    $house_type = mysqli_real_escape_string($connect, trim($_POST['house_type'] ?? ''));
    $building_material = mysqli_real_escape_string($connect, trim($_POST['building_material'] ?? ''));
    $latitude = isset($_POST['latitude']) && $_POST['latitude'] !== '' ? floatval($_POST['latitude']) : 'NULL';
    $longitude = isset($_POST['longitude']) && $_POST['longitude'] !== '' ? floatval($_POST['longitude']) : 'NULL';
    
    // ກວດສອບຂໍ້ມູນທີ່ຈຳເປັນ
    $errors = [];
    if (empty($qr_code)) $errors[] = 'QR Code ຫ້າມວ່າງເປົ່າ';
    if (empty($house_number)) $errors[] = 'ເລກທີ່ ຫ້າມວ່າງເປົ່າ';
    if (empty($house_name_lo)) $errors[] = 'ຊື່ເຮືອນ (ພາສາລາວ) ຫ້າມວ່າງເປົ່າ';
    
    // ກວດສອບ QR ຊ້ຳ
    $check = mysqli_query($connect, "SELECT house_id FROM heritage_houses WHERE qr_code = '$qr_code'");
    if (mysqli_num_rows($check) > 0) $errors[] = 'QR Code ນີ້ມີໃນລະບົບແລ້ວ';
    
    if (!empty($errors)) {
        $message = implode('<br>', $errors);
        $message_type = 'danger';
    } else {
        // ຮູບພາບຫຼັກ
        $image_main = '';
        if (isset($_FILES['image_main']) && $_FILES['image_main']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['image_main']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
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
        
        $sql = "INSERT INTO heritage_houses (
            qr_code, house_number, house_name_lo, house_name_en,
            owner_name_lo, owner_name_en, construction_year,
            architectural_style_lo, architectural_style_en,
            historical_significance_lo, historical_significance_en,
            description_lo, description_en, image_main, status, house_type, building_material,
            latitude, longitude
        ) VALUES (
            '$qr_code', '$house_number', '$house_name_lo', '$house_name_en',
            '$owner_name_lo', '$owner_name_en', $construction_year,
            '$architectural_style_lo', '$architectural_style_en',
            '$historical_significance_lo', '$historical_significance_en',
            '$description_lo', '$description_en', '$image_main', '$status', '$house_type', '$building_material',
            $latitude, $longitude
        )";
        
        if (mysqli_query($connect, $sql)) {
            $house_id = mysqli_insert_id($connect);
            
            // ຮູບພາບເພີ່ມເຕີມ
            if (isset($_FILES['additional_images']) && !empty($_FILES['additional_images']['name'][0])) {
                $total = count($_FILES['additional_images']['name']);
                for ($i = 0; $i < $total; $i++) {
                    if ($_FILES['additional_images']['error'][$i] === UPLOAD_ERR_OK) {
                        $ext = strtolower(pathinfo($_FILES['additional_images']['name'][$i], PATHINFO_EXTENSION));
                        if (in_array($ext, $allowed)) {
                            $new_filename = time() . '_' . uniqid() . '_' . $i . '.' . $ext;
                            if (move_uploaded_file($_FILES['additional_images']['tmp_name'][$i], $upload_dir . $new_filename)) {
                                $cap_lo = isset($_POST['image_caption_lo'][$i]) ? mysqli_real_escape_string($connect, $_POST['image_caption_lo'][$i]) : '';
                                $cap_en = isset($_POST['image_caption_en'][$i]) ? mysqli_real_escape_string($connect, $_POST['image_caption_en'][$i]) : '';
                                mysqli_query($connect, "INSERT INTO heritage_images (house_id, image_path, image_caption_lo, image_caption_en, display_order) VALUES ($house_id, '$new_filename', '$cap_lo', '$cap_en', $i)");
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
            
            $_SESSION['success_message'] = 'ບັນທຶກຂໍ້ມູນສຳເລັດແລ້ວ!';
            $_SESSION['success_type'] = 'success';
            echo "<script>window.location.href = 'houses.php';</script>";
            exit;
        } else {
            $message = 'ຜິດພາດ: ' . mysqli_error($connect);
            $message_type = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ເພີ່ມຂໍ້ມູນເຮືອນມໍລະດົກ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="brand">
        <i class="fas fa-landmark fa-2x"></i>
        <h6>ມໍລະດົກຫຼວງພະບາງ</h6>
    </div>
    <nav class="nav flex-column mt-2">
        <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>ໜ້າຫຼັກ</span></a>
        <a class="nav-link" href="houses.php"><i class="fas fa-home"></i> <span>ຈັດການເຮືອນ</span></a>
        <a class="nav-link active" href="add_house.php"><i class="fas fa-plus-circle"></i> <span>ເພີ່ມຂໍ້ມູນ</span></a>
        <a class="nav-link" href="../map.php" target="_blank"><i class="fas fa-map-marked-alt"></i> <span>ແຜນທີ່ມໍລະດົກ</span></a>
        <a class="nav-link" href="../api/logout.php"><i class="fas fa-sign-out-alt"></i> <span>ອອກຈາກລະບົບ</span></a>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="page-header">
        <div class="page-header-title-area">
            <h2><i class="fas fa-plus-circle"></i> ເພີ່ມຂໍ້ມູນເຮືອນມໍລະດົກ</h2>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
            <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" id="houseForm">
        <div class="row">
            <div class="col-md-6">
                <div class="card-custom">
                    <h5 class="mb-3"><i class="fas fa-info-circle text-success"></i> ຂໍ້ມູນທົ່ວໄປ</h5>
                    
                    <div class="mb-3">
                        <label class="required">ລະຫັດເຮືອນ</label>
                        <input type="text" name="qr_code" id="qr_code" class="form-control" required placeholder="ຕົວຢ່າງ: LP_H001">
                        <small class="text-muted">ຕົວຢ່າງ: LP_H001, LP_H002, ...</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="required">ເຮືອນເລກທີ່ / House Number</label>
                        <input type="text" name="house_number" id="house_number" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="required">ຊື່ເຮືອນ (ລາວ)</label>
                        <input type="text" name="house_name_lo" id="house_name_lo" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label>ຊື່ເຮືອນ (ອັງກິດ)</label>
                        <input type="text" name="house_name_en" class="form-control">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>ປີກໍ່ສ້າງ</label>
                            <input type="number" name="construction_year" class="form-control" min="1000" max="2025">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card-custom">
                    <h5 class="mb-3">
                        <i class="fas fa-map-marker-alt text-success"></i> ປະເພດ ແລະ ວັດສະດຸ</h5>
                    
                    <div class="mb-3">
                        <label>ປະເພດເຮືອນ</label>
                        <select name="house_type" class="form-control">
                            <option value="">-- ເລືອກປະເພດເຮືອນ --</option>
                            <option value="ຫຼັງຄາດ່ຽວ (Single-Pitch Roof)">ຫຼັງຄາດ່ຽວ (Single-Pitch Roof)</option>
                            <option value="ຫຼັງຄາດ່ຽວມີເຊຍ (Single-Pitch Roof with Gable)">ຫຼັງຄາດ່ຽວມີເຊຍ (Single-Pitch Roof with Gable)</option>
                            <option value="ຫຼັງຄາດ່ຽວເຮືອນຄົວຂວາງ (Single-Pitch Roof with Detached Kitchen)">ຫຼັງຄາດ່ຽວເຮືອນຄົວຂວາງ (Single-Pitch Roof with Detached Kitchen)</option>
                            <option value="ເຮືອນເປັນຫ້ອງແຖວ (Row House)">ເຮືອນເປັນຫ້ອງແຖວ (Row House)</option>
                            <option value="ອາຄານຫ້ອງແຖວເປັນລະບົບ (Systematic Row House Building)">ອາຄານຫ້ອງແຖວເປັນລະບົບ (Systematic Row House Building)</option>
                            <option value="ເຮືອນແບບປະສົມ (Mixed-Style House)">ເຮືອນແບບປະສົມ (Mixed-Style House)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label>ວັດສະດຸກໍ່ສ້າງ</label>
                        <select name="building_material" class="form-control">
                            <option value="">-- ເລືອກວັດສະດຸ --</option>
                            <option value="ໄມ້ (Bois)">ໄມ້ (Bois)</option>
                            <option value="ໄມ້/ຕ໋ອກຊີ (Bois/Torchis)">ໄມ້/ຕ໋ອກຊີ (Bois/Torchis)</option>
                            <option value="ໄມ້/ດິນຈີ່ກໍ່ປະທາຍປູນ (Bois/Brique Chaux)">ໄມ້/ດິນຈີ່ກໍ່ປະທາຍປູນ (Bois/Brique Chaux)</option>
                            <option value="ດິນຈີ່ກໍ່ປະທາຍປູນ (Brique/Chaux)">ດິນຈີ່ກໍ່ປະທາຍປູນ (Brique/Chaux)</option>
                            <option value="ດິນຈີ່ກໍ່ປະທາຍປູນ/ຕ໋ອກຊີ (Brique Chaux/Torchis)">ດິນຈີ່ກໍ່ປະທາຍປູນ/ຕ໋ອກຊີ (Brique Chaux/Torchis)</option>
                            <option value="ໄມ້/ຕ໋ອກຊີ/ດິນຈີ່ກໍ່ປະທາຍປູນ (Bois/Torchis et Brique Chaux)">ໄມ້/ຕ໋ອກຊີ/ດິນຈີ່ກໍ່ປະທາຍປູນ (Bois/Torchis et Brique Chaux)</option>
                        </select>
                    </div>
                    

                    <div class="mb-3">
                        <label>ຮູບພາບເຮືອນມໍລະດົກ</label>
                        <input type="file" name="image_main" class="form-control" accept="image/*" id="main_image">
                        <div id="main_preview" class="mt-2"></div>
                        <small class="text-muted">ຂະໜາດແນະນຳ: 800x600px (JPG, PNG, GIF)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== ແຜນທີ່ (Map Picker) ===== -->
        <div class="card-custom">
            <div class="card-body">
                <div class="section-title"><i class="fas fa-map-marker-alt"></i> ຕຳແໜ່ງ GPS ໃນແຜນທີ່</div>
                <p class="text-muted" style="font-size:0.85rem; margin-bottom:12px;"><i class="fas fa-hand-pointer" style="color:#2d6a4f;"></i> ຄລິກໃສ່ແຜນທີ່ເພື່ອເລືອກຕຳແໜ່ງ ຫຼື ພິມພິກັດດ້ານລຸ່ມ</p>
                <div id="map-picker"></div>
                <div class="map-coord-display">
                    <div class="coord-field">
                        <label class="form-label" style="font-size:0.82rem;">Latitude</label>
                        <input type="number" name="latitude" id="lat_input" class="form-control" step="0.0000001" value="" placeholder="19.8973" oninput="updatePinFromInputs()">
                    </div>
                    <div class="coord-field">
                        <label class="form-label" style="font-size:0.82rem;">Longitude</label>
                        <input type="number" name="longitude" id="lng_input" class="form-control" step="0.0000001" value="" placeholder="102.1432" oninput="updatePinFromInputs()">
                    </div>
                    <div style="display:flex; align-items:flex-end; padding-bottom:4px;">
                        <button type="button" class="btn-clear-pin" onclick="clearPin()"><i class="fas fa-times"></i> ລ້າງພິກັດ</button>
                    </div>
                </div>
                <div class="coord-hint"><i class="fas fa-lightbulb"></i> ຫຼວງພະບາງ: ≈ 19.8967°N, 102.1350°E</div>
            </div>
        </div>

        <div class="card-custom">
            <h5 class="mb-3"><i class="fas fa-history text-success"></i> ຂໍ້ມູນລາຍລະອຽດ</h5>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>ຂໍ້ມູນເຮືອນ (ລາວ)</label>
                    <textarea name="historical_significance_lo" class="form-control" rows="3" placeholder="ອະທິບາຍຄວາມສຳຄັນ..."></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label>ຂໍ້ມູນເຮືອນ (ອັງກິດ)</label>
                    <textarea name="historical_significance_en" class="form-control" rows="3" placeholder="Historical significance..."></textarea>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>ລາຍລະອຽດຂອງເຮືອນ (ລາວ)</label>
                    <textarea name="description_lo" class="form-control" rows="3" placeholder="ລາຍລະອຽດເພີ່ມເຕີມ..."></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label>ລາຍລະອຽດຂອງເຮືອນ (ອັງກິດ)</label>
                    <textarea name="description_en" class="form-control" rows="3" placeholder="Additional details..."></textarea>
                </div>
            </div>
        </div>

        <div class="card-custom">
            <h5 class="mb-3"><i class="fas fa-images text-success"></i> ຮູບພາບເພີ່ມເຕີມ</h5>
            <div id="additional_images_container">
                <div class="row mb-2 image-row">
                    <div class="col-md-5">
                        <input type="file" name="additional_images[]" class="form-control" accept="image/*">
                    </div>
                 
                    <div class="col-md-3">
                        <input type="text" name="image_caption_lo[]" class="form-control" placeholder="ຄຳອະທິບາຍ (ລາວ)">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="image_caption_en[]" class="form-control" placeholder="Caption (English)">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-row">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-outline-success btn-sm mt-2" onclick="addImageRow()">
                <i class="fas fa-plus"></i> ເພີ່ມຮູບພາບ
            </button>
        </div>

        <div class="action-bar">
            <div class="d-flex gap-3 align-items-center">
                <button type="button" class="btn-custom" id="submitBtn">
                    <i class="fas fa-save"></i> ບັນທຶກຂໍ້ມູນ
                </button>
                <a href="houses.php" class="btn-cancel-custom">
                    <i class="fas fa-times"></i> ຍົກເລີກ
                </a>
            </div>
        </div>
    </form>
</div>

<script>
function addImageRow() {
    const html = `
        <div class="row mb-2 image-row">
            <div class="col-md-5">
                <input type="file" name="additional_images[]" class="form-control" accept="image/*">
            </div>
            <div class="col-md-3">
                <input type="text" name="image_caption_lo[]" class="form-control" placeholder="ຄຳອະທິບາຍ (ລາວ)">
            </div>
            <div class="col-md-3">
                <input type="text" name="image_caption_en[]" class="form-control" placeholder="Caption (English)">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm remove-row">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    $('#additional_images_container').append(html);
}

$(document).on('click', '.remove-row', function() {
    $(this).closest('.image-row').remove();
});

document.getElementById('main_image').addEventListener('change', function(e) {
    const preview = document.getElementById('main_preview');
    preview.innerHTML = '';
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            preview.innerHTML = `<img src="${ev.target.result}" class="image-preview">`;
        };
        reader.readAsDataURL(this.files[0]);
    }
});

document.getElementById('submitBtn').addEventListener('click', function(e) {
    const qrCode = document.getElementById('qr_code').value.trim();
    const houseNumber = document.getElementById('house_number').value.trim();
    const houseNameLo = document.getElementById('house_name_lo').value.trim();
    
    if (!qrCode || !houseNumber || !houseNameLo) {
        Swal.fire({
            icon: 'warning',
            title: 'ຂໍ້ມູນບໍ່ຄົບຖ້ວນ',
            text: 'ກະລຸນາປ້ອນ QR Code, ເລກທີ່, ແລະ ຊື່ເຮືອນພາສາລາວ',
            confirmButtonText: 'ຕົກລົງ'
        });
        return;
    }
    
    Swal.fire({
        title: 'ຢືນຢັນການບັນທຶກ',
        text: `ທ່ານຕ້ອງການບັນທຶກຂໍ້ມູນດັ່ງກ່າວແທ້ ຫຼື ບໍ່?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2d6a4f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ບັນທຶກ',
        cancelButtonText: 'ຍົກເລີກ'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'ກຳລັງບັນທຶກ...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            document.getElementById('houseForm').submit();
        }
    });
});
// ===== Map Picker =====
const luangPrabangCenter = [19.8967, 102.1350];
const pickerMap = L.map('map-picker', { zoomControl: true }).setView(luangPrabangCenter, 16);
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
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
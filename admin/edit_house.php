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

$message = ''; 
$message_type = '';
$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$upload_dir = '../uploads/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qr_code = isset($_POST['qr_code']) ? mysqli_real_escape_string($connect, $_POST['qr_code']) : $house['qr_code'];
    $house_number = isset($_POST['house_number']) ? mysqli_real_escape_string($connect, $_POST['house_number']) : '';
    $house_name_lo = isset($_POST['house_name_lo']) ? mysqli_real_escape_string($connect, $_POST['house_name_lo']) : '';
    $house_name_en = isset($_POST['house_name_en']) ? mysqli_real_escape_string($connect, $_POST['house_name_en']) : '';
    $owner_name_lo = isset($_POST['owner_name_lo']) ? mysqli_real_escape_string($connect, $_POST['owner_name_lo']) : '';
    $owner_name_en = isset($_POST['owner_name_en']) ? mysqli_real_escape_string($connect, $_POST['owner_name_en']) : '';
    $construction_year = isset($_POST['construction_year']) && !empty($_POST['construction_year']) ? intval($_POST['construction_year']) : 'NULL';
    $architectural_style_lo = isset($_POST['architectural_style_lo']) ? mysqli_real_escape_string($connect, $_POST['architectural_style_lo']) : '';
    $architectural_style_en = isset($_POST['architectural_style_en']) ? mysqli_real_escape_string($connect, $_POST['architectural_style_en']) : '';
    $historical_significance_lo = isset($_POST['historical_significance_lo']) ? mysqli_real_escape_string($connect, $_POST['historical_significance_lo']) : '';
    $historical_significance_en = isset($_POST['historical_significance_en']) ? mysqli_real_escape_string($connect, $_POST['historical_significance_en']) : '';
    $description_lo = isset($_POST['description_lo']) ? mysqli_real_escape_string($connect, $_POST['description_lo']) : '';
    $description_en = isset($_POST['description_en']) ? mysqli_real_escape_string($connect, $_POST['description_en']) : '';
    $latitude = isset($_POST['latitude']) && $_POST['latitude'] !== '' ? floatval($_POST['latitude']) : 'NULL';
    $longitude = isset($_POST['longitude']) && $_POST['longitude'] !== '' ? floatval($_POST['longitude']) : 'NULL';
    $status = isset($_POST['status']) ? mysqli_real_escape_string($connect, $_POST['status']) : 'active';
    
    $image_main = $house['image_main'];
    if (isset($_FILES['image_main']) && $_FILES['image_main']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image_main']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            if ($image_main && file_exists($upload_dir . $image_main)) unlink($upload_dir . $image_main);
            $new_filename = time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image_main']['tmp_name'], $upload_dir . $new_filename)) $image_main = $new_filename;
        }
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
        status='$status' 
    WHERE house_id=$house_id";
    
    if (mysqli_query($connect, $updateQuery)) { 
        $message = 'ອັບເດດຂໍ້ມູນສຳເລັດ!'; 
        $message_type = 'success'; 
    } else { 
        $message = 'ຜິດພາດ: ' . mysqli_error($connect); 
        $message_type = 'danger'; 
    }
}
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ແກ້ໄຂຂໍ້ມູນເຮືອນມໍລະດົກ</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { font-family: 'Noto Sans Lao', 'Phetsarath OT', sans-serif; }
        body { background: #f5f0e8; }
        .sidebar { background: #1a472a; min-height: 100vh; color: white; position: fixed; width: 260px; }
        .sidebar .nav-link { color: rgba(255,255,255,0.85); padding: 12px 20px; border-radius: 10px; margin: 5px 10px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #2d6a4f; color: white; }
        .sidebar .nav-link i { margin-right: 12px; width: 25px; }
        .main-content { margin-left: 260px; padding: 20px; }
        .card-custom { background: white; border-radius: 20px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.08); }
        .btn-custom { background: #2d6a4f; border: none; border-radius: 50px; padding: 10px 25px; color: white; }
        .btn-custom:hover { background: #1a472a; }
        .btn-cancel { background: #6c757d; border: none; border-radius: 50px; padding: 10px 25px; color: white; }
        .btn-cancel:hover { background: #5a6268; }
        .form-label { font-weight: bold; color: #1a472a; }
        .image-preview { width: 100px; height: 100px; object-fit: cover; border-radius: 10px; margin: 5px; }
        @media (max-width: 768px) { .sidebar { width: 70px; } .sidebar .nav-link span:not(.nav-icon) { display: none; } .main-content { margin-left: 70px; } }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="p-3 text-center border-bottom border-success mb-3">
        <i class="fas fa-landmark fa-2x mb-2"></i>
        <h6 class="mb-0 d-none d-md-block">ມໍລະດົກຫຼວງພະບາງ</h6>
    </div>
    <nav class="nav flex-column">
        <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>ໜ້າຫຼັກ</span></a>
        <a class="nav-link active" href="houses.php"><i class="fas fa-home"></i> <span>ຈັດການເຮືອນ</span></a>
        <a class="nav-link" href="add_house.php"><i class="fas fa-plus-circle"></i> <span>ເພີ່ມຂໍ້ມູນ</span></a>
        <a class="nav-link" href="../api/logout.php"><i class="fas fa-sign-out-alt"></i> <span>ອອກຈາກລະບົບ</span></a>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <h2 class="mb-4"><i class="fas fa-edit text-success"></i> ແກ້ໄຂຂໍ້ມູນເຮືອນມໍລະດົກ</h2>
    
    <form method="POST" enctype="multipart/form-data" id="editForm">
        <div class="row">
            <div class="col-md-6">
                <div class="card-custom mb-4">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-info-circle text-success"></i> ຂໍ້ມູນທົ່ວໄປ</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">QR Code</label>
                            <input type="text" name="qr_code" class="form-control" value="<?php echo htmlspecialchars($house['qr_code']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">ເລກທີ່ / House Number</label>
                            <input type="text" name="house_number" class="form-control" value="<?php echo htmlspecialchars($house['house_number']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">ຊື່ເຮືອນ (ລາວ)</label>
                            <input type="text" name="house_name_lo" class="form-control" value="<?php echo htmlspecialchars($house['house_name_lo']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">ຊື່ເຮືອນ (ອັງກິດ)</label>
                            <input type="text" name="house_name_en" class="form-control" value="<?php echo htmlspecialchars($house['house_name_en']); ?>">
                        </div>
                        
                        
                        <div class="mb-3">
                            <label class="form-label">ປີກໍ່ສ້າງ</label>
                            <input type="number" name="construction_year" class="form-control" value="<?php echo $house['construction_year']; ?>">
                        </div>
                        
                        
                        
                        <!-- <div class="mb-3">
                            <label class="form-label">ສະຖານະ</label>
                            <select name="status" class="form-select">
                                <option value="active" <?php echo $house['status'] == 'active' ? 'selected' : ''; ?>>ເປີດໃຊ້ງານ</option>
                                <option value="inactive" <?php echo $house['status'] == 'inactive' ? 'selected' : ''; ?>>ປິດໃຊ້ງານ</option>
                            </select>
                        </div> -->
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card-custom mb-4">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-image text-success"></i> ຮູບພາບຫຼັກ</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">ຮູບພາບຫຼັກ</label>
                            <?php if ($house['image_main'] && file_exists('../uploads/' . $house['image_main'])): ?>
                                <div class="mb-2">
                                    <img src="../uploads/<?php echo $house['image_main']; ?>" class="image-preview" id="currentImage">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="image_main" class="form-control" accept="image/*" id="main_image">
                            <div id="main_preview" class="mt-2"></div>
                            <small class="text-muted">ອັບໂຫຼດຮູບໃໝ່ເພື່ອປ່ຽນແທນຮູບເກົ່າ</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card-custom mb-4">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-history text-success"></i> ຂໍ້ມູນລາຍລະອຽດ</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">ຄວາມສຳຄັນທາງປະຫວັດສາດ (ລາວ)</label>
                            <textarea name="historical_significance_lo" class="form-control" rows="3"><?php echo htmlspecialchars($house['historical_significance_lo']); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">ຄວາມສຳຄັນທາງປະຫວັດສາດ (ອັງກິດ)</label>
                            <textarea name="historical_significance_en" class="form-control" rows="3"><?php echo htmlspecialchars($house['historical_significance_en']); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">ລາຍລະອຽດ (ລາວ)</label>
                            <textarea name="description_lo" class="form-control" rows="3"><?php echo htmlspecialchars($house['description_lo']); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">ລາຍລະອຽດ (ອັງກິດ)</label>
                            <textarea name="description_en" class="form-control" rows="3"><?php echo htmlspecialchars($house['description_en']); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mb-4">
            <button type="button" class="btn-custom btn-lg px-5" id="submitBtn">
                <i class="fas fa-save"></i> ບັນທຶກການແກ້ໄຂ
            </button>
            <a href="houses.php" class="btn-cancel btn-lg px-5">
                <i class="fas fa-times"></i> ຍົກເລີກ
            </a>
        </div>
    </form>
</div>

<script>
// ສະແດງຕົວຢ່າງຮູບກ່ອນອັບ
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

// ຢືນຢັນກ່ອນອັບເດດ
document.getElementById('submitBtn').addEventListener('click', function(e) {
    const houseName = document.querySelector('input[name="house_name_lo"]').value.trim();
    const houseNameDisplay = houseName || 'ເຮືອນມໍລະດົກ';
    
    Swal.fire({
        title: 'ຢືນຢັນການແກ້ໄຂ',
        text: `ທ່ານຕ້ອງການອັບເດດຂໍ້ມູນ "${houseNameDisplay}" ແທ້ ຫຼື ບໍ່?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2d6a4f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ບັນທຶກ',
        cancelButtonText: 'ຍົກເລີກ'
    }).then((result) => {
        if (result.isConfirmed) {
            // ສະແດງ loading
            Swal.fire({
                title: 'ກຳລັງອັບເດດ...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            document.getElementById('editForm').submit();
        }
    });
});

// ສະແດງຜົນການອັບເດດ
<?php if ($message_type == 'success' && $message == 'ອັບເດດຂໍ້ມູນສຳເລັດ!'): ?>
Swal.fire({
    icon: 'success',
    title: 'ສຳເລັດ!',
    text: '<?php echo $message; ?>',
    confirmButtonText: 'ຕົກລົງ'
}).then(() => {
    window.location.href = 'houses.php';
});
<?php elseif ($message_type == 'danger' && $message): ?>
Swal.fire({
    icon: 'error',
    title: 'ຜິດພາດ',
    text: '<?php echo addslashes($message); ?>',
    confirmButtonText: 'ຕົກລົງ'
});
<?php endif; ?>
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
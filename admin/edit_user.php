<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { 
    header('Location: login.php'); 
    exit; 
}
include_once '../config/database.php';
include_once 'check_permission.php';

if (!canManageUsers()) {
    header('Location: dashboard.php');
    exit;
}

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = "SELECT * FROM users WHERE user_id = $user_id";
$result = mysqli_query($connect, $query);
$user = mysqli_fetch_assoc($result);

if (!$user) { 
    header('Location: users.php'); 
    exit; 
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname_lo = mysqli_real_escape_string($connect, trim($_POST['fullname_lo'] ?? ''));
    $fullname_en = mysqli_real_escape_string($connect, trim($_POST['fullname_en'] ?? ''));
    $email = mysqli_real_escape_string($connect, trim($_POST['email'] ?? ''));
    $role = mysqli_real_escape_string($connect, $_POST['role'] ?? $user['role']);
    $status = mysqli_real_escape_string($connect, $_POST['status'] ?? 'active');
    
    $errors = [];
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'ຮູບແບບ Email ບໍ່ຖືກຕ້ອງ';
    }
    
    $update_sql = "UPDATE users SET 
                   fullname_lo='$fullname_lo', 
                   fullname_en='$fullname_en', 
                   email='$email', 
                   role='$role', 
                   status='$status' 
                   WHERE user_id=$user_id";
    
    if (!empty($_POST['new_password'])) {
        $new_pwd = $_POST['new_password'];
        $confirm_pwd = $_POST['confirm_password'];
        if ($new_pwd !== $confirm_pwd) {
            $errors[] = 'ລະຫັດຜ່ານໃໝ່ບໍ່ກົງກັນ';
        } elseif (strlen($new_pwd) < 6) {
            $errors[] = 'ລະຫັດຜ່ານຕ້ອງມີຢ່າງນ້ອຍ 6 ຕົວອັກສອນ';
        } else {
            $hashed = password_hash($new_pwd, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET 
                           fullname_lo='$fullname_lo', 
                           fullname_en='$fullname_en', 
                           email='$email', 
                           role='$role', 
                           status='$status', 
                           password='$hashed' 
                           WHERE user_id=$user_id";
        }
    }
    
    if (empty($errors)) {
        if (mysqli_query($connect, $update_sql)) {
            $message = 'ອັບເດດຂໍ້ມູນສຳເລັດ!';
            $message_type = 'success';
            echo "<script>setTimeout(function(){ window.location.href = 'users.php'; }, 1500);</script>";
        } else {
            $message = 'ຜິດພາດ: ' . mysqli_error($connect);
            $message_type = 'danger';
        }
    } else {
        $message = implode('<br>', $errors);
        $message_type = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ແກ້ໄຂຜູ້ໃຊ້</title>
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
        .card-custom { background: white; border-radius: 20px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.08); padding: 30px; max-width: 800px; margin: auto; }
        .btn-custom { background: #2d6a4f; border: none; border-radius: 50px; padding: 10px 25px; color: white; }
        .btn-custom:hover { background: #1a472a; }
        @media (max-width: 768px) { .sidebar { width: 70px; } .sidebar .nav-link span:not(.nav-icon) { display: none; } .main-content { margin-left: 70px; } }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="p-3 text-center border-bottom border-success mb-3">
        <i class="fas fa-landmark fa-2x mb-2"></i>
        <h6 class="mb-0 d-none d-md-block">ມໍລະດົກຫຼວງພະບາງ</h6>
    </div>
    <nav class="nav flex-column">
        <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>ໜ້າຫຼັກ</span></a>
        <a class="nav-link" href="houses.php"><i class="fas fa-home"></i> <span>ຈັດການເຮືອນ</span></a>
        <a class="nav-link" href="add_house.php"><i class="fas fa-plus-circle"></i> <span>ເພີ່ມຂໍ້ມູນ</span></a>
        <a class="nav-link" href="users.php"><i class="fas fa-users"></i> <span>ຈັດການຜູ້ໃຊ້</span></a>
        <a class="nav-link" href="add_user.php"><i class="fas fa-user-plus"></i> <span>ເພີ່ມຜູ້ໃຊ້</span></a>
        <a class="nav-link" href="../map.php?from=admin" target="_blank"><i class="fas fa-map-marked-alt"></i> <span>ແຜນທີ່ມໍລະດົກ</span></a>
        <a class="nav-link" href="../api/logout.php"><i class="fas fa-sign-out-alt"></i> <span>ອອກຈາກລະບົບ</span></a>
    </nav>
</div>

<div class="main-content">
    <div class="card-custom">
        <h2 class="mb-4 text-center"><i class="fas fa-user-edit text-success"></i> ແກ້ໄຂຂໍ້ມູນຜູ້ໃຊ້</h2>
        <p class="text-center text-muted mb-4">ຊື່ຜູ້ໃຊ້: <strong><?php echo htmlspecialchars($user['username']); ?></strong></p>
        
        <?php if($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show"><?php echo $message; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label>ຊື່ ແລະ ນາມສະກຸນ (ລາວ)</label>
                    <input type="text" name="fullname_lo" class="form-control" value="<?php echo htmlspecialchars($user['fullname_lo']); ?>">
                </div>
                
                <div class="col-md-12 mb-3">
                    <label>ຊື່ ແລະ ນາມສະກຸນ (ອັງກິດ)</label>
                    <input type="text" name="fullname_en" class="form-control" value="<?php echo htmlspecialchars($user['fullname_en']); ?>">
                </div>
                
                <div class="col-md-12 mb-3">
                    <label>ອີເມວ (Email)</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label>ສິດທິ</label>
                    <select name="role" class="form-control">
                        <?php if($user['role'] == 'admin'): ?>
                            <option value="admin" selected disabled>Admin (ບໍ່ສາມາດປ່ຽນໄດ້)</option>
                        <?php endif; ?>
                        <option value="staff" <?php echo $user['role'] == 'staff' ? 'selected' : ''; ?>>ພະນັກງານ (Staff)</option>
                        <option value="viewer" <?php echo $user['role'] == 'viewer' ? 'selected' : ''; ?>>ຜູ້ເບິ່ງ (Viewer)</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label>ສະຖານະ</label>
                    <select name="status" class="form-control">
                        <option value="active" <?php echo $user['status'] == 'active' ? 'selected' : ''; ?>>ເປີດໃຊ້ງານ</option>
                        <option value="inactive" <?php echo $user['status'] == 'inactive' ? 'selected' : ''; ?>>ປິດໃຊ້ງານ</option>
                    </select>
                </div>
                
                <div class="col-md-12 mt-3">
                    <hr>
                    <h5><i class="fas fa-key text-warning"></i> ປ່ຽນລະຫັດຜ່ານ (ຖ້າຕ້ອງການ)</h5>
                    <small class="text-muted">ປ່ອຍວ່າງໄວ້ຖ້າບໍ່ຕ້ອງການປ່ຽນ</small>
                </div>
                
                <div class="col-md-6 mb-3 mt-2">
                    <label>ລະຫັດຜ່ານໃໝ່</label>
                    <input type="password" name="new_password" id="new_password" class="form-control">
                </div>
                
                <div class="col-md-6 mb-3 mt-2">
                    <label>ຢືນຢັນລະຫັດຜ່ານໃໝ່</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control">
                </div>
            </div>
            
            <div class="text-center mt-3">
                <button type="submit" class="btn-custom btn-lg px-5"><i class="fas fa-save"></i> ບັນທຶກ</button>
                <a href="users.php" class="btn btn-secondary btn-lg px-5 ms-2"><i class="fas fa-times"></i> ຍົກເລີກ</a>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    let newPwd = document.getElementById('new_password').value;
    let confirmPwd = document.getElementById('confirm_password').value;
    if(newPwd !== confirmPwd) {
        e.preventDefault();
        Swal.fire('ຜິດພາດ', 'ລະຫັດຜ່ານໃໝ່ບໍ່ກົງກັນ', 'error');
    } else if(newPwd.length > 0 && newPwd.length < 6) {
        e.preventDefault();
        Swal.fire('ຜິດພາດ', 'ລະຫັດຜ່ານຕ້ອງມີຢ່າງນ້ອຍ 6 ຕົວອັກສອນ', 'error');
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
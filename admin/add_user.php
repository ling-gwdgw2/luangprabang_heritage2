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

$message = '';
$message_type = '';

// ກວດສອບການເຊື່ອມຕໍ່ຖານຂໍ້ມູນ
if (!$connect) {
    die("ເຊື່ອມຕໍ່ຖານຂໍ້ມູນບໍ່ສຳເລັດ");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(mysqli_real_escape_string($connect, $_POST['username'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $fullname_lo = trim(mysqli_real_escape_string($connect, $_POST['fullname_lo'] ?? ''));
    $fullname_en = trim(mysqli_real_escape_string($connect, $_POST['fullname_en'] ?? ''));
    $email = trim(mysqli_real_escape_string($connect, $_POST['email'] ?? ''));
    $role = mysqli_real_escape_string($connect, $_POST['role'] ?? 'staff');
    $status = mysqli_real_escape_string($connect, $_POST['status'] ?? 'active');
    
    $errors = [];
    
    // ກວດສອບຂໍ້ມູນ
    if (empty($username)) {
        $errors[] = 'ຊື່ຜູ້ໃຊ້ຫ້າມວ່າງເປົ່າ';
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $errors[] = 'ຊື່ຜູ້ໃຊ້ຕ້ອງມີ 3-20 ຕົວອັກສອນ (a-z, A-Z, 0-9, _)';
    }
    
    if (empty($password)) {
        $errors[] = 'ລະຫັດຜ່ານຫ້າມວ່າງເປົ່າ';
    } elseif (strlen($password) < 6) {
        $errors[] = 'ລະຫັດຜ່ານຕ້ອງມີຢ່າງນ້ອຍ 6 ຕົວອັກສອນ';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'ລະຫັດຜ່ານບໍ່ກົງກັນ';
    }
    
    // ກວດສອບຊື່ຜູ້ໃຊ້ຊໍ້າ
    $check_query = "SELECT user_id FROM users WHERE username = '$username'";
    $check_result = mysqli_query($connect, $check_query);
    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $errors[] = 'ຊື່ຜູ້ໃຊ້ນີ້ມີແລ້ວ';
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'ຮູບແບບ Email ບໍ່ຖືກຕ້ອງ';
    }
    
    // ຖ້າບໍ່ມີຂໍ້ຜິດພາດ, ເພີ່ມຂໍ້ມູນ
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO users (username, password, fullname_lo, fullname_en, email, role, status, created_at) 
                  VALUES ('$username', '$hashed_password', '$fullname_lo', '$fullname_en', '$email', '$role', '$status', NOW())";
        
        if (mysqli_query($connect, $query)) {
            $message = 'ເພີ່ມຜູ້ໃຊ້ສຳເລັດ!';
            $message_type = 'success';
            // ຣີເລັດຟອມ ຫຼື ກັບໄປໜ້າ users.php
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'users.php';
                }, 2000);
            </script>";
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
    <title>ເພີ່ມຜູ້ໃຊ້ໃໝ່</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { font-family: 'Noto Sans Lao', 'Phetsarath OT', sans-serif; }
        body { background: #f5f0e8; }
        .sidebar { background: #1a472a; min-height: 100vh; color: white; position: fixed; width: 260px; }
        .sidebar .nav-link { color: rgba(255,255,255,0.85); padding: 12px 20px; border-radius: 10px; margin: 5px 10px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #2d6a4f; color: white; }
        .sidebar .nav-link i { margin-right: 12px; width: 25px; }
        .main-content { margin-left: 260px; padding: 20px; }
        .card-custom { background: white; border-radius: 20px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.08); padding: 30px; max-width: 700px; margin: auto; }
        .btn-custom { background: #2d6a4f; border: none; border-radius: 50px; padding: 10px 25px; color: white; }
        .btn-custom:hover { background: #1a472a; }
        .required:after { content: " *"; color: red; }
        @media (max-width: 768px) { 
            .sidebar { width: 70px; } 
            .sidebar .nav-link span { display: none; } 
            .main-content { margin-left: 70px; } 
        }
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
        <a class="nav-link active" href="add_user.php"><i class="fas fa-user-plus"></i> <span>ເພີ່ມຜູ້ໃຊ້</span></a>
        <a class="nav-link" href="../api/logout.php"><i class="fas fa-sign-out-alt"></i> <span>ອອກຈາກລະບົບ</span></a>
    </nav>
</div>

<div class="main-content">
    <div class="card-custom">
        <h2 class="mb-4 text-center"><i class="fas fa-user-plus text-success"></i> ເພີ່ມຜູ້ໃຊ້ໃໝ່</h2>
        
        <?php if($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <form method="POST" onsubmit="return validateForm()">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="required">ຊື່ຜູ້ໃຊ້ (Username)</label>
                    <input type="text" name="username" id="username" class="form-control form-control-lg" required 
                           pattern="[a-zA-Z0-9_]{3,20}" 
                           title="ຊື່ຜູ້ໃຊ້ຕ້ອງມີ 3-20 ຕົວອັກສອນ (a-z, A-Z, 0-9, _)">
                    <small class="text-muted">ອັກສອນພາສາອັງກິດ ຫຼື ຕົວເລກເທົ່ານັ້ນ (3-20 ຕົວອັກສອນ)</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="required">ລະຫັດຜ່ານ</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                    <small class="text-muted">ຢ່າງນ້ອຍ 6 ຕົວອັກສອນ</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="required">ຢືນຢັນລະຫັດຜ່ານ</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label>ຊື່ ແລະ ນາມສະກຸນ (ລາວ)</label>
                    <input type="text" name="fullname_lo" class="form-control">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label>ຊື່ ແລະ ນາມສະກຸນ (ອັງກິດ)</label>
                    <input type="text" name="fullname_en" class="form-control">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label>ອີເມວ (Email)</label>
                    <input type="email" name="email" class="form-control">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label>ສິດທິ</label>
                    <select name="role" class="form-control">
                        <option value="staff">ພະນັກງານ (Staff)</option>
                        <option value="viewer">ຜູ້ເບິ່ງ (Viewer)</option>
                    </select>
                    <small class="text-muted">Admin ສາມາດສ້າງໄດ້ຈາກຖານຂໍ້ມູນເທົ່ານັ້ນ</small>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label>ສະຖານະ</label>
                    <select name="status" class="form-control">
                        <option value="active">ເປີດໃຊ້ງານ</option>
                        <option value="inactive">ປິດໃຊ້ງານ</option>
                    </select>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <button type="submit" class="btn-custom btn-lg px-5">
                    <i class="fas fa-save"></i> ບັນທຶກ
                </button>
                <a href="users.php" class="btn btn-secondary btn-lg px-5 ms-2">
                    <i class="fas fa-times"></i> ຍົກເລີກ
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function validateForm() {
    let username = document.getElementById('username').value.trim();
    let pwd = document.getElementById('password').value;
    let cpwd = document.getElementById('confirm_password').value;
    
    if(username.length < 3) {
        Swal.fire('ຜິດພາດ', 'ຊື່ຜູ້ໃຊ້ຕ້ອງມີຢ່າງນ້ອຍ 3 ຕົວອັກສອນ', 'error');
        return false;
    }
    
    if(pwd !== cpwd) {
        Swal.fire('ຜິດພາດ', 'ລະຫັດຜ່ານບໍ່ກົງກັນ', 'error');
        return false;
    }
    
    if(pwd.length < 6) {
        Swal.fire('ຜິດພາດ', 'ລະຫັດຜ່ານຕ້ອງມີຢ່າງນ້ອຍ 6 ຕົວອັກສອນ', 'error');
        return false;
    }
    
    return true;
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
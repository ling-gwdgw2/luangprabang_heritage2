<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { 
    header('Location: login.php'); 
    exit; 
}
include_once '../config/database.php';
include_once 'check_permission.php';

// ສະເພາະ Admin ເທົ່ານັ້ນທີ່ເຂົ້າໄດ້
if (!canManageUsers()) {
    header('Location: dashboard.php');
    exit;
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $check = mysqli_query($connect, "SELECT role FROM users WHERE user_id = $delete_id");
    $user = mysqli_fetch_assoc($check);
    
    if ($user && $user['role'] != 'admin') {
        mysqli_query($connect, "DELETE FROM users WHERE user_id = $delete_id");
        $message = "ລຶບຜູ້ໃຊ້ສຳເລັດ!";
        $message_type = "success";
    } else {
        $message = "ບໍ່ສາມາດລຶບຜູ້ໃຊ້ admin ຫຼັກໄດ້";
        $message_type = "danger";
    }
}

$query = "SELECT user_id, username, fullname_lo, fullname_en, email, role, status, created_at FROM users ORDER BY user_id ASC";
$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ຈັດການຜູ້ໃຊ້ງານ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="sidebar">
    <div class="brand">
        <i class="fas fa-landmark fa-2x"></i>
        <h6>ມໍລະດົກຫຼວງພະບາງ</h6>
    </div>
    <nav class="nav flex-column mt-2">
        <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>ໜ້າຫຼັກ</span></a>
        <a class="nav-link" href="houses.php"><i class="fas fa-home"></i> <span>ຈັດການເຮືອນ</span></a>
        <a class="nav-link" href="add_house.php"><i class="fas fa-plus-circle"></i> <span>ເພີ່ມຂໍ້ມູນ</span></a>
        <a class="nav-link active" href="users.php"><i class="fas fa-users"></i> <span>ຈັດການຜູ້ໃຊ້</span></a>
        <a class="nav-link" href="add_user.php"><i class="fas fa-user-plus"></i> <span>ເພີ່ມຜູ້ໃຊ້</span></a>
        <a class="nav-link" href="report_views.php"> <i class="fas fa-chart-bar"></i><span>ລາຍງານການເຂົ້າຊົມ</span></a>
        <a class="nav-link" href="../map.php" target="_blank"><i class="fas fa-map-marked-alt"></i> <span>ແຜນທີ່ມໍລະດົກ</span></a>
        <a class="nav-link" href="../api/logout.php"><i class="fas fa-sign-out-alt"></i> <span>ອອກຈາກລະບົບ</span></a>
    </nav>
</div>

<div class="main-content">
    <div class="page-header">
        <div class="page-header-title-area">
            <h2><i class="fas fa-users"></i> ຈັດການຜູ້ໃຊ້ງານ</h2>
        </div>
        <a href="add_user.php" class="btn-custom"><i class="fas fa-user-plus"></i> ເພີ່ມຜູ້ໃຊ້ໃໝ່</a>
    </div>
    
    <?php if($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show"><?php echo $message; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    
    <div class="card-custom">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 12%">ຊື່ຜູ້ໃຊ້</th>
                            <th style="width: 15%">ຊື່ (ລາວ)</th>
                            <th style="width: 15%">ຊື່ (ອັງກິດ)</th>
                            <th style="width: 18%">ອີເມວ</th>
                            <th style="width: 10%">ສິດທິ</th>
                            <th style="width: 10%">ສະຖານະ</th>
                            <th style="width: 15%">ການຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1; while($row = mysqli_fetch_assoc($result)): 
                            $status_class = $row['status'] == 'active' ? 'success' : 'secondary';
                            $status_text = $row['status'] == 'active' ? 'ເປີດໃຊ້' : 'ປິດໃຊ້';
                            if($row['role'] == 'admin'): $role_class = 'danger'; $role_text = 'ແອັດມິນ';
                            elseif($row['role'] == 'staff'): $role_class = 'warning'; $role_text = 'ພະນັກງານ';
                            else: $role_class = 'secondary'; $role_text = 'ຜູ້ເບິ່ງ'; endif;
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo $count++; ?></td>
                            <td><code><?php echo htmlspecialchars($row['username']); ?></code></td>
                            <td><?php echo htmlspecialchars($row['fullname_lo'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($row['fullname_en'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($row['email'] ?: '-'); ?></td>
                            <td><span class="badge bg-<?php echo $role_class; ?>"><?php echo $role_text; ?></span></td>
                            <td>
                                <span class="status-badge bg-<?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-outline-warning me-1"><i class="fas fa-edit"></i></a>
                                <?php if($row['role'] != 'admin'): ?>
                                    <button onclick="deleteUser(<?php echo $row['user_id']; ?>)" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if(mysqli_num_rows($result) == 0): ?>
                            <tr><td colspan="8" class="text-center py-4 text-muted">ຍັງບໍ່ມີຂໍ້ມູນຜູ້ໃຊ້</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function deleteUser(userId) {
    Swal.fire({ title: 'ຢືນຢັນການລຶບ', text: "ທ່ານຕ້ອງການລຶບຜູ້ໃຊ້ນີ້ແທ້ ຫຼື ບໍ່?", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'ລຶບ', cancelButtonText: 'ຍົກເລີກ' })
    .then((result) => { 
        if (result.isConfirmed) { 
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'users.php';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_id';
            input.value = userId;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        } 
    });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
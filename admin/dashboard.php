<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { 
    header('Location: login.php'); 
    exit; 
}
include_once '../config/database.php';
include_once 'check_permission.php';

$totalHouses = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as count FROM heritage_houses"))['count'];
$totalVisits = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as count FROM visit_logs"))['count'];
$todayVisits = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as count FROM visit_logs WHERE visit_date = CURDATE()"))['count'];
$activeHouses = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as count FROM heritage_houses WHERE status = 'active'"))['count'];
$recentHouses = mysqli_query($connect, "SELECT * FROM heritage_houses ORDER BY created_at DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | ຄຸ້ມຄອງມໍລະດົກ</title>
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
        <a class="nav-link active" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>ໜ້າຫຼັກ</span></a>
        <a class="nav-link" href="houses.php"><i class="fas fa-home"></i> <span>ຈັດການເຮືອນ</span></a>
        <a class="nav-link" href="add_house.php"><i class="fas fa-plus-circle"></i> <span>ເພີ່ມຂໍ້ມູນ</span></a>
        <a class="nav-link" href="users.php"><i class="fas fa-users"></i> <span>ຈັດການຜູ້ໃຊ້</span></a>
        <a class="nav-link" href="add_user.php"><i class="fas fa-user-plus"></i> <span>ເພີ່ມຜູ້ໃຊ້</span></a>
        <a class="nav-link" href="report_views.php"> <i class="fas fa-chart-bar"></i><span>ລາຍງານການເຂົ້າຊົມ</span></a>
        <a class="nav-link" href="../api/logout.php"><i class="fas fa-sign-out-alt"></i> <span>ອອກຈາກລະບົບ</span></a>
    </nav>
</div>
<div class="main-content">
    <div class="page-header">
        <div class="page-header-title-area">
            <h2><i class="fas fa-tachometer-alt"></i> ໜ້າຫຼັກ</h2>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="user-badge"><i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            <?php if(canManageUsers()): ?>
            <a href="add_user.php" class="btn-custom"><i class="fas fa-user-plus"></i> ເພີ່ມຜູ້ໃຊ້</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div>
                    <div class="stat-number"><?php echo $totalHouses; ?></div>
                    <div class="stat-title">ເຮືອນມໍລະດົກທັງໝົດ</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-home"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div>
                    <div class="stat-number"><?php echo $activeHouses; ?></div>
                    <div class="stat-title">ເຮືອນທີ່ເປີດໃຫ້ຊົມ</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div>
                    <div class="stat-number"><?php echo number_format($totalVisits); ?></div>
                    <div class="stat-title">ຜູ້ເຂົ້າຊົມທັງໝົດ</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div>
                    <div class="stat-number"><?php echo number_format($todayVisits); ?></div>
                    <div class="stat-title">ຜູ້ເຂົ້າຊົມມື້ນີ້</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="card-custom">
        <div class="card-header-custom">
            <h5><i class="fas fa-list"></i> ເຮືອນມໍລະດົກລ່າສຸດ</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>QR Code</th><th>ຊື່ເຮືອນ (ລາວ)</th><th>ຊື່ເຮືອນ (ອັງກິດ)</th><th style="width: 20%">ການຈັດການ</th></tr>
                    </thead>
                    <tbody>
                    <?php while ($house = mysqli_fetch_assoc($recentHouses)) { ?>
                        <tr>
                            <td><code><?php echo htmlspecialchars($house['qr_code']); ?></code></td>
                            <td><?php echo htmlspecialchars($house['house_name_lo'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($house['house_name_en'] ?: '-'); ?></td>
                            <td>
                                <a href="edit_house.php?id=<?php echo $house['house_id']; ?>" class="btn btn-sm btn-outline-warning me-1" title="ແກ້ໄຂ"><i class="fas fa-edit"></i></a>
                                <button onclick="deleteHouse(<?php echo $house['house_id']; ?>)" class="btn btn-sm btn-outline-danger me-1" title="ລຶບ"><i class="fas fa-trash"></i></button>
                                <button onclick="generateQR(<?php echo $house['house_id']; ?>)" class="btn btn-sm btn-outline-info" title="ສ້າງ QR Code"><i class="fas fa-qrcode"></i></button>
                            </td>
                        </tr>
                    <?php } if (mysqli_num_rows($recentHouses) == 0) echo '<tr><td colspan="5" class="text-center py-4 text-muted">ຍັງບໍ່ມີຂໍ້ມູນ</td></tr>'; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
function deleteHouse(houseId) { 
    Swal.fire({ 
        title: 'ຢືນຢັນການລຶບ', 
        text: 'ທ່ານຕ້ອງການລຶບເຮືອນມໍລະດົກນີ້ແທ້ ຫຼື ບໍ່?', 
        icon: 'warning', 
        showCancelButton: true, 
        confirmButtonColor: '#d33', 
        cancelButtonColor: '#3085d6', 
        confirmButtonText: 'ລຶບ', 
        cancelButtonText: 'ຍົກເລີກ' 
    }).then((result) => { 
        if (result.isConfirmed) { 
            Swal.fire({ title: 'ກຳລັງລຶບ...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
            $.ajax({ 
                url: 'simple_delete_ajax.php',
                method: 'POST', 
                data: { delete_id: houseId }, 
                dataType: 'json', 
                success: function(response) { 
                    Swal.close();
                    if (response.success) { 
                        Swal.fire('ສຳເລັດ!', response.message, 'success').then(() => location.reload()); 
                    } else { 
                        Swal.fire('ຜິດພາດ!', response.message, 'error'); 
                    } 
                }, 
                error: function(xhr) { 
                    Swal.close();
                    Swal.fire('ຜິດພາດ!', 'ບໍ່ສາມາດລຶບຂໍ້ມູນໄດ້: ' + xhr.status, 'error'); 
                } 
            }); 
        } 
    }); 
}
function generateQR(houseId) { 
    Swal.fire({ title: 'ກຳລັງສ້າງ QR Code...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } }); 
    $.ajax({ 
        url: 'generate_qr.php', 
        method: 'POST', 
        data: { house_id: houseId }, 
        dataType: 'json', 
        success: function(response) { 
            Swal.close(); 
            if (response.success) { 
                Swal.fire({ 
                    title: 'ສ້າງ QR Code ສຳເລັດ', 
                    html: `<img src="../${response.qr_url}" style="width: 200px; height: 200px;"><br><code>${response.qr_id}</code><br><a href="../${response.qr_url}" download class="btn btn-success mt-2">ດາວໂຫຼດ QR Code</a>`, 
                    showConfirmButton: true 
                }).then(() => location.reload()); 
            } else { 
                Swal.fire('ຜິດພາດ!', response.message, 'error'); 
            } 
        }, 
        error: function() { 
            Swal.close(); 
            Swal.fire('ຜິດພາດ!', 'ບໍ່ສາມາດສ້າງ QR Code ໄດ້', 'error'); 
        } 
    }); 
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { 
    header('Location: login.php'); 
    exit; 
}
include_once '../config/database.php';

// ========== ຮັບຂໍ້ຄວາມສຳເລັດຈາກ add_house.php ==========
$success_message = $_SESSION['success_message'] ?? '';
$success_type = $_SESSION['success_type'] ?? '';
unset($_SESSION['success_message']);
unset($_SESSION['success_type']);
// ================================================================

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? mysqli_real_escape_string($connect, $_GET['search']) : '';

if ($search) {
    $countQuery = "SELECT COUNT(*) as count FROM heritage_houses WHERE house_name_lo LIKE '%$search%' OR house_name_en LIKE '%$search%' OR qr_code LIKE '%$search%'";
    $query = "SELECT * FROM heritage_houses WHERE house_name_lo LIKE '%$search%' OR house_name_en LIKE '%$search%' OR qr_code LIKE '%$search%' ORDER BY created_at DESC LIMIT $offset, $limit";
} else {
    $countQuery = "SELECT COUNT(*) as count FROM heritage_houses";
    $query = "SELECT * FROM heritage_houses ORDER BY created_at DESC LIMIT $offset, $limit";
}

$totalResult = mysqli_query($connect, $countQuery);
$total = mysqli_fetch_assoc($totalResult)['count'];
$totalPages = ceil($total / $limit);
$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ຈັດການເຮືອນມໍລະດົກ | Heritage Houses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        <a class="nav-link active" href="houses.php"><i class="fas fa-home"></i> <span>ຈັດການເຮືອນ</span></a>
        <a class="nav-link" href="add_house.php"><i class="fas fa-plus-circle"></i> <span>ເພີ່ມຂໍ້ມູນ</span></a>
        <a class="nav-link" href="../map.php?from=admin"><i class="fas fa-map-marked-alt"></i> <span>ແຜນທີ່ມໍລະດົກ</span></a>
        <a class="nav-link" href="../api/logout.php"><i class="fas fa-sign-out-alt"></i> <span>ອອກຈາກລະບົບ</span></a>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="page-header">
        <div class="page-header-title-area">
            <h2><i class="fas fa-home"></i> ຈັດการເຮືອນມໍລະດົກ</h2>
        </div>
        <a href="add_house.php" class="btn-custom"><i class="fas fa-plus"></i> ເພີ່ມຂໍ້ມູນໃໝ່</a>
    </div>

    <!-- Search Form -->
    <div class="card-custom mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-md-9">
                    <input type="text" name="search" class="form-control form-control-lg" placeholder="ຄົ້ນຫາຕາມຊື່ ຫຼື QR Code..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn-custom w-100 py-3"><i class="fas fa-search"></i> ຄົ້ນຫາ</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Houses Table -->
    <div class="card-custom">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 15%">QR Code</th>
                            <th style="width: 25%">ຊື່ເຮືອນ (ລາວ)</th>
                            <th style="width: 25%">ຊື່ເຮືອນ (ອັງກິດ)</th>
                            <th style="width: 10%">ສະຖານະ</th>
                            <th style="width: 20%">ການຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $count = $offset + 1; 
                        while ($house = mysqli_fetch_assoc($result)) { 
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo $count++; ?></td>
                            <td><code><?php echo htmlspecialchars($house['qr_code']); ?></code></td>
                            <td><?php echo htmlspecialchars($house['house_name_lo'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($house['house_name_en'] ?: '-'); ?></td>
                            <td>
                                <span class="status-badge bg-<?php echo $house['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                    <?php echo $house['status'] == 'active' ? 'ເປີດໃຊ້' : 'ປິດໃຊ້'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_house.php?id=<?php echo $house['house_id']; ?>" class="btn btn-sm btn-outline-warning me-1" title="ແກ້ໄຂ">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteHouse(<?php echo $house['house_id']; ?>)" class="btn btn-sm btn-outline-danger me-1" title="ລຶບ">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button onclick="generateQR(<?php echo $house['house_id']; ?>)" class="btn btn-sm btn-outline-info" title="ສ້າງ QR Code">
                                    <i class="fas fa-qrcode"></i>
                                </button>
                            </td>
                        </tr>
                        <?php } 
                        if (mysqli_num_rows($result) == 0) { 
                            echo '<tr><td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-home fa-3x mb-2 d-block"></i>
                                    ຍັງບໍ່ມີຂໍ້ມູນ
                                  </td></tr>'; 
                        } 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1) { ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>">«</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                </li>
            <?php } ?>
            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>">»</a>
            </li>
        </ul>
    </nav>
    <?php } ?>
</div>

<script>
function deleteHouse(houseId) { 
    Swal.fire({ 
        title: 'ຢືນຢັນການລຶບ', 
        text: 'ທ່ານຕ້ອງການລຶບຂໍ້ມູນນີ້ແທ້ ຫຼື ບໍ່?', 
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
    Swal.fire({ 
        title: 'ກຳລັງສ້າງ QR Code...', 
        allowOutsideClick: false, 
        didOpen: () => { Swal.showLoading(); } 
    }); 
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
                    html: `<img src="../${response.qr_url}" style="width: 200px; height: 200px; border-radius: 10px;"><br>
                           <code>${response.qr_id}</code><br>
                           <a href="../${response.qr_url}" download class="btn btn-success mt-2">ດາວໂຫຼດ QR Code</a>`, 
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

// ສະແດງ SweetAlert ເມື່ອມີການບັນທຶກສຳເລັດ
<?php if ($success_message): ?>
Swal.fire({
    icon: '<?php echo $success_type; ?>',
    title: '<?php echo $success_message; ?>',
    text: 'ຂໍ້ມູນໄດ້ຖືກບັນທຶກເຂົ້າລະບົບແລ້ວ',
    confirmButtonText: 'ຕົກລົງ',
    timer: 2000,
    showConfirmButton: true
});
<?php endif; ?>
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
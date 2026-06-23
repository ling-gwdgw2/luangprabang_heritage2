<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

include_once '../config/database.php';

$sql = "
SELECT
    h.house_id,
    h.house_number,
    h.house_name_lo,
    h.house_name_en,
    COUNT(v.log_id) AS total_views
FROM heritage_houses h
LEFT JOIN visit_logs v
ON h.house_id = v.house_id
GROUP BY h.house_id, h.house_number, h.house_name_lo, h.house_name_en
ORDER BY total_views DESC
";

$result = mysqli_query($connect, $sql);
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ລາຍງານການເຂົ້າຊົມ</title>

    <!-- Bootstrap 5.3 & Font Awesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts: Noto Sans Lao -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Noto Sans Lao', sans-serif;
            background-color: #f8f6f0; /* พื้นหลังสีครีมอุ่นๆ เหมาะกับแนวประวัติศาสตร์/มรดก */
            color: #333;
        }
        .report-container {
            max-width: 1000px;
            margin-top: 3rem;
            margin-bottom: 3rem;
        }
        .card-custom {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            background: #ffffff;
            overflow: hidden;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #1e4620, #2d6a4f); /* ไล่เฉดสีเขียวธรรมชาติเข้ม ดูภูมิฐาน */
            color: #ffffff;
            padding: 2rem;
            border-bottom: none;
        }
        .table-custom {
            margin-bottom: 0;
        }
        .table-custom thead th {
            background-color: #f1f5f0;
            color: #2d6a4f;
            font-weight: 600;
            text-transform: uppercase;
            border-bottom: 2px solid #e2ebd5;
            padding: 1rem;
        }
        .table-custom tbody tr {
            transition: all 0.2s ease;
        }
        .table-custom tbody tr:hover {
            background-color: #fdfbf7 !important;
            transform: scale(1.005);
        }
        .table-custom td {
            padding: 1rem;
            vertical-align: middle;
        }
        /* ตกแต่ง Badge ตัวเลข */
        .badge-views {
            background-color: #e8f5e9;
            color: #2d6a4f;
            font-weight: 600;
            padding: 0.6em 1em;
            border-radius: 8px;
            border: 1px solid #c8e6c9;
        }
        /* ตกแต่งอันดับ 1, 2, 3 ให้เด่น */
        .rank-badge {
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: bold;
        }
        .rank-1 { background-color: #ffd700; color: #000; } /* ทอง */
        .rank-2 { background-color: #c0c0c0; color: #000; } /* เงิน */
        .rank-3 { background-color: #cd7f32; color: #fff; } /* ทองแดง */
        .rank-other { background-color: #eee; color: #666; }
    </style>
</head>
<body>

<div class="container report-container">
    <div class="card card-custom">
        
        <!-- ส่วนหัวของ Card แบบใหม่ -->
        <div class="card-header-custom d-flex align-items-center justify-content-between">
            <div>
                <h2 class="mb-1 fw-bold">
                    <i class="fa-solid fa-hotel me-2"></i> ລາຍງານເຮືອນມໍລະດົກ
                </h2>
                <p class="mb-0 opacity-75">ຈັດອັນດັບຕາມຈຳນວນຜູ້ເຂົ້າຊົມຫຼາຍທີ່ສຸດ</p>
            </div>
            <div class="bg-white text-dark rounded-circle p-3 shadow-sm d-none d-sm-block">
                <i class="fa-solid fa-chart-line fa-2x text-success"></i>
            </div>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-custom table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="10%" class="text-center">ອັນດັບ</th>
                            <th width="15%">ເລກທີ່ເຮືອນ</th>
                            <th width="50%">ຊື່ເຮືອນມໍລະດົກ</th>
                            <th width="25%" class="text-center">ຈຳນວນຄັ້ງເຂົ້າຊົມ</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php
                    $rank = 1;
                    while($row = mysqli_fetch_assoc($result)){
                        // กำหนด Class ของวงกลมตัวเลขอันดับ
                        if ($rank == 1) $rank_class = 'rank-1';
                        elseif ($rank == 2) $rank_class = 'rank-2';
                        elseif ($rank == 3) $rank_class = 'rank-3';
                        else $rank_class = 'rank-other';
                    ?>

                        <tr>
                            <!-- อันดับ -->
                            <td class="text-center">
                                <span class="rank-badge <?php echo $rank_class; ?>">
                                    <?php echo $rank++; ?>
                                </span>
                            </td>
                            
                            <!-- เลขที่บ้าน -->
                            <td class="fw-bold text-secondary">
                                #<?php echo htmlspecialchars($row['house_number']); ?>
                            </td>
                            
                            <!-- ชื่อบ้าน -->
                            <td>
                                <div class="fw-semibold text-dark fs-5">
                                    <?php echo htmlspecialchars($row['house_name_lo']); ?>
                                </div>
                                <?php if(!empty($row['house_name_en'])): ?>
                                    <small class="text-muted d-block italic"><?php echo htmlspecialchars($row['house_name_en']); ?></small>
                                <?php endif; ?>
                            </td>
                            
                            <!-- จำนวนเข้าชม -->
                            <td class="text-center">
                                <span class="badge-views fs-6">
                                    <i class="fa-regular fa-eye me-1"></i> 
                                    <?php echo number_format($row['total_views']); ?> ຄັ້ງ
                                </span>
                            </td>
                        </tr>

                    <?php } ?>

                    </tbody>
                </table>
            </div> <!-- /table-responsive -->
        </div> <!-- /card-body -->
        
        <!-- ส่วนท้ายการ์ด (อัปเดตเมื่อ...) -->
        <div class="card-footer bg-light text-end text-muted py-3 px-4" style="border-top: 1px solid #eee;">
            <small><i class="fa-regular fa-clock me-1"></i> ອັບເດດລ່າສຸດ: <?php echo date('d/m/Y H:i'); ?></small>
        </div>
    </div>
</div>

</body>
</html>
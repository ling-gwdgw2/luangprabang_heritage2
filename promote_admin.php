<?php
// ============================================================
// ໄຟລ໌ຊົ່ວຄາວ: promote_admin.php
// ⚠️ ໃຊ້ຄັ້ງດຽວ ແລ້ວລຶບໄຟລ໌ນີ້ອອກທັນທີ (ດ້ວຍເຫດຜົນຄວາມປອດໄພ)
// ຈຸດປະສົງ: ຍົກລະດັບ user 'admin' ໃຫ້ມີ role = 'admin'
// ============================================================
header('Content-Type: text/plain; charset=utf-8');

include_once 'config/database.php';

$target_username = 'admin';

// 1) ສະແດງສະຖານະປັດຈຸບັນ
$stmt = mysqli_prepare($connect, "SELECT user_id, username, role, status FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, 's', $target_username);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$user) {
    echo "❌ ບໍ່ພົບ user ທີ່ຊື່ '$target_username' ໃນຖານຂໍ້ມູນ.\n";
    echo "   ກວດສອບຊື່ຜູ້ໃຊ້ ຫຼື ແກ້ໄຂ \$target_username ໃນໄຟລ໌ນີ້.\n";
    exit;
}

echo "ກ່ອນ:  user_id={$user['user_id']}, username={$user['username']}, role={$user['role']}, status={$user['status']}\n";

if ($user['role'] === 'admin') {
    echo "✅ user ນີ້ມີ role = 'admin' ຢູ່ແລ້ວ. ບໍ່ຕ້ອງເຮັດຫຍັງເພີ່ມ.\n";
    echo "   ຖ້າຍັງເຂົ້າບໍ່ໄດ້ → ໃຫ້ logout ແລ້ວ login ໃໝ່ (role ຖືກໂຫຼດຕອນ login).\n";
    echo "\n⚠️ ກະລຸນາລຶບໄຟລ໌ promote_admin.php ນີ້ອອກທັນທີ.\n";
    exit;
}

// 2) ຍົກລະດັບເປັນ admin
$upd = mysqli_prepare($connect, "UPDATE users SET role = 'admin' WHERE username = ?");
mysqli_stmt_bind_param($upd, 's', $target_username);
$ok = mysqli_stmt_execute($upd);
$affected = mysqli_stmt_affected_rows($upd);
mysqli_stmt_close($upd);

if (!$ok) {
    echo "❌ UPDATE ລົ້ມເຫຼວ: " . mysqli_error($connect) . "\n";
    exit;
}

// 3) ຢືນຢັນຜົນ
$stmt2 = mysqli_prepare($connect, "SELECT user_id, username, role, status FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt2, 's', $target_username);
mysqli_stmt_execute($stmt2);
$res2 = mysqli_stmt_get_result($stmt2);
$after = mysqli_fetch_assoc($res2);
mysqli_stmt_close($stmt2);

echo "ຫຼັງ:  user_id={$after['user_id']}, username={$after['username']}, role={$after['role']}, status={$after['status']}\n";
echo "ແຖວທີ່ຖືກປ່ຽນ: $affected\n";
echo "\n✅ ສຳເລັດ! ກະລຸນາ logout ແລ້ວ login ໃໝ່ ເພື່ອໃຫ້ session ຮັບ role ໃໝ່.\n";
echo "\n⚠️⚠️ ສຳຄັນ: ລຶບໄຟລ໌ promote_admin.php ນີ້ອອກທັນທີ ຫຼັງໃຊ້ແລ້ວ! ⚠️⚠️\n";
?>

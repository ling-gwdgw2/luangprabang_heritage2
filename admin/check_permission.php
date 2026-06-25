<?php
// ============================================
// ໄຟລ໌: admin/check_permission.php
// ຟັງຊັນກວດສອບສິດທິຂອງຜູ້ໃຊ້
// ============================================

function getCurrentUserRole() {
    return $_SESSION['admin_role'] ?? 'viewer'; // 🛠️ ລະບົບຂອງເຈົ້າໃຊ້ admin_role
}

function getUserRoleLevel($role = null) {
    $role = $role ?: getCurrentUserRole();
    $levels = [
        'admin' => 3,
        'staff' => 2,
        'viewer' => 1
    ];
    return $levels[$role] ?? 1;
}

// ກວດສອບສິດຕາມລະດັບທີ່ກຳນົດ
function hasPermission($required_role = 'viewer') {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        return false;
    }
    return getUserRoleLevel() >= getUserRoleLevel($required_role);
}

// ສາມາດເພີ່ມ/ແກ້ໄຂຂໍ້ມູນ (Admin ແລະ Staff)
function canEdit() {
    return in_array(getCurrentUserRole(), ['admin']);
}

// ສາມາດລຶບຂໍ້ມູນ (Admin ແລະ Staff)
function canDelete() {
    return in_array(getCurrentUserRole(), ['admin']);
}

// ສາມາດເພີ່ມຂໍ້ມູນ (Admin ແລະ Staff)
function canAdd() {
    return in_array(getCurrentUserRole(), ['admin', 'staff']);
}

// 🛠️ ແກ້ໄຂຈຸດນີ້: ເພີ່ມ 'staff' ເຂົ້າໄປ ເພື່ອໃຫ້ພະນັກງານສາມາດຈັດການຜູ້ໃຊ້ໄດ້
function canManageUsers() {
    return in_array(getCurrentUserRole(), ['admin']);
}

// ສະແດງຊື່ສິດເປັນພາສາລາວ
function getRoleName() {
    $role = getCurrentUserRole();
    switch($role) {
        case 'admin': return 'ຜູ້ດູແລລະບົບ';
        case 'staff': return 'ພະນັກງານ';
        default: return 'ຜູ້ເບິ່ງ';
    }
}
?>
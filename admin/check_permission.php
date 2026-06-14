<?php
// ============================================
// ໄຟລ໌: admin/check_permission.php
// ຟັງຊັນກວດສອບສິດທິຂອງຜູ້ໃຊ້
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getCurrentUserRole() {
    return $_SESSION['admin_role'] ?? 'viewer';
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
    return in_array(getCurrentUserRole(), ['admin', 'staff']);
}

// ສາມາດລຶບຂໍ້ມູນ (Admin ແລະ Staff)
function canDelete() {
    return in_array(getCurrentUserRole(), ['admin', 'staff']);
}

// ສາມາດເພີ່ມຂໍ້ມູນ (Admin ແລະ Staff)
function canAdd() {
    return in_array(getCurrentUserRole(), ['admin', 'staff']);
}

// ສາມາດຈັດການຜູ້ໃຊ້ (Admin ເທົ່ານັ້ນ)
function canManageUsers() {
    return getCurrentUserRole() === 'admin';
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
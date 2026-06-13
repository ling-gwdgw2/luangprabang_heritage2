<?php
// config/domain.php
// ປ່ຽນເປັນໂດເມນຂອງເຈົ້າທີ່ຂຶ້ນຢູ່ Railway

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];

// ກຳນົດໂດເມນສຳລັບ QR Code (ໃຊ້ໂດເມນຈິງ)
// ຕົວຢ່າງ: https://heritage-luangprabang.up.railway.app
define('BASE_URL', 'https://your-domain.up.railway.app'); // ປ່ຽນຕາມໂດເມນເຈົ້າ

// ສຳລັບການພັດທະນາໃນ localhost, ໃຊ້ ngrok ຫຼື serveo
// ຫຼືຕັ້ງຄ່າ hosts file

return ['base_url' => BASE_URL];
?>
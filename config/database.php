<?php
$server = getenv('MYSQLHOST') ?: "127.0.0.1";
$user = getenv('MYSQLUSER') ?: "root";
$password = getenv('MYSQLPASSWORD') ?: "";
$db_name = getenv('MYSQLDATABASE') ?: "luangprabang_heritage";
$port = getenv('MYSQLPORT') ?: 3307;

$connect = mysqli_connect($server, $user, $password, $db_name, $port);
mysqli_set_charset($connect, "utf8");

if (!$connect) {
    die("ການເຊື່ອມຕໍ່ຖານຂໍ້ມູນລົ້ມເຫຼວ: " . mysqli_connect_error());
}

mysqli_query($connect, "CREATE TABLE IF NOT EXISTS image_store (
    filename VARCHAR(255) NOT NULL PRIMARY KEY,
    image_mime VARCHAR(100) NOT NULL,
    image_data LONGTEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8");
?>
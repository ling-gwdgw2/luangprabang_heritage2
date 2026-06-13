<?php
// test_db_connection.php
header('Content-Type: text/html; charset=utf-8');

echo "<h1>🔍 ທົດສອບການເຊື່ອມຕໍ່ຖານຂໍ້ມູນ</h1>";

// ອ່ານຄ່າຈາກ Environment Variables
echo "<h2>📋 Environment Variables:</h2>";
echo "<pre>";
echo "MYSQLHOST: " . (getenv('MYSQLHOST') ?: '❌ not set') . "\n";
echo "MYSQLUSER: " . (getenv('MYSQLUSER') ?: '❌ not set') . "\n";
echo "MYSQLPASSWORD: " . (getenv('MYSQLPASSWORD') ? '✅ set (hidden)' : '❌ not set') . "\n";
echo "MYSQLDATABASE: " . (getenv('MYSQLDATABASE') ?: '❌ not set') . "\n";
echo "MYSQLPORT: " . (getenv('MYSQLPORT') ?: '❌ not set') . "\n";
echo "RAILWAY_ENVIRONMENT: " . (getenv('RAILWAY_ENVIRONMENT') ?: '❌ not set') . "\n";
echo "</pre>";

// ລອງເຊື່ອມຕໍ່ແບບຕັ້ງຄ່າໂດຍກົງ
$host = getenv('MYSQLHOST') ?: 'localhost';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: '';
$db = getenv('MYSQLDATABASE') ?: 'railway';
$port = getenv('MYSQLPORT') ?: 3306;

echo "<h2>🔌 ກຳລັງເຊື່ອມຕໍ່...</h2>";
echo "<p>Host: $host:$port<br>Database: $db<br>User: $user</p>";

$conn = @mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    echo "<p style='color:red'>❌ ເຊື່ອມຕໍ່ບໍ່ສຳເລັດ: " . mysqli_connect_error() . "</p>";
    
    // ລອງເຊື່ອມຕໍ່ແບບບໍ່ລະບຸ database
    echo "<h3>ລອງເຊື່ອມຕໍ່ແບບບໍ່ມີ database:</h3>";
    $conn2 = @mysqli_connect($host, $user, $pass, '', $port);
    if ($conn2) {
        echo "<p style='color:green'>✅ ເຊື່ອມຕໍ່ MySQL ສຳເລັດ (ແຕ່ບໍ່ມີ database)</p>";
        
        // ສະແດງລາຍຊື່ databases
        $result = mysqli_query($conn2, "SHOW DATABASES");
        echo "<h3>📚 ລາຍຊື່ Databases:</h3><ul>";
        while ($row = mysqli_fetch_row($result)) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
        mysqli_close($conn2);
    } else {
        echo "<p style='color:red'>❌ ເຊື່ອມຕໍ່ MySQL ບໍ່ສຳເລັດ: " . mysqli_connect_error() . "</p>";
    }
} else {
    echo "<p style='color:green'>✅ ເຊື່ອມຕໍ່ສຳເລັດ!</p>";
    
    // ສະແດງລາຍຊື່ຕາຕະລາງ
    $result = mysqli_query($conn, "SHOW TABLES");
    if ($result) {
        echo "<h3>📋 ລາຍຊື່ຕາຕະລາງ:</h3><ul>";
        while ($row = mysqli_fetch_row($result)) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color:orange'>⚠️ ບໍ່ມີຕາຕະລາງໃນ database ນີ້</p>";
    }
    
    mysqli_close($conn);
}
?>
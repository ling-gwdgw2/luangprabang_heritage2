<?php
// railway_setup.php - ເປີດໃນ browser ເທື່ອດຽວຫຼັງຈາກ deploy
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Railway Database Setup</h1>";

// ກວດສອບ environment
echo "<h2>Environment Check:</h2>";
echo "<pre>";
echo "RAILWAY_ENVIRONMENT: " . (getenv('RAILWAY_ENVIRONMENT') ?: 'Not set (Local)' ) . "\n";
echo "MYSQL_URL: " . (getenv('MYSQL_URL') ? 'Set' : 'Not set') . "\n";
echo "MYSQLHOST: " . (getenv('MYSQLHOST') ?: 'Not set') . "\n";
echo "MYSQLDATABASE: " . (getenv('MYSQLDATABASE') ?: 'Not set') . "\n";
echo "</pre>";

// ລອງເຊື່ອມຕໍ່
include_once 'config/database.php';

if ($connect) {
    echo "<p style='color: green'>✅ ເຊື່ອມຕໍ່ຖານຂໍ້ມູນສຳເລັດ!</p>";
    
    // ສ້າງຕາຕະລາງ
    echo "<h2>Creating Tables...</h2>";
    
    $sql_tables = "
    CREATE TABLE IF NOT EXISTS users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        fullname_lo VARCHAR(100),
        fullname_en VARCHAR(100),
        email VARCHAR(100),
        role ENUM('admin', 'staff', 'viewer') DEFAULT 'viewer',
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    
    CREATE TABLE IF NOT EXISTS heritage_houses (
        house_id INT AUTO_INCREMENT PRIMARY KEY,
        qr_code VARCHAR(100) UNIQUE NOT NULL,
        house_number VARCHAR(50),
        house_name_lo VARCHAR(255),
        house_name_en VARCHAR(255),
        owner_name_lo VARCHAR(255),
        owner_name_en VARCHAR(255),
        construction_year INT,
        architectural_style_lo VARCHAR(255),
        architectural_style_en VARCHAR(255),
        historical_significance_lo TEXT,
        historical_significance_en TEXT,
        description_lo TEXT,
        description_en TEXT,
        image_main VARCHAR(255),
        status ENUM('active', 'inactive') DEFAULT 'active',
        house_type VARCHAR(100),
        building_material VARCHAR(100),
        latitude DECIMAL(10,8),
        longitude DECIMAL(11,8),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );
    
    CREATE TABLE IF NOT EXISTS heritage_images (
        image_id INT AUTO_INCREMENT PRIMARY KEY,
        house_id INT,
        image_path VARCHAR(255),
        image_caption_lo VARCHAR(255),
        image_caption_en VARCHAR(255),
        display_order INT DEFAULT 0,
        FOREIGN KEY (house_id) REFERENCES heritage_houses(house_id) ON DELETE CASCADE
    );
    
    CREATE TABLE IF NOT EXISTS image_store (
        id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255) UNIQUE NOT NULL,
        image_mime VARCHAR(100),
        image_data LONGBLOB,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS visit_logs (
        log_id INT AUTO_INCREMENT PRIMARY KEY,
        house_id INT,
        visitor_ip VARCHAR(45),
        visitor_device VARCHAR(255),
        visit_date DATE,
        visit_time TIME,
        FOREIGN KEY (house_id) REFERENCES heritage_houses(house_id) ON DELETE SET NULL
    );
    ";

    $queries = explode(';', $sql_tables);
    $success = true;

    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            if (mysqli_query($connect, $query)) {
                echo "<p style='color: green'>✅ " . substr($query, 0, 50) . "...</p>";
            } else {
                echo "<p style='color: red'>❌ Error: " . mysqli_error($connect) . "</p>";
                $success = false;
            }
        }
    }

    // Add missing columns to visit_logs if the table already existed without them
    $alter_queries = [
        "ALTER TABLE visit_logs ADD COLUMN IF NOT EXISTS visitor_device VARCHAR(255)",
        "ALTER TABLE visit_logs ADD COLUMN IF NOT EXISTS visit_time TIME",
    ];
    foreach ($alter_queries as $q) {
        mysqli_query($connect, $q);
    }
    echo "<p style='color: green'>✅ visit_logs columns verified</p>";
    
    // ສ້າງຜູ້ໃຊ້ admin
    if ($success) {
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $check_admin = mysqli_query($connect, "SELECT * FROM users WHERE username = 'admin'");
        
        if (mysqli_num_rows($check_admin) == 0) {
            $insert = "INSERT INTO users (username, password, fullname_lo, role, status) 
                       VALUES ('admin', '$hashed_password', 'ຜູ້ຈັດການລະບົບ', 'admin', 'active')";
            if (mysqli_query($connect, $insert)) {
                echo "<p style='color: green'>✅ ສ້າງຜູ້ໃຊ້ admin ສຳເລັດ!</p>";
                echo "<p>ຊື່ຜູ້ໃຊ້: <strong>admin</strong><br>ລະຫັດຜ່ານ: <strong>admin123</strong></p>";
            }
        } else {
            echo "<p>ℹ️ ຜູ້ໃຊ້ admin ມີແລ້ວ</p>";
        }
    }
    
} else {
    echo "<p style='color: red'>❌ ບໍ່ສາມາດເຊື່ອມຕໍ່ຖານຂໍ້ມູນໄດ້</p>";
    echo "<p>ກະລຸນາກວດສອບ Environment Variables ໃນ Railway:</p>";
    echo "<ul>";
    echo "<li>MYSQL_URL (ຄວນມີໂດຍອັດຕະໂນມັດຖ້າໃຊ້ MySQL plugin)</li>";
    echo "<li>ຫຼືຕັ້ງຄ່າ: MYSQLHOST, MYSQLUSER, MYSQLPASSWORD, MYSQLDATABASE, MYSQLPORT</li>";
    echo "</ul>";
}
?>
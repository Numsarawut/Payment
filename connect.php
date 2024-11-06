<?php
// กำหนดค่าการเชื่อมต่อ
$servername = "localhost";
$username = "root";       
$password = "รหัสฐานข้อมูล";        
$dbname = "ชื่อฐานข้อมูล";     // ชื่อฐานข้อมูลที่ต้องการเชื่อมต่อ

// เปิดการรายงานข้อผิดพลาด
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // สร้างการเชื่อมต่อ
    $conn = new mysqli($servername, $username, $password, $dbname);

    // ตรวจสอบการเชื่อมต่อ
   // echo "เชื่อมต่อสำเร็จ";

    // ที่นี่คุณสามารถทำงานกับฐานข้อมูลได้

} catch (mysqli_sql_exception $e) {
    // แสดงข้อผิดพลาด
    die("การเชื่อมต่อล้มเหลว: " . $e->getMessage());
} 

?>

<?php
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือยัง
if (!isset($_SESSION['username'])) {
    header("Location: Loginpayment.php");
    exit();
}


// เชื่อมต่อฐานข้อมูล
include 'connect.php';

// Query ข้อมูลธุรกรรมทั้งหมดสำหรับแสดงในตาราง
$sql = "SELECT payment_id, author_id, title, fullname_th, fullname_eg, amount, payment_date, status, payment_method, payment_other, Institution, other, slip_file 
FROM payments 
WHERE status = 'Pending' 
ORDER BY payment_date DESC 
LIMIT 5;";
$transactions = $conn->query($sql);

// ดึงข้อมูลยอดรวมจากธุรกรรมที่ได้รับการอนุมัติ
$sql2 = "SELECT amount FROM payments WHERE status = 'approved'";
$result = $conn->query($sql2);

$totalAmount = 0;

// ตรวจสอบว่ามีข้อมูลหรือไม่
if ($result && $result->num_rows > 0) {
    // วนลูปผ่านแต่ละแถว
    while($row = $result->fetch_assoc()) {
        // ตัดตัวอักษรออกจาก amount
        $amountStr = $row['amount'];

        // ใช้ regex เพื่อดึงตัวเลข
        preg_match_all('!\d+(\.\d+)?!', $amountStr, $matches);

        // รวมตัวเลข
        foreach ($matches[0] as $match) {
            $totalAmount += floatval($match); // แปลงเป็น float และบวก
        }
    }
}


// ดึงข้อมูลยอดรวมจากธุรกรรมที่ไม่ได้รับการอนุมัติ
$sql3 = "SELECT amount FROM payments WHERE status = 'pending'";

$result = $conn->query($sql3);

$totalAmount3 = 0;

// ตรวจสอบว่ามีข้อมูลหรือไม่
if ($result && $result->num_rows > 0) {
    // วนลูปผ่านแต่ละแถว
    while($row = $result->fetch_assoc()) {
        // ตัดตัวอักษรออกจาก amount
        $amountStr3 = $row['amount'];

        // ใช้ regex เพื่อดึงตัวเลข
        preg_match_all('!\d+(\.\d+)?!', $amountStr3, $matches3);

        // รวมตัวเลข
        foreach ($matches3[0] as $match3) {
            $totalAmount3 += floatval($match3); // แปลงเป็น float และบวก
        }
    }
}

$conn->close(); // ปิดการเชื่อมต่อ
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benjamit Payment</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Prompt&display=swap');

        /* Reset */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Prompt', sans-serif; background-color: #f4f4f9; display: flex; flex-direction: column; min-height: 100vh; margin: 0; }
        .dashboard-container { width: 100%; max-width: 1200px; margin: 2rem auto; padding: 1rem; }
        .dashboard-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem; background-color:#007bff; color: white; border-radius: 8px; margin-bottom: 2rem; }
        .dashboard-header h1 { font-size: 1.5rem; }
        .dashboard-header button { background-color: #0056b3; color: white; border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer; transition: background-color 0.3s ease; }
        .dashboard-header button:hover { background-color: #003a80; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background-color: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); text-align: center; transition: transform 0.2s; }
        .stat-card h2 { font-size: 1rem; color: #333; margin-bottom: 0.5rem; }
        .stat-card p { font-size: 1.5rem; font-weight: bold; color: #007bff; }
        .stat-card:hover { transform: scale(1.05); }
        .transaction-table { width: 100%; border-collapse: collapse; background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); }
        .transaction-table th, .transaction-table td { padding: 1rem; text-align: left; border-bottom: 1px solid #f0f0f0; }
        .transaction-table th { background-color: #007bff; color: white; font-weight: normal; }
        .transaction-table tr:hover { background-color: #f4f4f9; }
        a { text-decoration: none; }
        
/* Navbar Styles */
        .navbar { background-color: #dcdcdc; padding: 1rem; color: white; }
        .navbar-container { display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: auto; }
        .navbar-logo { font-size: 1.5rem; }
        .navbar-menu { display: flex; gap: 1rem; }
        .navbar-menu a { color: black;  text-decoration: none; }
        .navbar-toggle { display: none; font-size: 1.5rem; cursor: pointer; }
/* Responsive Navbar */
@media (max-width: 768px) {
            .navbar-menu { display: none; flex-direction: column; }
            .navbar-menu.active { display: flex; } /* Show menu when active */
            .navbar-toggle { display: block; }
        }

        @media (max-width: 768px) {
    .dashboard-header { flex-direction: column; text-align: center; }
    .dashboard-header h1 { font-size: 1.3rem; }
    .dashboard-header button { padding: 0.4rem 0.8rem; font-size: 0.9rem; }
}

@media (max-width: 480px) {
    .dashboard-container { padding: 0.5rem; }
    .dashboard-header h1 { font-size: 1.1rem; }
    .dashboard-header button { padding: 0.3rem 0.7rem; font-size: 0.85rem; }
    .stat-card h2 { font-size: 0.95rem; }
    .stat-card p { font-size: 1.2rem; }
    .transaction-table th, .transaction-table td { padding: 0.8rem; font-size: 0.9rem; }
}

@media (max-width: 355px) {
    .dashboard-header { padding: 0.5rem; }
    .dashboard-header h1 { font-size: 1rem; }
    .stats-grid { gap: 1rem; grid-template-columns: 1fr; } /* ปรับให้มีคอลัมน์เดียว */
    .stat-card { padding: 1rem; }
    .stat-card h2 { font-size: 0.85rem; }
    .stat-card p { font-size: 1rem; }
    .transaction-table th, .transaction-table td { padding: 0.6rem; font-size: 0.8rem; }
    .transaction-table { font-size: 0.8rem; }
}

    </style>
</head>
<script>
        // Toggle Navbar Menu
        function toggleMenu() {
            const menu = document.querySelector('.navbar-menu');
            menu.classList.toggle('active');
        }
    </script>
<body>


    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-logo"><img src="img/logoBen1 (2).png" alt=""></div>
            <div class="navbar-toggle" onclick="toggleMenu()">
                <i class="fas fa-bars"></i>
            </div>
            <div class="navbar-menu">
                <a href="https://benjamit.thonburi-u.ac.th/index.php">หน้าหลัก</a>
                <a href="logout.php">ออกจากระบบ</a>
               
            </div>
        </div>
    </nav>


    <div class="dashboard-container">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <h1>Benjamit payment</h1>
           
        </div>

        <!-- Stats Section -->
        <div class="stats-grid">
            <a href="review_payments.php"><div class="stat-card">
                <h2>ปรับสถานะรายบุคคล</h2>
                <p><i class="fas fa-user"></i></p>
            </div></a>
            <a href="review_group_payment.php"><div class="stat-card">
                <h2>ปรับสถานะรายกลุ่ม</h2>
                <p><i class="fas fa-users"></i></p>
            </div></a>
            <a href="#"><div class="stat-card">
                <h2>บทความที่ยังไม่ชำระเงิน</h2>
                <p><i class="fas fa-file-alt"></i></p>
            </div></a>
            <a href="review_print.php"><div class="stat-card">
                <h2>รายงานการชำระเงิน</h2>
                <p><i class="fas fa-print"></i></p>
            </div></a>
            <a href="edit_payment.php"><div class="stat-card">
                <h2>แก้ไขข้อมูลการชำระเงิน</h2>
                <p><i class="fas fa-edit"></i></p>
            </div></a>
        </div>
        

        <!-- Transaction Table -->
         <div> <h2> รายการลงทะเบียนชำระเงินล่าสุด </h2></div>
        <table class="transaction-table">
            <thead>
                <tr>
                    <th>รหัสชำระเงิน</th>
                    <th>วันเวลา</th>
                    <th>ชื่อเรื่อง</th>
                    <th>สถานะ</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($transactions && $transactions->num_rows > 0): ?>
                    <?php while($row = $transactions->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row["payment_id"]; ?></td>
                            <td><?php echo date("Y-m-d", strtotime($row["payment_date"])); ?></td>
                            <td><?php echo $row["title"]; ?></td>
                            <td><?php echo ucfirst($row["status"]); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No transactions available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Total Amount Display -->
        <div style="margin-top: 1rem; font-size: 1.2rem;">
            <?php echo "ยอดรวมการอนุมัติทั้งหมด: " . number_format($totalAmount, 2); ?>
        </div>
        <div style="margin-top: 1rem; font-size: 1.2rem;">
            <?php echo "ยอดรวมการที่รอการตรวจสอบทั้งหมด: " . number_format($totalAmount3, 2); ?>
        </div>
    </div>
</body>
</html>

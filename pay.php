<?php
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือยัง
if (!isset($_SESSION['username'])) {
    header("Location: Loginpayment.php");
    exit();
}

// เชื่อมต่อฐานข้อมูล
include 'connect.php';

// รับค่าอีเมลจาก session และแปลงเป็นตัวพิมพ์เล็ก
$email = ($_SESSION['email']);
$userid = ($_SESSION['user_id']);

// ค้นหาอีเมลในฐานข้อมูล
$sql = "SELECT * FROM view_notpay WHERE email = '$email' ";

$result = $conn->query($sql);

// กำหนดค่าตัวแปรเริ่มต้น
$formVisible = false;
$formVisible2 = false;
$emailError = "";
$username_th = "";
$surname_th = "";
$firstname_en = "";
$lastname_en = "";

// ตรวจสอบว่าพบข้อมูลอีเมลในฐานข้อมูลหรือไม่
if ($result->num_rows > 0) {
    // สร้างอาเรย์เพื่อเก็บข้อมูลหลายแถว
    $articles = [];
    while ($row = $result->fetch_assoc()) {
        $articles[] = $row;
    }

    $formVisible = true;

    // ค้นหาอีเมลในตาราง tb_ojs
    $sql2 = "SELECT * FROM view_firstauthorentries WHERE email = '$email' ";
    $result2 = $conn->query($sql2);
   
    if ($result2->num_rows > 0) {
        $row2 = $result2->fetch_assoc();
        $all_settings = explode(',', $row2['all_settings']);
        
        // Populate name parts
        $name_parts = explode(',', $row2['all_settings']);
        
        // Define variables for form usage
        $username_th = isset($name_parts[0]) ? $name_parts[0] : '';
        $firstname_en = isset($name_parts[1]) ? $name_parts[1] : '';
        $surname_th = isset($name_parts[2]) ? $name_parts[2] : '';
        $lastname_en = isset($name_parts[3]) ? $name_parts[3] : '';
    
        
        $formVisible2 = true;
    }
    
} else {
    $Alert= '<h5 style="text-align:center; background-color:#ffb08e; padding:20px; border-radius:10px;">คุณยังไม่มีค่าใช้จ่ายที่ต้องชำระในขณะนี้</h5>';

   // $emailError = "อีเมลไม่ถูกต้องหรือท่านยังไม่ได้ลงทะเบียนสมัครสมาชิก";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระค่าลงทะเบียนส่งบทความ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Prompt&display=swap');
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #e8f0f8;
            color: #333;
        }
        .form-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-header {
            background-color: #165e8a;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            color: white;
            border-radius: 8px 8px 0 0;
        }

        .form-headersay {
            background-color: #165e8a;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            color: white;
        }
        .required {
            color: red;
        }
       

        .submit-btn {
            background-color: #4CAF50;
            color: white;
            width: 100%;
        }
        .submit-btn:hover {
            background-color: #45a049;
        }
        .navbar-custom {
            background-color: #165e8a;
        }
        .navbar-custom .nav-link.active {
            color: #e8f0f8;
        }
        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: white;
        }
        .navbar-custom .navbar-brand:hover,
        .navbar-custom .nav-link:hover {
            color: #ffc107;
        }
        .banner-section img {
            width: 100%;
            height: auto;
        }

        .btn {
        display: inline-block;
        padding: 8px 16px; /* ขนาดของปุ่ม */
        font-size: 14px; /* ขนาดตัวอักษร */
        color: white; /* สีตัวอักษร */
        background-color: #007bff; /* สีพื้นหลัง */
        border: none; /* ไม่ต้องการเส้นขอบ */
        border-radius: 4px; /* มุมโค้ง */
        text-align: center; /* จัดกึ่งกลาง */
        text-decoration: none; /* ไม่ต้องการขีดเส้นใต้ */
        transition: background-color 0.3s; /* เอฟเฟกต์เปลี่ยนสีเมื่อชี้เมาส์ */
    }
        @media (max-width: 576px) {
            .form-container {
                padding: 14px;
            }
            .navbar-custom .navbar-brand {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">ชำระเงิน</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">หน้าหลัก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="Loginpayment.php">ออกจากระบบ</a>
                    </li>
                
                </ul>
            </div>
        </div>
    </nav>

    <!-- Banner Section -->
    <section class="banner-section">
        <img src="Brochure_benjamit.png" alt="แบนเนอร์ของเว็บไซต์">
    </section>

           <!-- สวัสดีอิอิ-->
           <div class="form-headersay">
        <div class="mb-3">
                <label for="sayhello" class="form-label"> <h5>สวัสดีคุณ <?php echo htmlspecialchars($_SESSION['username']) . $Alert; ?> </h5></label>
        </div>

        <div class="mb-3">
        <a href="checkstatusme.php" class="adjas btn">ตรวจสอบสถานะการชำระเงิน</a>
         </div>
        </div>
               <!--สิ้นสุดสวัสดี -->
       
     
    <div class="container form-container">
        <div class="form-header">แบบฟอร์มชำระค่าลงทะเบียนส่งบทความ</div>


         

        <?php if ($formVisible): ?>
        <form method="POST" action="register.php" enctype="multipart/form-data"  >
            <div class="mb-3">
                <label for="status_group5" class="form-label">ชื่อบทความ : </label>
                <select id="statusGroupSelect5" name="status_group5" class="form-select" onchange="updatePublicationId()">
                    <option value="">--เลือก--</option>
                    <?php foreach ($articles as $article): ?>
                        <option value="<?php echo htmlspecialchars($article['title']); ?>">
                            <?php echo htmlspecialchars($article['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="required"><i style="color:red"> หมายเหตุ จะแสดงแค่บทความที่ยังไม่ลงทะเบียนชำระเงินเท่านั้น </i> *</span> 
            </div>

          <!-- ซ่อน Select สำหรับ author_id -->
     <div style="display: none;">
    <label for="authorSelect">Author ID:</label>
    <select id="authorSelect"    name="author_id">
        <option value="">--เลือก--</option>
    </select>
    </div>

            <div class="mb-3">
                <label for="status_group4" class="form-label">รหัสบทความ :</label>
                <select id="statusGroupSelect4" name="status_group4" class="form-select">
                    <option value="">--เลือก--</option>
                </select>
                <span class="required"> *</span>
            </div>

            <div class="mb-3">
                <label for="email1" class="form-label">อีเมล :</label>
                <input type="text" name="email1" class="form-control" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly>
            </div>

            <?php if ($formVisible2): ?>
            <div class="mb-3">
                <label for="username_th" class="form-label">ชื่อภาษาไทย :</label>
                <input type="text" name="username_th_display" class="form-control" value="<?php  $username_th_trimmed = trim($username_th); echo !empty($username_th_trimmed) ? htmlspecialchars($username_th_trimmed) : 'ไม่ได้ระบุ'; ?>" readonly>
                <!-- ซ่อนค่าจริงที่จะถูกส่ง -->
                 <input type="hidden" name="username_th" class="form-control"  value="<?php echo htmlspecialchars($username_th_trimmed); ?>">
             </div>
    

            <div class="mb-3">
                <label for="username_eg" class="form-label">ชื่อภาษาอังกฤษ:</label>
                <input type="text" name="username_eg_display" class="form-control" value="<?php  $firstname_en_trimmed = trim($firstname_en);  echo !empty($firstname_en_trimmed) ? htmlspecialchars($firstname_en_trimmed) : 'ไม่ได้ระบุ'; ?>" readonly>
                <!-- ซ่อนค่าจริงที่จะถูกส่ง -->
                <input type="hidden" name="username_eg" class="form-control"  value="<?php echo htmlspecialchars($firstname_en_trimmed); ?>">
             </div>

            <div class="mb-3">
                <label for="lastname_th" class="form-label"> นามสกุลภาษาไทย :</label>
                <input type="text" name="surname_th_display" class="form-control" value="<?php  $surname_th_trimmed = trim($surname_th);  echo !empty($surname_th_trimmed) ? htmlspecialchars($surname_th_trimmed) : 'ไม่ได้ระบุ'; ?>" readonly>
                <!-- ซ่อนค่าจริงที่จะถูกส่ง -->
                <input type="hidden" name="surname_th" class="form-control" value="<?php echo htmlspecialchars($surname_th_trimmed); ?>">
            </div>
            
            <div class="mb-3">
                <label for="lastname_eg" class="form-label">นามสกุลภาษาอังกฤษ :</label>
                <input type="text" name="lastname_en_display" class="form-control" value="<?php  $lastname_entrimmed = trim($lastname_en); echo !empty($lastname_entrimmed) ? htmlspecialchars($lastname_entrimmed) : 'ไม่ได้ระบุ'; ?>" readonly>
                <!-- ซ่อนค่าจริงที่จะถูกส่ง -->
                <input type="hidden" name="lastname_en" class="form-control" value="<?php echo htmlspecialchars($lastname_entrimmed); ?>">
            </div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="status_group" class="form-label">สถาบัน :</label>
                <select id="statusGroupSelect" name="status_group0" class="form-select" required onchange="toggleOtherInput()">
                <option value="">--เลือก--</option>
                <option value="สมาคมสถาบันอุดมศึกษาเอกชนแห่งประเทศไทย">สมาคมสถาบันอุดมศึกษาเอกชนแห่งประเทศไทย</option>
                <option value="สทสท (ThaiAECT)">สทสท (ThaiAECT)</option>
                <option value="มหาวิทยาลัยธนบุรี (Thonburi University)">มหาวิทยาลัยธนบุรี (Thonburi University)</option>
                <option value="มหาวิทยาลัยนอร์ทกรุงเทพ (North Bangkok University)">มหาวิทยาลัยนอร์ทกรุงเทพ (North Bangkok University)</option>
                <option value="มหาวิทยาลัยเซาธ์อีสท์บางกอก (Southeast Bangkok College)">มหาวิทยาลัยเซาธ์อีสท์บางกอก (Southeast Bangkok College)</option>
                <option value="มหาวิทยาลัยกรุงเทพสุวรรณภูมิ (Bangkok Suvarnabhumi University)">มหาวิทยาลัยกรุงเทพสุวรรณภูมิ (Bangkok Suvarnabhumi University)</option>
                <option value="มหาวิทยาลัยฟาร์อีสเทอร์น (The Far Eastern University)">มหาวิทยาลัยฟาร์อีสเทอร์น (The Far Eastern University)</option>
                 <option value="มหาวิทยาลัยนอร์ท-เชียงใหม่ (North-Chiang Mai University)">มหาวิทยาลัยนอร์ท-เชียงใหม่ (North-Chiang Mai University)</option>
                <option value="other">อื่น ๆ</option> <!-- เพิ่มตัวเลือก "อื่น ๆ" -->
                 </select>
         
                <span class="required"> *</span>
            </div>
              <!-- Input สำหรับกรอกสถาบันที่ไม่ได้อยู่ในตัวเลือก -->
            <div id="otherInstitution">
        <label for="otherInstitutionInput">กรอกชื่อสถาบันอื่น:</label>
        <input type="text" id="otherInstitutionInput" name="or_ithenstitution" class="form-control" placeholder="กรอกชื่อสถาบันอื่นที่นี่" require>
        <span class="required"> *</span>
           </div>
             
            <div class="mb-3">
                <label for="status_group" class="form-label">ประเภทการเสนอ :</label>
                <select id="statusGroupSelect1" name="status_group1" class="form-select">
              <option value="">--เลือก--</option>
              <option value="ผู้นำเสนอทั่วไป">ผู้นำเสนอทั่วไป</option>
            <option value="ผู้นำเสนอสมาชิกเครือข่าย">ผู้นำเสนอสมาชิกเครือข่าย</option>
            <option value="นักศึกษาบัณฑิตระดับเครือข่าย">นักศึกษาบัณฑิตระดับเครือข่าย</option>
            <option value="ระดับนานาชาติ">ระดับนานาชาติ</option>
               </select>
                <span class="required"> *</span>
            </div>

            <?php
           // กำหนดโซนเวลาเป็นประเทศไทย (ICT - เวลามาตรฐานอินโดจีน)
date_default_timezone_set('Asia/Bangkok');

// วันที่เป้าหมายในโซนเวลาประเทศไทย
$targetDate = strtotime('2025-01-31');

// รับวันที่และเวลาปัจจุบันในโซนเวลาประเทศไทย
$currentDate = time();

// เปรียบเทียบวันที่ทั้งสอง
if ($currentDate < $targetDate) {
            $a = 2500;
            $b = 2300;
            $c = 1800;
            $d = 4600;
            $title = "อัตราการลงทะเบียน(ล่วงหน้า)";
            $typy_time ="รอบล่วงหน้า";
} elseif ($currentDate > $targetDate) {
        $a = 3000;
        $b = 2500;
        $c = 2500;
        $d = 4600;
        $title = "อัตราการลงทะเบียน(ปกติ)";
        $typy_time ="รอบปกติ";
} else {
    $a = 2500;
            $b = 2300;
            $c = 1800;
            $d = 4600;
            $title = "อัตราการลงทะเบียน(ล่วงหน้า)";}
            $typy_time ="รอบล่วงหน้า";
            ?>
  <label for="status_group" class="form-label"><?php echo $title; ?> :</label>
<select id="statusGroupSelect2" name="status_group2_display" class="form-select">
    <option value="">--เลือก--</option>
    <option value="ผู้นำเสนอทั่วไป <?php echo $a; ?> บาท">ผู้นำเสนอทั่วไป <?php echo $a; ?> บาท</option>
    <option value="ผู้นำเสนอสมาชิกเครือข่าย <?php echo $b; ?> บาท">ผู้นำเสนอสมาชิกเครือข่าย <?php echo $b; ?> บาท</option>
    <option value="นักศึกษาบัณฑิตระดับเครือข่าย <?php echo $c; ?> บาท">นักศึกษาบัณฑิตระดับเครือข่าย <?php echo $c; ?> บาท</option>
    <option value="ระดับนานาชาติ <?php echo $d; ?> บาท">ระดับนานาชาติ <?php echo $d; ?> บาท</option>
</select>
<span class="required"> *</span>


<div class="mb-3">
    <label for="status_group6" class="form-label">ช่องทางการชำระเงิน :</label>
    <select id="statusGroupSelect6" name="status_group6" class="form-select" required onchange="toggleOtherPayInput()">
        <option value="">--เลือก--</option>
        <option value="เงินสด">เงินสด</option>
        <option value="โอนผ่านบัญชีธนาคาร หรือ internet banking">โอนผ่านบัญชีธนาคาร หรือ internet banking</option>
        <option value="บัตรเครดิต บัตรเดบิต">บัตรเครดิต บัตรเดบิต</option>
        <option value="ช่องทางอื่น">ช่องทางอื่นๆ</option>
    </select>
    <span class="required"> *</span>
</div>

<!-- Input สำหรับกรอกช่องทางการชำระเงินที่ไม่ได้อยู่ในตัวเลือก -->
<div id="otherpay" style="display: none;">
    <label for="otherpayInput">กรอกช่องทางการชำระเงินอื่น:</label>
    <input type="text" id="otherpayInput" name="otherpay1" class="form-control" placeholder="กรอกช่องทางการชำระเงินอื่นที่นี่" required>
    <span class="required"> *</span>
</div>

<script>
    function toggleOtherPayInput() {
        const selectElement = document.getElementById("statusGroupSelect6");
        const otherPayDiv = document.getElementById("otherpay");
        const otherPayInput = document.getElementById("otherpayInput");

        // แสดงฟิลด์ otherPay เมื่อไม่ได้เลือกช่องทางการชำระเงินใดๆ
        if (selectElement.value === "ช่องทางอื่น") {
            otherPayDiv.style.display = "block";
            otherPayInput.setAttribute("required", true);
        } else {
            otherPayDiv.style.display = "none";
            otherPayInput.removeAttribute("required");
        }
    }
</script>




<!-- Hidden input สำหรับส่งค่า -->
       <input type="hidden" id="hiddenStatusGroup2" name="status_group2" value="">
      
       <div class="mb-3">
                <label for="file" class="form-label">แนบไฟล์ :</label>
                <input type="file" class="form-control" id="file1" name="slip_file" accept=".pdf,.jpg,.png" required>
                <label for="file" class="form-label">  <i  style="color:red"> หมายเหตุ สามารถแนบไฟล์ที่เป็น pdf,jpg,png เท่านั้น :  </i></label>
                <span class="required"> *</span>
            </div>
            <div class="mb-3">
            <input type="hidden" id="type_time" name="typy_time" value="<?php echo $typy_time; ?>">
            
                  <!-- ปุ่ม submit -->
                <button type="submit" class="btn submit-btn">ยืนยัน</button>
            </div>
    </div>
 </form>

        <?php  echo $vijai_parts; ?>
        <?php endif; ?>
    </div>

    <!-- รวม Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    

    <script>
    function toggleOtherInput() {
        var selectElement = document.getElementById("statusGroupSelect");
        var otherInput = document.getElementById("otherInstitution");

        // แสดงหรือซ่อน input สำหรับกรอก "อื่น ๆ"
        if (selectElement.value === "other") {
            otherInput.style.display = "block"; // แสดง input สำหรับกรอก "อื่น ๆ"
            otherInput.setAttribute("required", true); // เพิ่ม required ถ้าผู้ใช้ต้องกรอก

            // ถ้าใช่ "other" ให้เลือกค่าอัตโนมัติใน select อื่น ๆ
            var select1 = document.getElementById("statusGroupSelect1");
            var select2 = document.getElementById("statusGroupSelect2");
            var hiddenInput = document.getElementById("hiddenStatusGroup2");

            // ตัวเลือกที่เป็นไปได้ พร้อมค่าจาก PHP ที่จะเข้ามาแทน
            var options = [
                { value1: "ผู้นำเสนอทั่วไป", value2: "ผู้นำเสนอทั่วไป <?php echo $a; ?> บาท" },
                { value1: "นักศึกษาบัณฑิตระดับเครือข่าย", value2: "นักศึกษาบัณฑิตระดับเครือข่าย <?php echo $c; ?> บาท" },
                { value1: "ระดับนานาชาติ", value2: "ระดับนานาชาติ <?php echo $d; ?> บาท" }
            ];

            // ตัวอย่างเลือกตัวเลือกแรก
            select1.value = options[0].value1;
            select2.value = options[0].value2;

            // อัปเดตค่า hidden input
            hiddenInput.value = select2.value;

        } else {
            otherInput.style.display = "none"; // ซ่อน input สำหรับกรอก "อื่น ๆ"
            otherInput.removeAttribute("required"); // ลบ required เมื่อเลือกตัวเลือกอื่น ๆ

            // ถ้าไม่ใช่ "other" ให้ตั้งค่าเป็น "ผู้นำเสนอสมาชิกเครือข่าย"
            var select1 = document.getElementById("statusGroupSelect1");
            var select2 = document.getElementById("statusGroupSelect2");
            var hiddenInput = document.getElementById("hiddenStatusGroup2");

            select1.value = "ผู้นำเสนอสมาชิกเครือข่าย";
            select2.value = "ผู้นำเสนอสมาชิกเครือข่าย <?php echo $b; ?> บาท";

            // อัปเดตค่า hidden input สำหรับส่งไปกับฟอร์ม
            hiddenInput.value = select2.value;
        }
    }

    // ฟังก์ชันสำหรับส่งฟอร์ม
    function submitForm() {
        var selectElement = document.getElementById("statusGroupSelect");
        var otherInstitutionInput = document.getElementById("otherInstitutionInput");

        // ถ้าเลือก "other" ให้ใช้ค่าจาก input ที่กรอกแทน
        if (selectElement.value === "สถาบันที่ไมได้เป็นสมาชิก") {
            if (otherInstitutionInput.value.trim() === "") {
                alert("กรุณาระบุสถาบันอื่น ๆ");
                return false; // หยุดการส่งฟอร์มถ้ายังไม่ได้กรอก
            } else {
                // แทนที่ค่าของ select ด้วยค่าที่ผู้ใช้พิมพ์
                selectElement.value = otherInstitutionInput.value.trim();
            }
        }
        return true; // ส่งฟอร์มถ้าข้อมูลครบ
    }
</script>




<script>
    // จัดการข้อมูล articles เพื่อใช้งานใน JavaScript
    const articles = <?php echo json_encode($articles); ?>;

    // ฟังก์ชันสำหรับอัปเดต select ของรหัสบทความและ author_id โดยอัตโนมัติ
    function updatePublicationId() {
        const titleSelect = document.getElementById('statusGroupSelect5');
        const idSelect = document.getElementById('statusGroupSelect4');
        const authorSelect = document.getElementById('authorSelect');
        const selectedTitle = titleSelect.value;

        // ล้างตัวเลือกทั้งหมดใน select ของรหัสบทความและ author_id
        idSelect.innerHTML = '<option value="">--เลือก--</option>';
        authorSelect.innerHTML = '<option value="">--เลือก--</option>';

        // ค้นหาและตั้งค่าตัวเลือกที่ตรงกับชื่อบทความที่เลือก
        articles.forEach(article => {
            if (article.title === selectedTitle) {
                // ตั้งค่า select รหัสบทความ
                const optionId = document.createElement('option');
                optionId.value = article.publication_id;
                optionId.text = article.publication_id;
                idSelect.appendChild(optionId);
                idSelect.value = article.publication_id;

                // ตั้งค่า select author_id
                const optionAuthor = document.createElement('option');
                optionAuthor.value = article.author_id;
                optionAuthor.text = article.author_id;
                authorSelect.appendChild(optionAuthor);
                authorSelect.value = article.author_id;
            }
        });
    }
</script>

    


    <style>
        /* ซ่อนช่อง input ถ้าไม่ได้เลือก "อื่น ๆ" */
        #otherInstitution {
            display: none;
        }
    </style>
</body>
</html>

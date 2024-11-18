<?php
require_once './link/title.php';
require_once './config/db.php';
require_once './functions/Course/Course.php';
require_once './functions/Course/Enroll.php';
require_once './functions/lib/GenerateKey.php';
require_once './functions/lib/ConvertThaiDate.php';
require_once './functions/lib/Files.php';

global $conn;

$courseDetails = null;

if (isset($id) && $id != '') {
    $courseId = htmlspecialchars($id);
    $courseDetails = getCourseById($conn, $courseId);

    if ($courseDetails['courseLimit'] != 0) {
        if ($courseDetails['participants'] >= $courseDetails['courseLimit']) {
            header("Location: /index.php?page=course_detail&id={$courseDetails['id']}");
            exit();
        }
    }

    if (!$courseDetails) {
        header('Location: /');
        exit();
    }
} else {
    header('Location: /');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $courseKey = $courseDetails['courseKey'];
    $enrollCode = generateKey();
    $fullname = $_POST["fullname"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $institution = $_POST["institution"];

    $canEnroll = checkEnrollAttend($conn, $fullname, $courseKey);
    if ($canEnroll) {
        if ($courseDetails['courseOnline'] == 1 && $courseDetails['courseOnsite'] == 1) {
            $enrollType = isset($_POST['type-enroll']) ? $_POST['type-enroll'] : "-";
        } else if ($courseDetails['courseOnline'] == 1) {
            $enrollType = "ออนไลน์";
        } else if ($courseDetails['courseOnsite'] == 1) {
            $enrollType = "ออนไซต์";
        } else {
            $enrollType = "-";
        }
        $foodType = isset($_POST['type-food']) ? $_POST['type-food'] : "-";

        if ($foodType == 'อื่นๆ') {
            $foodTypeOther = $_POST["type-food-other"];
            $resultFoodType = $foodTypeOther;
        } else {
            $resultFoodType = $foodType;
        }


        $paymentProof = '-';
        if (isset($_FILES['paymentProof']) && $_FILES['paymentProof']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = "./uploads/payment-proof/{$courseKey}/";
            createFolder($uploadDir);
            $fileExtension = pathinfo($_FILES['paymentProof']['name'], PATHINFO_EXTENSION);

            $fileName = uniqid() . '.' . $fileExtension;

            $uploadFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['paymentProof']['tmp_name'], $uploadFile)) {
                $paymentProof = $fileName;
            }
        }

        $data = array(
            "courseKey" => $courseKey,
            "enrollCode" => $enrollCode,
            "fullname" => $fullname,
            "phone" => $phone,
            "email" => $email,
            "institution" => $institution,
            "enrollType" => $enrollType,
            "foodType" => $resultFoodType,
            "paymentProof" => $paymentProof,
        );

        $enroll = enrollCourse($conn, $data);

        if ($enroll) {
            $_SESSION["alert"] = '<script>
                Swal.fire({
                    title: "ลงทะเบียนหลักสูตรเรียบร้อย!",
                    text: "หากต้องการเปลี่ยนแปลงข้อมูล โปรดติดต่อผู้ดูแล",
                    icon: "success",
                    confirmButtonColor: "#5fe280",
                    confirmButtonText: "โอเค",
                })
            </script>';
            $_SESSION["enrollInfo"] = array(
                "fullname" => $fullname,
                "enrollCode" => $enrollCode,
            );
            header("Location: /index.php?page=course_detail&id={$courseDetails['id']}");
            exit();
        }
    } else {
        $_SESSION["alert"] = "<script>
            Swal.fire({
                title: \"ลงทะเบียนไม่สำเร็จ\",
                text: \"" . addslashes($fullname) . " ได้ลงทะเบียนหลักสูตรนี้ไปแล้ว\",
                icon: \"warning\",
                confirmButtonColor: \"#5fe280\",
                confirmButtonText: \"โอเค\"
            });
        </script>";
        header("Location: /index.php?page=course_enroll&id={$courseDetails['id']}");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if ($courseDetails) : ?>
        <?php title("สมัคร " . $courseDetails['courseTitle']); ?>
    <?php else : ?>
        <?php title('Course Not Found'); ?>
    <?php endif; ?>
    <?php require './link/styles.php' ?>
    <?php require './link/favicon.php' ?>
    <script src="/functions/form.js"></script>
    <script src="/functions/preview.js"></script>
    <script src="/lib/sweetalert2.all.min.js"></script>
</head>

<body>
    <?php require_once './layout/Navbar.php'; ?>
    <main>
        <?php
        if (isset($_SESSION['alert'])) {
            echo $_SESSION['alert'];
            unset($_SESSION['alert']);
        }
        ?>
        <div class="container px-4 md:px-12 mx-auto">
            <div class="shadow-lg p-4 md:p-10 border min-h-screen">
                <div>
                    <h1 class="text-2xl font-medium text-white text-center bg-cyan-400 p-4"><?php echo $courseDetails['courseTitle'] ?></h1>
                    <div class="mt-3 text-center">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-calendar-event">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                <path d="M16 3l0 4" />
                                <path d="M8 3l0 4" />
                                <path d="M4 11l16 0" />
                                <path d="M8 15h2v2h-2z" />
                            </svg>
                        </span>
                        <span class="font-medium">อบรมวันที่ : </span>
                        <span><?php echo convertGregorianDateToThai($courseDetails['courseStartEnrollDate']) ?> ถึง <?php echo convertGregorianDateToThai($courseDetails['courseEndEnrollDate']) ?></span>
                    </div>
                </div>
                <div class="md:p-10">
                    <div class="md:flex w-full items-center justify-center mb-3">
                        <div class="md:w-1/2">
                            <img class="object-fit" src="/public/courses/thumbnails/<?php echo $courseDetails['courseKey']; ?>/<?php echo $courseDetails['courseThumbnail']; ?>" alt="Course Image">
                        </div>
                        <div class="md:w-1/2 flex flex-col items-center justify-center my-8 md:my-0">
                            <div class="flex-none">
                                <div>
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-category-2">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M14 4h6v6h-6z" />
                                            <path d="M4 14h6v6h-6z" />
                                            <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                            <path d="M7 7m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                        </svg>
                                    </span>
                                    <span class="font-medium">หมวดหมู่ : </span>
                                    <span><?php echo $courseDetails['categoryTitle'] ?></span>
                                </div>
                                <div>
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-calendar-event">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                            <path d="M16 3l0 4" />
                                            <path d="M8 3l0 4" />
                                            <path d="M4 11l16 0" />
                                            <path d="M8 15h2v2h-2z" />
                                        </svg>
                                    </span>
                                    <span class="font-medium">ลงทะเบียนวัน : </span>
                                    <span><?php echo convertGregorianDateToThai($courseDetails['courseStartDate']) ?> ถึง <?php echo convertGregorianDateToThai($courseDetails['courseEndDate']) ?></span>
                                </div>
                                <div>
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-coins">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M9 14c0 1.657 2.686 3 6 3s6 -1.343 6 -3s-2.686 -3 -6 -3s-6 1.343 -6 3z" />
                                            <path d="M9 14v4c0 1.656 2.686 3 6 3s6 -1.344 6 -3v-4" />
                                            <path d="M3 6c0 1.072 1.144 2.062 3 2.598s4.144 .536 6 0c1.856 -.536 3 -1.526 3 -2.598c0 -1.072 -1.144 -2.062 -3 -2.598s-4.144 -.536 -6 0c-1.856 .536 -3 1.526 -3 2.598z" />
                                            <path d="M3 6v10c0 .888 .772 1.45 2 2" />
                                            <path d="M3 11c0 .888 .772 1.45 2 2" />
                                        </svg>
                                    </span>
                                    <span class="font-medium">ค่าลงทะเบียน : </span>
                                    <span>
                                        <?php
                                        if ($courseDetails['courseEnrollFee'] > 0) {
                                            echo $courseDetails['courseEnrollFee'] . " บาท";
                                        } else {
                                            echo "ฟรี";
                                        }
                                        ?>
                                    </span>
                                </div>
                                <div class="<?php echo $courseDetails['participants'] == $courseDetails['courseLimit'] && $courseDetails['courseLimit'] != 0 ? "text-red-600" : "" ?>">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-users">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                            <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                        </svg>
                                    </span>
                                    <span class="font-medium">รับจำนวน : </span>
                                    <?php if ($courseDetails['courseLimit'] != 0) {
                                        if ($courseDetails['participants'] <= $courseDetails['courseLimit']) : ?>
                                            <span><?php echo $courseDetails['participants'] ?>/<?php echo $courseDetails['courseLimit'] ?> คน</span>
                                        <?php endif;
                                    } else { ?>
                                        <span>ไม่จำกัดจำนวนคน</span>
                                    <?php } ?>
                                </div>
                                <div>
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-target-arrow">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                            <path d="M12 7a5 5 0 1 0 5 5" />
                                            <path d="M13 3.055a9 9 0 1 0 7.941 7.945" />
                                            <path d="M15 6v3h3l3 -3h-3v-3z" />
                                            <path d="M15 9l-3 3" />
                                        </svg>
                                    </span>
                                    <span class="font-medium">กลุ่มเป้าหมาย : </span>
                                    <span><?php echo $courseDetails['courseTarget'] ?></span>
                                </div>
                                <div>
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-map-pin">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                            <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                                        </svg>
                                    </span>
                                    <span class="font-medium">สถานที่ : </span>
                                    <span><?php echo $courseDetails['courseLocation'] ?></span>
                                </div>
                                <div>
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-wifi">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 18l.01 0" />
                                            <path d="M9.172 15.172a4 4 0 0 1 5.656 0" />
                                            <path d="M6.343 12.343a8 8 0 0 1 11.314 0" />
                                            <path d="M3.515 9.515c4.686 -4.687 12.284 -4.687 17 0" />
                                        </svg>
                                    </span>
                                    <span class="font-medium">เปิดออนไลน์ : </span>
                                    <span>
                                        <?php if ($courseDetails['courseOnline'] == 1) { ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500 inline-flex icon icon-tabler icons-tabler-outline icon-tabler-rosette-discount-check">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M5 7.2a2.2 2.2 0 0 1 2.2 -2.2h1a2.2 2.2 0 0 0 1.55 -.64l.7 -.7a2.2 2.2 0 0 1 3.12 0l.7 .7c.412 .41 .97 .64 1.55 .64h1a2.2 2.2 0 0 1 2.2 2.2v1c0 .58 .23 1.138 .64 1.55l.7 .7a2.2 2.2 0 0 1 0 3.12l-.7 .7a2.2 2.2 0 0 0 -.64 1.55v1a2.2 2.2 0 0 1 -2.2 2.2h-1a2.2 2.2 0 0 0 -1.55 .64l-.7 .7a2.2 2.2 0 0 1 -3.12 0l-.7 -.7a2.2 2.2 0 0 0 -1.55 -.64h-1a2.2 2.2 0 0 1 -2.2 -2.2v-1a2.2 2.2 0 0 0 -.64 -1.55l-.7 -.7a2.2 2.2 0 0 1 0 -3.12l.7 -.7a2.2 2.2 0 0 0 .64 -1.55v-1" />
                                                <path d="M9 12l2 2l4 -4" />
                                            </svg>
                                        <?php } else { ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-500 inline-flex icon icon-tabler icons-tabler-outline icon-tabler-circle-dashed-x">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95" />
                                                <path d="M3.69 8.56a9 9 0 0 0 -.69 3.44" />
                                                <path d="M3.69 15.44a9 9 0 0 0 1.95 2.92" />
                                                <path d="M8.56 20.31a9 9 0 0 0 3.44 .69" />
                                                <path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95" />
                                                <path d="M20.31 15.44a9 9 0 0 0 .69 -3.44" />
                                                <path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92" />
                                                <path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69" />
                                                <path d="M14 14l-4 -4" />
                                                <path d="M10 14l4 -4" />
                                            </svg>
                                        <?php } ?>
                                    </span>
                                </div>
                                <div>
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-building-estate">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M3 21h18" />
                                            <path d="M19 21v-4" />
                                            <path d="M19 17a2 2 0 0 0 2 -2v-2a2 2 0 1 0 -4 0v2a2 2 0 0 0 2 2z" />
                                            <path d="M14 21v-14a3 3 0 0 0 -3 -3h-4a3 3 0 0 0 -3 3v14" />
                                            <path d="M9 17v4" />
                                            <path d="M8 13h2" />
                                            <path d="M8 9h2" />
                                        </svg>
                                    </span>
                                    <span class="font-medium">เปิดออนไซต์ : </span>
                                    <span>
                                        <?php if ($courseDetails['courseOnsite'] == 1) { ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500 inline-flex icon icon-tabler icons-tabler-outline icon-tabler-rosette-discount-check">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M5 7.2a2.2 2.2 0 0 1 2.2 -2.2h1a2.2 2.2 0 0 0 1.55 -.64l.7 -.7a2.2 2.2 0 0 1 3.12 0l.7 .7c.412 .41 .97 .64 1.55 .64h1a2.2 2.2 0 0 1 2.2 2.2v1c0 .58 .23 1.138 .64 1.55l.7 .7a2.2 2.2 0 0 1 0 3.12l-.7 .7a2.2 2.2 0 0 0 -.64 1.55v1a2.2 2.2 0 0 1 -2.2 2.2h-1a2.2 2.2 0 0 0 -1.55 .64l-.7 .7a2.2 2.2 0 0 1 -3.12 0l-.7 -.7a2.2 2.2 0 0 0 -1.55 -.64h-1a2.2 2.2 0 0 1 -2.2 -2.2v-1a2.2 2.2 0 0 0 -.64 -1.55l-.7 -.7a2.2 2.2 0 0 1 0 -3.12l.7 -.7a2.2 2.2 0 0 0 .64 -1.55v-1" />
                                                <path d="M9 12l2 2l4 -4" />
                                            </svg>
                                        <?php } else { ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-500 inline-flex icon icon-tabler icons-tabler-outline icon-tabler-circle-dashed-x">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95" />
                                                <path d="M3.69 8.56a9 9 0 0 0 -.69 3.44" />
                                                <path d="M3.69 15.44a9 9 0 0 0 1.95 2.92" />
                                                <path d="M8.56 20.31a9 9 0 0 0 3.44 .69" />
                                                <path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95" />
                                                <path d="M20.31 15.44a9 9 0 0 0 .69 -3.44" />
                                                <path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92" />
                                                <path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69" />
                                                <path d="M14 14l-4 -4" />
                                                <path d="M10 14l4 -4" />
                                            </svg>
                                        <?php } ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <form class="mx-auto py-10" id="enrollForm" action="/?page=course_enroll&id=<?php echo $courseDetails['id'] ?>" method="POST" enctype="multipart/form-data">
                        <h1 class="mb-3 text-2xl font-semibold">แบบฟอร์มการลงทะเบียน</h1>
                        <div class="mb-5">
                            <label for="fullname" class="block mb-2 text-sm font-medium">ชื่อ - นามสกุล</label>
                            <input type="text" id="fullname" name="fullname" class="transition-all bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="ชื่อ - นามสกุล" onkeydown="clearBorder(this)" required />
                        </div>
                        <div class="mb-5">
                            <label for="institution" class="block mb-2 text-sm font-medium">หน่วยงาน</label>
                            <input type="text" id="institution" name="institution" class="transition-all bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="หน่วยงาน" onkeydown="clearBorder(this)" required />
                        </div>
                        <div class="mb-5">
                            <label for="phone" class="block mb-2 text-sm font-medium">เบอร์โทรศัพท์</label>
                            <input type="number" id="phone" name="phone" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="0123456789" onkeydown="clearBorder(this)" required />
                        </div>
                        <div class="mb-5">
                            <label for="email" class="block mb-2 text-sm font-medium">อีเมล</label>
                            <input type="email" id="email" name="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="name@metta.go.th" onkeydown="clearBorder(this)" required />
                        </div>
                        <?php if ($courseDetails['courseFoodQuestion'] == 1) : ?>
                            <div class="mb-5">
                                <h3 class="mb-4 font-semibold text-gray-900 dark:text-white">ประสงค์จะรับประทานอาหาร</h3>
                                <ul id="type-food-form" class="items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex">
                                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r">
                                        <div class="flex items-center ps-3">
                                            <input id="horizontal-type-food-normal" type="radio" value="ธรรมดา" name="type-food" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500" onclick="clearBorderCheckedFood()">
                                            <label for="horizontal-type-food-normal" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">ธรรมดา</label>
                                        </div>
                                    </li>
                                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r">
                                        <div class="flex items-center ps-3">
                                            <input id="horizontal-type-food-vegetarian" type="radio" value="มังสวิรัติ" name="type-food" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500" onclick="clearBorderCheckedFood()">
                                            <label for="horizontal-type-food-vegetarian" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">มังสวิรัติ</label>
                                        </div>
                                    </li>
                                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r">
                                        <div class="flex items-center ps-3">
                                            <input id="horizontal-type-food-islamic" type="radio" value="อิสลาม" name="type-food" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500" onclick="clearBorderCheckedFood()">
                                            <label for="horizontal-type-food-islamic" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">อิสลาม</label>
                                        </div>
                                    </li>
                                    <li class="w-full">
                                        <div class="flex items-center ps-3">
                                            <input id="horizontal-type-food-other" type="radio" value="อื่นๆ" name="type-food" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500" onclick="clearBorderCheckedFood()">
                                            <label for="horizontal-type-food-other" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">อื่น ๆ ระบุ</label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="mb-5" id="type-food-other-form">
                                <label for="type-food-other-input" class="block mb-2 text-sm font-medium">อื่น ๆ</label>
                                <input type="text" id="type-food-other-input" name="type-food-other" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="อาหารประเภทอื่น ๆ" onkeydown="clearBorder(this)" required />
                            </div>
                        <?php endif; ?>
                        <?php if ($courseDetails['courseOnline'] == 1 && $courseDetails['courseOnsite'] == 1) : ?>
                            <div class="mb-5">
                                <h3 class="mb-4 font-semibold text-gray-900">เข้าอบรม</h3>
                                <ul id="type-enroll-form" class="items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex">
                                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r">
                                        <div class="flex items-center ps-3">
                                            <input id="horizontal-type-enroll-vegetarian" type="radio" value="ออนไซต์" name="type-enroll" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500" onclick="clearBorderCheckedEnroll()">
                                            <label for="horizontal-type-enroll-vegetarian" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Onsite | ออนไซต์</label>
                                        </div>
                                    </li>
                                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r">
                                        <div class="flex items-center ps-3">
                                            <input id="horizontal-type-enroll-normal" type="radio" value="ออนไลน์" name="type-enroll" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500" onclick="clearBorderCheckedEnroll()">
                                            <label for="horizontal-type-enroll-normal" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Online | ออนไลน์</label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <?php if ($courseDetails['courseEnrollFee'] > 0) : ?>
                            <div class="mb-5">
                                <h3 for="dropzone-file" class="mb-4 font-semibold text-gray-900">หลักฐานการโอนเงินค่าลงทะเบียน</h3>
                                <div class="flex items-center justify-center w-full">
                                    <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                            </svg>
                                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">คลิกเพื่ออัปโหลด</span> หรือ ลาก วาง</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG หรือ JPEG</p>
                                        </div>
                                        <input id="dropzone-file" name="paymentProof" type="file" class="hidden" accept="image/png, image/jpeg, image/jpg" onchange="previewPaymentProof(event)" />
                                    </label>
                                </div>
                                <div class="flex justify-center">
                                    <div class="max-w-[350px]">
                                        <div id="paymentProof-preview" class="border border-dashed border-cyan-300 rounded-lg object-cover mt-4"></div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <button type="button" onclick="enroll()" class="transition-all w-full text-white bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">สมัครเลย</button>
                    </form>

                </div>
            </div>
        </div>
    </main>
</body>

</html>
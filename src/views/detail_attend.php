<?php
require_once './link/title.php';
require_once './functions/Course/Course.php';
require_once './functions/Course/AttendsList.php';
global $conn;

if (isset($courseId) && $courseId != '') {
    $courseId = htmlspecialchars($courseId);
    $courseDetail = getCourseById($conn, $courseId);
    if (!$courseDetail) {
        header('Location: /');
        exit();
    }
    $courseKey = $courseDetail['courseKey'];
}

if (isset($attendId) && $attendId != '') {
    $attendId = htmlspecialchars($attendId);
    $attendDetail = getAttendDetail($conn, $courseKey, $attendId)[0];
    $enrollCode = $attendDetail['enrollCode'];
    if (!$attendDetail) {
        header('Location: /');
        exit();
    }
}

if ($attendDetail['statusPayment'] == 1) {
    $statusPaymentText = "ยืนยันการชำระเงินแล้ว";
    $statusPaymentBg = "bg-green-300";
    $showButtonConfirm = false;
} else {
    $statusPaymentText = "ยังไม่ยืนยันการชำระเงิน";
    $statusPaymentBg = "bg-red-300";
    $showButtonConfirm = true;
};

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['clearSessionUpdateAttend'])) {
    unset($_SESSION['updateAttendInfo']);
    header("Location: /?page=course_attend_detail&courseId={$courseId}&attendId={$attendId}");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && !isset($_POST['clearSessionUpdateAttend'])) {
    $fullname = $_POST["fullname"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $institution = $_POST["institution"];
    if ($courseDetail['courseOnline'] == 1 && $courseDetail['courseOnsite'] == 1) {
        $enrollType = isset($_POST['type-enroll']) ? $_POST['type-enroll'] : "-";
    } else if ($courseDetail['courseOnline'] == 1) {
        $enrollType = $courseDetail['courseOnline'];
    } else if ($courseDetail['courseOnsite'] == 1) {
        $enrollType = $courseDetail['courseOnsite'];
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

    $data = array(
        "fullname" => $fullname,
        "phone" => $phone,
        "email" => $email,
        "institution" => $institution,
        "enrollType" => $enrollType,
        "foodType" => $resultFoodType,
    );
    $updateAttend = updateAttendDetail($conn, $courseKey, $enrollCode, $data);
    if ($updateAttend) {
        $_SESSION["alert"] = '<script>
                Swal.fire({
                    title: "แก้ไขข้อมูลผู้ลงทะเบียนเรียบร้อย!",
                    text: "สามารถแก้ไขข้อมูลได้ตลอด",
                    icon: "success",
                    confirmButtonColor: "#5fe280",
                    confirmButtonText: "โอเค",
                })
            </script>';
        $_SESSION["updateAttendInfo"] = array(
            "fullname" => $fullname
        );
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    };
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if ($attendDetail) : ?>
        <?php title($attendDetail['fullname']); ?>
    <?php else : ?>
        <?php title('Attend Not Found'); ?>
    <?php endif; ?>
    <?php require './link/styles.php' ?>
    <?php require './link/favicon.php' ?>
    <script src="/lib/sweetalert2.all.min.js"></script>
    <script src="/functions/Course/AttendsList.php"></script>
    <script src="/functions/form.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php
    if (isset($_SESSION['alert'])) {
        echo $_SESSION['alert'];
        unset($_SESSION['alert']);
    }
    ?>
    <?php require_once './layout/Navbar.php'; ?>
    <main>
        <div class="container px-4 md:px-12 mx-auto">
            <div class="shadow-lg p-4 md:p-10 border min-h-screen">
                <?php if (isset($_SESSION['updateAttendInfo'])) : ?>
                    <div class="relative bg-red-400 text-white font-medium rounded-lg py-4 my-3">
                        <div class="absolute left-4 top-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="inline-flex icon icon-tabler icons-tabler-filled icon-tabler-bell">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14.235 19c.865 0 1.322 1.024 .745 1.668a3.992 3.992 0 0 1 -2.98 1.332a3.992 3.992 0 0 1 -2.98 -1.332c-.552 -.616 -.158 -1.579 .634 -1.661l.11 -.006h4.471z" />
                                <path d="M12 2c1.358 0 2.506 .903 2.875 2.141l.046 .171l.008 .043a8.013 8.013 0 0 1 4.024 6.069l.028 .287l.019 .289v2.931l.021 .136a3 3 0 0 0 1.143 1.847l.167 .117l.162 .099c.86 .487 .56 1.766 -.377 1.864l-.116 .006h-16c-1.028 0 -1.387 -1.364 -.493 -1.87a3 3 0 0 0 1.472 -2.063l.021 -.143l.001 -2.97a8 8 0 0 1 3.821 -6.454l.248 -.146l.01 -.043a3.003 3.003 0 0 1 2.562 -2.29l.182 -.017l.176 -.004z" />
                            </svg>
                        </div>
                        <div class="text-end mr-3 md:mr-0">
                            <form method="POST" action="">
                                <button type="submit" name="clearSessionUpdateAttend" class="transition-all md:absolute right-2 top-2 bg-cyan-300 hover:bg-cyan-500 text-black font-bold p-2 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-x">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M18 6l-12 12" />
                                        <path d="M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                        <div class="px-4 md:text-center">
                            <p>
                                แก้ไขข้อมูลผู้ลงทะเบียน "<?php echo $_SESSION['updateAttendInfo']['fullname'] ?>" เรียบร้อย
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="flex md:flex-row flex-col text-sm md:text-2xl font-medium text-black">
                    <div>
                        <span>รายละเอียด</span>
                    </div>
                    <div>
                        <span><?php echo $attendDetail['fullname'] ?></span>
                    </div>
                </div>
                <p>ลงทะเบียนในหลักสูตร "<?php echo $courseDetail['courseTitle'] ?>"</p>
                <p>ลงทะเบียนเวลา "<?php
                                    date_default_timezone_set('Asia/Bangkok');
                                    $enrolTime = strtotime($attendDetail['enrollTime']);
                                    $formattedDate = date("d/m/Y H:i:s", strtotime('+543 year', $enrolTime));
                                    echo $formattedDate;
                                    ?>"</p>
                <?php if ($courseDetail['courseEnrollFee'] != 0) : ?>
                    <div class="w-full">
                        <div class="flex flex-col md:flex-row md:gap-x-10 items-center justify-center">
                            <img class="object-fit max-h-[300px]" src="/uploads/payment-proof/<?php echo $courseDetail['courseKey'] ?>/<?php echo $attendDetail['paymentProof'] ?>" alt="" srcset="">
                            <div class="sm:mb-10 text-center">
                                <div class="font-medium sm:mb-3">หลักฐานการโอนเงิน</div>
                                <div class="px-0.5 md:p-2 my-5 <?php echo $statusPaymentBg; ?>">
                                    <?php echo $statusPaymentText; ?>
                                </div>
                                <?php if ($showButtonConfirm == true) : ?>
                                    <a href="/?page=course_attend_verify&courseId=<?php echo $courseId ?>&courseKey=<?php echo $courseKey ?>&enrollCode=<?php echo $attendDetail['enrollCode'] ?>" class="transition-all text-white bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">ยืนยันการการชำระเงิน</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <hr>
                <div class="mt-5">
                    <form action="" method="POST">
                        <div class="mb-5">
                            <label for="fullname" class="block mb-2 text-sm font-medium text-gray-900">ชื่อ - นามสกุล</label>
                            <input type="text" id="fullname" name="fullname" value="<?php echo $attendDetail['fullname'] ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="อบรมเรื่อง..." required />
                        </div>
                        <div class="mb-5">
                            <label for="institution" class="block mb-2 text-sm font-medium">หน่วยงาน</label>
                            <input type="text" id="institution" name="institution" value="<?php echo $attendDetail['institution'] ?>" class="transition-all bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="หน่วยงาน" onkeydown="clearBorder(this)" required />
                        </div>
                        <div class="mb-5">
                            <label for="phone" class="block mb-2 text-sm font-medium">เบอร์โทรศัพท์</label>
                            <input type="number" id="phone" name="phone" value="<?php echo $attendDetail['phone'] ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="0123456789" onkeydown="clearBorder(this)" required />
                        </div>
                        <div class="mb-5">
                            <label for="email" class="block mb-2 text-sm font-medium">อีเมล</label>
                            <input type="email" id="email" name="email" value="<?php echo $attendDetail['email'] ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="name@metta.go.th" onkeydown="clearBorder(this)" required />
                        </div>
                        <?php if ($courseDetail['courseFoodQuestion'] == 1) : ?>
                            <div class="mb-5">
                                <h3 class="mb-4 font-semibold text-gray-900 dark:text-white">ประสงค์จะรับประทานอาหาร</h3>
                                <ul id="type-food-form" class="items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex">
                                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r">
                                        <div class="flex items-center ps-3">
                                            <input id="horizontal-type-food-normal" type="radio" <?php echo ($attendDetail['foodType'] == "ธรรมดา") ? "checked" : "" ?> value="ธรรมดา" name="type-food" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500" onclick="clearBorderCheckedFood()">
                                            <label for="horizontal-type-food-normal" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">ธรรมดา</label>
                                        </div>
                                    </li>
                                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r">
                                        <div class="flex items-center ps-3">
                                            <input id="horizontal-type-food-vegetarian" type="radio" <?php echo ($attendDetail['foodType'] == "มังสวิรัติ") ? "checked" : "" ?> value="มังสวิรัติ" name="type-food" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500" onclick="clearBorderCheckedFood()">
                                            <label for="horizontal-type-food-vegetarian" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">มังสวิรัติ</label>
                                        </div>
                                    </li>
                                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r">
                                        <div class="flex items-center ps-3">
                                            <input id="horizontal-type-food-islamic" type="radio" <?php echo ($attendDetail['foodType'] == "อิสลาม") ? "checked" : "" ?> value="อิสลาม" name="type-food" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500" onclick="clearBorderCheckedFood()">
                                            <label for="horizontal-type-food-islamic" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">อิสลาม</label>
                                        </div>
                                    </li>
                                    <li class="w-full">
                                        <div class="flex items-center ps-3">
                                            <input id="horizontal-type-food-other" type="radio" <?php
                                                                                                if ($attendDetail['foodType'] != "-")
                                                                                                    echo ($attendDetail['foodType'] != "ธรรมดา"
                                                                                                        && $attendDetail['foodType'] != "มังสวิรัติ"
                                                                                                        && $attendDetail['foodType'] != "อิสลาม")
                                                                                                        ? "checked"
                                                                                                        : ""
                                                                                                ?> value="อื่นๆ" name="type-food" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500" onclick="clearBorderCheckedFood()">
                                            <label for="horizontal-type-food-other" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">อื่น ๆ ระบุ</label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="mb-5" id="type-food-other-form">
                                <label for="type-food-other-input" class="block mb-2 text-sm font-medium">อื่น ๆ</label>
                                <input type="text" id="type-food-other-input" value="<?php
                                                                                        if ($attendDetail['foodType'] != "-")
                                                                                            echo ($attendDetail['foodType'] != "ธรรมดา"
                                                                                                && $attendDetail['foodType'] != "มังสวิรัติ"
                                                                                                && $attendDetail['foodType'] != "อิสลาม")
                                                                                                ? $attendDetail['foodType']
                                                                                                : ""
                                                                                        ?>" name="type-food-other" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="อาหารประเภทอื่น ๆ" onkeydown="clearBorder(this)" />
                            </div>
                        <?php endif; ?>
                        <?php if ($courseDetail['courseOnline'] == 1 && $courseDetail['courseOnsite'] == 1) : ?>
                            <div class="mb-5">
                                <h3 class="mb-4 font-semibold text-gray-900">เข้าอบรม</h3>
                                <ul id="type-enroll-form" class="items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex">
                                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r">
                                        <div class="flex items-center ps-3">
                                            <input id="horizontal-type-enroll-vegetarian" type="radio" <?php echo ($attendDetail['enrollType'] == "ออนไซต์") ? "checked" : "" ?> value="ออนไซต์" name="type-enroll" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500" onclick="clearBorderCheckedEnroll()">
                                            <label for="horizontal-type-enroll-vegetarian" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Onsite | ออนไซต์</label>
                                        </div>
                                    </li>
                                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r">
                                        <div class="flex items-center ps-3">
                                            <input id="horizontal-type-enroll-normal" type="radio" <?php echo ($attendDetail['enrollType'] == "ออนไลน์") ? "checked" : "" ?> value="ออนไลน์" name="type-enroll" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500" onclick="clearBorderCheckedEnroll()">
                                            <label for="horizontal-type-enroll-normal" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Online | ออนไลน์</label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <button type="submit" class="transition-all duration-300 ease-in-out w-full text-white bg-purple-500 hover:bg-purple-600 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">แก้ไขข้อมูลการลงทะเบียน</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
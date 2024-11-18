<?php
require_once './link/title.php';
require_once './config/db.php';
require_once './functions/Course/Course.php';
require_once './functions/lib/ConvertThaiDate.php';

global $conn;
if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
    $user = getUser($conn, $userId);
    $canPost = $user['canPostCourse'];
    $manageUser = $user['canManageUser'];
}
$courseDetails = null;
if (isset($id) && $id != '') {
    $courseId = htmlspecialchars($id);
    $courseDetails = getCourseById($conn, $courseId);

    if (!$courseDetails) {
        header('Location: /');
        exit();
    } else if (!isset($_SESSION['userId']) && $courseDetails['courseOpen'] == 0) {
        header('Location: /');
        exit();
    }
} else {
    header('Location: /');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['clearSession'])) {
    unset($_SESSION['enrollInfo']);
    header("Location: /index.php?page=course_detail&id={$courseDetails['id']}");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['clearSessionUpdate'])) {
    unset($_SESSION['updateCourseInfo']);
    header("Location: /index.php?page=course_detail&id={$courseDetails['id']}");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['fileTitle'])) {
    $fileTitle = isset($_POST['fileTitle']) ? $_POST['fileTitle'] : "-";
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = "./public/courses/files/{$courseDetails['courseKey']}/";
        createFolder($uploadDir);
        $allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/zip',
            'application/x-rar-compressed',
        ];

        if (in_array($_FILES['file']['type'], $allowedTypes)) {
            $fileExtension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $fileExtension;
            $uploadFile = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
                $file = $fileName;
                $size = $_FILES['file']['size'];
                $fileType = $fileExtension;
            }
        } else {
            $_SESSION["uploadFile"] = '<script>
             Swal.fire({
                 title: "อัพโหลดไฟล์ ' . htmlspecialchars('"' . $fileTitle . '"', ENT_QUOTES, 'UTF-8') . ' ไม่สำเร็จ",
                 text: "กรุณาอัปโหลด แค่นามสกุลไฟล์ที่กำหนดเท่านั้น",
                 icon: "warning",
                 confirmButtonColor: "#d33",
                 confirmButtonText: "ลองใหม่",
             })
            </script>';
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }

        $file = array(
            "fileName" => $file,
            "fileTitle" => $fileTitle,
            "fileType" => $fileType,
            "fileSize" => $size,
        );
        $errorMessage = match ($_FILES['file']['error']) {
            UPLOAD_ERR_INI_SIZE => "ไฟล์มีขนาดใหญ่เกินกว่าที่กำหนดในการตั้งค่า PHP",
            UPLOAD_ERR_FORM_SIZE => "ไฟล์มีขนาดใหญ่เกินกว่าที่กำหนดในฟอร์ม HTML",
            UPLOAD_ERR_PARTIAL => "ไฟล์ถูกอัปโหลดเพียงบางส่วน",
            UPLOAD_ERR_NO_FILE => "ไม่มีไฟล์ถูกอัปโหลด",
            UPLOAD_ERR_NO_TMP_DIR => "ไม่พบโฟลเดอร์ชั่วคราว",
            UPLOAD_ERR_CANT_WRITE => "ไม่สามารถเขียนไฟล์ลงดิสก์ได้",
            UPLOAD_ERR_EXTENSION => "การอัปโหลดไฟล์ถูกหยุดโดยส่วนขยาย PHP",
            default => "เกิดข้อผิดพลาดที่ไม่รู้จักในการอัปโหลดไฟล์",
        };
        if (newCourseFile($conn, $courseDetails['courseKey'], $file)) {
            $_SESSION["uploadFile"] = '<script>
                Swal.fire({
                    title: "อัพโหลดไฟล์ ' . htmlspecialchars($fileTitle, ENT_QUOTES, 'UTF-8') . ' เรียบร้อย",
                    text: "สามารถอัปโหลดเพิ่ม หรือ แก้ไขภายหลังได้",
                    icon: "success",
                    confirmButtonColor: "#5fe280",
                    confirmButtonText: "โอเค",
                })
                </script>';
        } else {
            $_SESSION["uploadFile"] = "<script>
                Swal.fire({
                    title: 'อัพโหลดไฟล์ " . htmlspecialchars($fileTitle, ENT_QUOTES, 'UTF-8') . " ไม่สำเร็จ',
                    text: '$errorMessage',
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'ลองใหม่',
                })
            </script>";
        }
    } else {
        $_SESSION["uploadFile"] = "<script>
            Swal.fire({
                title: 'อัพโหลดไฟล์ " . htmlspecialchars($fileTitle, ENT_QUOTES, 'UTF-8') . " ไม่สำเร็จ',
                text: '$errorMessage',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'ลองใหม่',
            })
        </script>";
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if ($courseDetails) : ?>
        <?php title($courseDetails['courseTitle']); ?>
    <?php else : ?>
        <?php title('Course Not Found'); ?>
    <?php endif; ?>
    <?php require './link/styles.php' ?>
    <?php require './link/favicon.php' ?>
    <script src="/lib/sweetalert2.all.min.js"></script>
    <script src="/functions/Course/course.js"></script>
</head>

<body>
    <?php require_once './layout/Navbar.php'; ?>
    <?php
    if (isset($_SESSION['uploadFile'])) {
        echo $_SESSION['uploadFile'];
        unset($_SESSION['uploadFile']);
    }
    if (isset($_SESSION['alert'])) {
        echo $_SESSION['alert'];
        unset($_SESSION['alert']);
    }
    ?>
    <main>
        <div class="container px-4 md:px-12 mx-auto">
            <div class="shadow-lg md:p-10 border min-h-screen">
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
                <div class="md:p-10 p-4">
                    <?php if ($courseDetails) : ?>
                        <div class="md:flex w-full items-center justify-center mb-3">
                            <img class="md:w-1/2 object-fit" src="/public/courses/thumbnails/<?php echo $courseDetails['courseKey'] ?>/<?php echo $courseDetails['courseThumbnail']; ?>" alt="Course Image">
                            <div class="md:w-1/2 flex flex-col items-center justify-center">
                                <a href="#detail" class="transition-all md:w-1/2 w-full p-4 bg-cyan-300 hover:bg-cyan-400 text-center font-bold my-3 rounded-lg">อ่านรายละเอียด</a>
                                <?php if ($courseDetails['courseLimit'] != 0) {
                                    if ($courseDetails['participants'] < $courseDetails['courseLimit']) : ?>
                                        <a href="/?page=course_enroll&id=<?php echo $courseDetails['id'] ?>" class="transition-all md:w-1/2 w-full p-4 bg-pink-300 hover:bg-pink-400 text-center font-bold my-3 rounded-lg">สมัครเลย</a>
                                    <?php endif;
                                } else { ?>
                                    <a href="/?page=course_enroll&id=<?php echo $courseDetails['id'] ?>" class="transition-all md:w-1/2 w-full p-4 bg-pink-300 hover:bg-pink-400 text-center font-bold my-3 rounded-lg">สมัครเลย</a>
                                <?php } ?>
                                <a href="/" class="transition-all md:w-1/2 w-full p-4 bg-purple-300 hover:bg-purple-400 text-center font-bold my-3 rounded-lg">กลับหน้าหลัก</a>
                            </div>
                        </div>
                        <?php if (isset($_SESSION['userId']) && $canPost == 1) : ?>
                            <div class="flex justify-end items-center mb-5">
                                <div class="cursor-pointer font-semibold text-green-400 hover:text-green-500 transition-all p-2 rounded-lg">
                                    <a href="/?page=course_edit&id=<?php echo $courseDetails['id'] ?>">
                                        แก้ไข
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="inline-flex icon icon-tabler icons-tabler-filled icon-tabler-settings">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M14.647 4.081a.724 .724 0 0 0 1.08 .448c2.439 -1.485 5.23 1.305 3.745 3.744a.724 .724 0 0 0 .447 1.08c2.775 .673 2.775 4.62 0 5.294a.724 .724 0 0 0 -.448 1.08c1.485 2.439 -1.305 5.23 -3.744 3.745a.724 .724 0 0 0 -1.08 .447c-.673 2.775 -4.62 2.775 -5.294 0a.724 .724 0 0 0 -1.08 -.448c-2.439 1.485 -5.23 -1.305 -3.745 -3.744a.724 .724 0 0 0 -.447 -1.08c-2.775 -.673 -2.775 -4.62 0 -5.294a.724 .724 0 0 0 .448 -1.08c-1.485 -2.439 1.305 -5.23 3.744 -3.745a.722 .722 0 0 0 1.08 -.447c.673 -2.775 4.62 -2.775 5.294 0zm-2.647 4.919a3 3 0 1 0 0 6a3 3 0 0 0 0 -6z" />
                                        </svg>
                                    </a>
                                </div>
                                <div class="cursor-pointer font-semibold text-red-400 hover:text-red-500 transition-all p-2 rounded-lg">
                                    <a onclick="removeCourse('/?page=course_remove&key=<?php echo $courseDetails['courseKey'] ?>')">
                                        ลบ
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="inline-flex icon icon-tabler icons-tabler-filled icon-tabler-trash-x">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M20 6a1 1 0 0 1 .117 1.993l-.117 .007h-.081l-.919 11a3 3 0 0 1 -2.824 2.995l-.176 .005h-8c-1.598 0 -2.904 -1.249 -2.992 -2.75l-.005 -.167l-.923 -11.083h-.08a1 1 0 0 1 -.117 -1.993l.117 -.007h16zm-9.489 5.14a1 1 0 0 0 -1.218 1.567l1.292 1.293l-1.292 1.293l-.083 .094a1 1 0 0 0 1.497 1.32l1.293 -1.292l1.293 1.292l.094 .083a1 1 0 0 0 1.32 -1.497l-1.292 -1.293l1.292 -1.293l.083 -.094a1 1 0 0 0 -1.497 -1.32l-1.293 1.292l-1.293 -1.292l-.094 -.083z" />
                                            <path d="M14 2a2 2 0 0 1 2 2a1 1 0 0 1 -1.993 .117l-.007 -.117h-4l-.007 .117a1 1 0 0 1 -1.993 -.117a2 2 0 0 1 1.85 -1.995l.15 -.005h4z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                        <hr>
                        <div class="my-5" id="detail">
                            <?php if (isset($_SESSION['updateCourseInfo'])) : ?>
                                <div class="relative bg-green-400 text-white font-medium rounded-lg py-4 my-3">
                                    <div class="absolute left-4 top-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="inline-flex icon icon-tabler icons-tabler-filled icon-tabler-bell">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M14.235 19c.865 0 1.322 1.024 .745 1.668a3.992 3.992 0 0 1 -2.98 1.332a3.992 3.992 0 0 1 -2.98 -1.332c-.552 -.616 -.158 -1.579 .634 -1.661l.11 -.006h4.471z" />
                                            <path d="M12 2c1.358 0 2.506 .903 2.875 2.141l.046 .171l.008 .043a8.013 8.013 0 0 1 4.024 6.069l.028 .287l.019 .289v2.931l.021 .136a3 3 0 0 0 1.143 1.847l.167 .117l.162 .099c.86 .487 .56 1.766 -.377 1.864l-.116 .006h-16c-1.028 0 -1.387 -1.364 -.493 -1.87a3 3 0 0 0 1.472 -2.063l.021 -.143l.001 -2.97a8 8 0 0 1 3.821 -6.454l.248 -.146l.01 -.043a3.003 3.003 0 0 1 2.562 -2.29l.182 -.017l.176 -.004z" />
                                        </svg>
                                    </div>
                                    <div class="text-end mr-3 md:mr-0">
                                        <form method="POST" action="">
                                            <button type="submit" name="clearSessionUpdate" class="transition-all md:absolute right-2 top-2 bg-cyan-300 hover:bg-cyan-500 text-black font-bold p-2 rounded">
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
                                            แก้ไขข้อมูลหลักสูตร "<?php echo $_SESSION['updateCourseInfo']['courseTitle'] ?>" เรียบร้อย
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['enrollInfo'])) : ?>
                                <div class="relative bg-green-400 text-white font-medium rounded-lg py-2 my-3">
                                    <div class="absolute left-4 top-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="inline-flex icon icon-tabler icons-tabler-filled icon-tabler-bell">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M14.235 19c.865 0 1.322 1.024 .745 1.668a3.992 3.992 0 0 1 -2.98 1.332a3.992 3.992 0 0 1 -2.98 -1.332c-.552 -.616 -.158 -1.579 .634 -1.661l.11 -.006h4.471z" />
                                            <path d="M12 2c1.358 0 2.506 .903 2.875 2.141l.046 .171l.008 .043a8.013 8.013 0 0 1 4.024 6.069l.028 .287l.019 .289v2.931l.021 .136a3 3 0 0 0 1.143 1.847l.167 .117l.162 .099c.86 .487 .56 1.766 -.377 1.864l-.116 .006h-16c-1.028 0 -1.387 -1.364 -.493 -1.87a3 3 0 0 0 1.472 -2.063l.021 -.143l.001 -2.97a8 8 0 0 1 3.821 -6.454l.248 -.146l.01 -.043a3.003 3.003 0 0 1 2.562 -2.29l.182 -.017l.176 -.004z" />
                                        </svg>
                                    </div>
                                    <div class="text-end mr-3 md:mr-0">
                                        <form method="POST" action="">
                                            <button type="submit" name="clearSession" class="transition-all md:absolute right-2 top-2 bg-cyan-300 hover:bg-cyan-500 text-black font-bold p-2 rounded">
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
                                            <?php echo $_SESSION['enrollInfo']['fullname'] ?> ได้ลงทะเบียน "<?php echo $courseDetails['courseTitle'] ?>" เรียบร้อย
                                        </p>
                                        <p>
                                            รหัสการลงทะเบียนของคุณ : <?php echo $_SESSION['enrollInfo']['enrollCode'] ?>
                                        </p>
                                        <p>
                                            หากมีปัญหาสามารถติดต่อโรงพยาบาลได้เลย
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['userId'])) : ?>
                                <div class="flex flex-col my-6">
                                    <h1 class="text-xl mb-5 font-medium">รายชื่อผู้ลงทะเบียน</h1>
                                    <?php require_once './components/Course/CourseAttend.php' ?>
                                    <div class="flex justify-between items-center mt-6">
                                        <?php if ($current_page > 1) : ?>
                                            <a href="/?page=course_detail&id=<?php echo$courseDetails['id']?>&page_row=<?php echo $current_page - 1; ?>" class="btn btn-primary">ย้อนกลับ</a>
                                        <?php else : ?>
                                            <span class="text-gray-500">ย้อนกลับ</span>
                                        <?php endif; ?>
                                        <span>หน้า <?php echo $current_page; ?> จาก <?php echo $total_pages; ?></span>
                                        <?php if ($current_page < $total_pages) : ?>
                                            <a href="/?page=course_detail&id=<?php echo$courseDetails['id']?>&page_row=<?php echo $current_page + 1; ?>" class="btn btn-primary">ถัดไป</a>
                                        <?php else : ?>
                                            <span class="text-gray-500">ถัดไป</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h1 class="text-xl mb-5 font-medium">รายละเอียดหลักสูตร</h1>
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
                            <div class="mt-3 flex flex-wrap items-center justify-center">
                                <?php foreach ($courseDetails['courseImages'] as $courseImage) : ?>
                                    <div class="w-full md:w-1/2 p-1">
                                        <div class="transition-all duration-300 ease-in-out hover:drop-shadow-lg relative overflow-hidden rounded-lg">
                                            <img class="transition-transform duration-300 ease-in-out transform hover:scale-[1.05] w-full h-full object-cover border border-[#DDDDDD]" src="/public/courses/images/<?php echo $courseDetails['courseKey'] ?>/<?php echo $courseImage; ?>" alt="Course Image" srcset="">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="my-5" id="fileZone">
                            <h1 class="text-xl mb-5 font-medium">เอกสารประกอบหลักสูตร</h1>
                            <div class="border rounded-lg">
                                <div class="flex flex-col">
                                    <?php if (isset($_SESSION['userId'])) : ?>
                                        <div class="border-b flex gap-2 md:gap-0 justify-normal md:justify-between items-center px-4 py-5">
                                            <div class="transition-all ease-in-out duration-300 font-bold">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-upload">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                    <path d="M7 9l5 -5l5 5" />
                                                    <path d="M12 4l0 12" />
                                                </svg>
                                            </div>
                                            <div class="transition-all ease-in-out duration-300 font-bold">
                                                อัปโหลดไฟล์
                                            </div>
                                        </div>
                                        <div class="p-4 w-full border-b">
                                            <form action="" method="post" enctype="multipart/form-data">
                                                <div class="mb-5">
                                                    <label for="fileTitle" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ชื่อไฟล์</label>
                                                    <input type="text" name="fileTitle" id="fileTitle" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5" placeholder="กำหนดการหลักสูตร" />
                                                </div>
                                                <div class="flex items-center justify-center mb-5">
                                                    <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                            <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                                            </svg>
                                                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">คลิกเพื่ออัปโหลด</span> หรือ ลาก วาง</p>
                                                            <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLS, XLW, PPTX, ZIP, RAR</p>
                                                        </div>
                                                        <input id="dropzone-file" name="file" type="file" class="hidden" />
                                                    </label>
                                                </div>
                                                <div id="file-preview" class="hidden mb-5">
                                                    <h3 class="text-lg font-semibold mb-2">ตัวอย่างไฟล์:</h3>
                                                    <div class="bg-gray-100 p-4 rounded-lg">
                                                        <p id="file-name" class="font-medium"></p>
                                                        <p id="file-size" class="text-sm text-gray-600"></p>
                                                    </div>
                                                </div>
                                                <div>
                                                    <button type="submit" class="transition-all ease-in-out duration-300 w-full text-white bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">อัปโหลดไฟล์</button>
                                                </div>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($courseDetails['courseFiles'])) { ?>
                                        <?php foreach ($courseDetails['courseFiles'] as $file) : ?>
                                            <?php
                                            $iconFile = [
                                                "pdf" => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-file-type-pdf"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" /><path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" /><path d="M17 18h2" /><path d="M20 15h-3v6" /><path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z" /></svg>',
                                                "doc" => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-file-text"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 9l1 0" /><path d="M9 13l6 0" /><path d="M9 17l6 0" /></svg>',
                                                "xls" => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-file-type-xls"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" /><path d="M4 15l4 6" /><path d="M4 21l4 -6" /><path d="M17 20.25c0 .414 .336 .75 .75 .75h1.25a1 1 0 0 0 1 -1v-1a1 1 0 0 0 -1 -1h-1a1 1 0 0 1 -1 -1v-1a1 1 0 0 1 1 -1h1.25a.75 .75 0 0 1 .75 .75" /><path d="M11 15v6h3" /></svg>',
                                                "ppt" => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-file-type-ppt"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" /><path d="M11 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" /><path d="M16.5 15h3" /><path d="M18 15v6" /><path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" /></svg>',
                                                "zip" => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-file-zip"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 20.735a2 2 0 0 1 -1 -1.735v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2h-1" /><path d="M11 17a2 2 0 0 1 2 2v2a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1v-2a2 2 0 0 1 2 -2z" /><path d="M11 5l-1 0" /><path d="M13 7l-1 0" /><path d="M11 9l-1 0" /><path d="M13 11l-1 0" /><path d="M11 13l-1 0" /><path d="M13 15l-1 0" /></svg>',
                                                "default" => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex lucide lucide-file"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>'
                                            ];
                                            switch ($file['courseFileType']) {
                                                case "pdf":
                                                    $svg = $iconFile["pdf"];
                                                    $i = 1;
                                                    break;
                                                case "doc":
                                                case "docx":
                                                    $svg = $iconFile["doc"];
                                                    break;
                                                case "xls":
                                                case "xlsx":
                                                    $svg = $iconFile["xls"];
                                                    break;
                                                case "ppt":
                                                case "pptx":
                                                    $svg = $iconFile["ppt"];
                                                    break;
                                                case "zip":
                                                case "rar":
                                                    $svg = $iconFile["zip"];
                                                    break;
                                                default:
                                                    $svg = $iconFile["default"];
                                                    break;
                                            }
                                            ?>
                                            <div class="transition-all ease-in-out duration-300 group hover:shadow-lg border-b cursor-pointer">
                                                <div class="flex justify-between items-center px-4 py-5">
                                                    <a target="_blank" href="/public/courses/files/<?php echo $courseDetails['courseKey'] ?>/<?php echo $file['courseFileName']; ?>" class="no-underline">
                                                        <div class="transition-all ease-in-out duration-300 font-semibold group-hover:text-purple-600">
                                                            <?php echo $svg ? $svg : "" ?>
                                                            <span><?php echo $file['courseFileTitle']; ?> </span>
                                                            <span class="opacity-80">(<?php echo number_format(($file['courseFileSize'] / 1024 / 1024), 2); ?> MB)</span>
                                                        </div>
                                                    </a>
                                                    <div class="flex gap-3">
                                                        <div class="md:block hidden">
                                                            <a href="/public/courses/files/<?php echo $courseDetails['courseKey'] ?>/<?php echo $file['courseFileName']; ?>"
                                                                download="<?php echo $file['courseFileTitle']; ?>"
                                                                class="transition-all ease-in-out duration-300 font-bold text-purple-500 group-hover:text-purple-600 no-underline">
                                                                ดาวน์โหลด
                                                            </a>
                                                        </div>
                                                        <?php if (isset($_SESSION['userId'])) : ?>
                                                            <div>
                                                                <a class="no-underline" href="/course/<?php echo $courseId ?>/<?php echo $courseDetails['courseKey'] ?>/file/remove/<?php echo $file['id'] ?>">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                        <path d="M4 7l16 0" />
                                                                        <path d="M10 11l0 6" />
                                                                        <path d="M14 11l0 6" />
                                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php } else { ?>
                                        <div class="flex justify-center border-b">
                                            <div class="px-4 py-5 text-gray-500">
                                                ไม่มีเอกสารประกอบในหลักสูตร
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php else : ?>
                        <p>Course not found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    <script>
        document.getElementById('dropzone-file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const filePreview = document.getElementById('file-preview');
                const fileName = document.getElementById('file-name');
                const fileSize = document.getElementById('file-size');

                filePreview.classList.remove('hidden');
                fileName.textContent = `ชื่อไฟล์: ${file.name}`;
                fileSize.textContent = `ขนาดไฟล์: ${(file.size / (1024 * 1024)).toFixed(2)} MB`;

                const fileNameWithoutExtension = file.name.split('.').slice(0, -1).join('.');
                document.getElementById('fileTitle').value = fileNameWithoutExtension;
            }
        });
    </script>
</body>

</html>
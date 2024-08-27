<?php
require_once './link/title.php';
require_once './config/db.php';
require_once './functions/Course/Course.php';
require_once './functions/Course/Category.php';
require_once './functions/lib/ConvertThaiDate.php';

global $conn;
$categories = getCategory($conn);
if (!isset($_SESSION['userId'])) {
    header("location: /");
} else {
    $userId = $_SESSION['userId'];
    $user = getUser($conn, $userId);
    $canPost = $user['canPostCourse'];
    $manageUser = $user['canManageUser'];
    if ($canPost == 0) {
        header("location: /");
    }
}
$courseDetails = null;
if (isset($id) && $id != '') {
    $courseId = htmlspecialchars($id);
    $courseDetails = getCourseById($conn, $courseId);
    if (!$courseDetails) {
        header('Location: /');
        exit();
    }
} else {
    header('Location: /');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $courseKey = $courseDetails['courseKey'];
    $title = $_POST['title'];
    $categoryId = (int)$_POST['category'];
    $courseShowHomepage = isset($_POST['showHomepage']) ? (int)$_POST['showHomepage'] : 0;
    $startDate = convertThaiDateToGregorian($_POST['startDate']);
    $endDate = convertThaiDateToGregorian($_POST['endDate']);
    $startEnrollDate = convertThaiDateToGregorian($_POST['startEnrollDate']);
    $endEnrollDate = convertThaiDateToGregorian($_POST['endEnrollDate']);
    $enrollFee = isset($_POST['enrollFee']) ? (int)$_POST['enrollFee'] : 0;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 0;
    $target = $_POST['target'];
    $location = $_POST['location'];
    $food = isset($_POST['food']) ? (int)$_POST['food'] : 0;
    $online = isset($_POST['online']) ? (int)$_POST['online'] : 0;
    $onsite = isset($_POST['onsite']) ? (int)$_POST['onsite'] : 0;
    $open = isset($_POST['open']) ? (int)$_POST['open'] : 0;

    $thumbnail = $courseDetails['courseThumbnail'];

    $existingImages = $_POST['existing_images'] ?? [];
    $newImages = $_FILES['new_images'] ?? [];
    $currentImages = scandir("./public/courses/images/{$courseKey}");

    foreach ($currentImages as $image) {
        if ($image != '.' && $image != '..' && !in_array($image, $existingImages)) {
            unlink("./public/courses/images/{$courseKey}/" . $image);
        }
    }

    if (!empty($newImages['name'][0])) {
        foreach ($newImages['tmp_name'] as $key => $tmp_name) {
            $fileExtension = pathinfo($newImages['name'][$key], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $fileExtension;
            move_uploaded_file($tmp_name, "./public/courses/images/{$courseKey}/" . $fileName);
            $existingImages[] = $fileName;
        }
    }

    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = "./public/courses/thumbnails/{$courseKey}/";
        if (!empty($thumbnail) && file_exists($uploadDir . $thumbnail)) {
            unlink($uploadDir . $thumbnail);
        }

        $fileExtension = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $fileExtension;
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $uploadFile)) {
            $thumbnail = $fileName;
        }
    } else if ($_FILES['thumbnail']['size'] > 10485760) {
        $_SESSION["thumbnailERR"] = '<script>
                 Swal.fire({
                     title: "อัพโหลดรูปภาพ ' . $_FILES['thumbnail']["name"] . ' ไม่สำเร็จ",
                     text: "มีปัญหาบางอย่างเกิดขึ้น หรือ ไฟล์ขนาดใหญ่เกินไป",
                     icon: "warning",
                     confirmButtonColor: "#d33",
                     confirmButtonText: "ลองใหม่",
                 })
             </script>';
    }
    $data = array(
        'courseKey' => $courseKey,
        'courseTitle' => $title,
        'courseCategoryId' => $categoryId,
        'courseShowHomepage' => $courseShowHomepage,
        'courseThumbnail' => $thumbnail,
        'courseDate' => [
            'courseStartDate' => $startDate,
            'courseEndDate' => $endDate,
            'courseStartEnrollDate' => $startEnrollDate,
            'courseEndEnrollDate' => $endEnrollDate,
        ],
        'courseEnrollFee' => $enrollFee,
        'courseLimit' => $limit,
        'courseTarget' => $target,
        'courseLocation' => $location,
        'courseFood' => $food,
        'courseOnline' => $online,
        'courseOnsite' => $onsite,
        'courseImages' => $existingImages,
        'courseOpen' => $open,
    );
    if (updateCourse($conn, $data)) {
        $_SESSION["alert"] = '<script>
            Swal.fire({
                title: "แก้ไขหลักสูตรเรียบร้อย!",
                text: "สามารถแก้ไขข้อมูลได้ตลอด",
                icon: "success",
                confirmButtonColor: "#5fe280",
                confirmButtonText: "โอเค",
            })
        </script>';
        $_SESSION["updateCourseInfo"] = array(
            "courseTitle" => $title
        );
        header("Location: /index.php?page=course_detail&id={$courseDetails['id']}");
        exit();
    };
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
    <script src="/functions/preview.js"></script>
    <script src="/lib/jquery-3.7.1.min.js"></script>
    <script src="/lib/sweetalert2.all.min.js"></script>
    <?php require './link/pickdate.php' ?>
</head>

<body>
    <?php require_once './layout/Navbar.php'; ?>
    <?php
    if (isset($_SESSION['thumbnailERR'])) {
        echo $_SESSION['thumbnailERR'];
        unset($_SESSION['thumbnailERR']);
    }
    ?>
    <?php
    if (isset($_SESSION['alert'])) {
        echo $_SESSION['alert'];
        unset($_SESSION['alert']);
    }
    ?>
    <main>
        <div class="container px-4 md:px-12 mx-auto">
            <div class="shadow-lg p-4 md:p-10 border min-h-screen">
                <div>
                    <h1 class="text-2xl font-medium text-white text-center bg-cyan-400 p-4"><?php echo $courseDetails['courseTitle'] ?></h1>
                </div>
                <div class="md:p-10 p-4">
                    <form class="mx-auto" action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-5">
                            <h3 for="dropzone-file" class="mb-4 font-semibold text-gray-900">ภาพปกการอบรม</h3>
                            <div class="mb-3">
                                <label class="inline-flex items-center mr-3 cursor-pointer">
                                    <input type="checkbox" name="open" <?php echo ($courseDetails['courseOpen'] == 1) ? "checked" : "" ?> value="1" class="sr-only peer">
                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">เปิดให้ลงทะเบียน</span>
                                </label>
                            </div>
                            <div class="flex justify-center" id="imgBefore">
                                <img class="max-w-[350px] object-fit" src="/public/courses/thumbnails/<?php echo $courseDetails['courseKey']; ?>/<?php echo $courseDetails['courseThumbnail']; ?>" alt="Course Image">
                            </div>
                            <div class="flex justify-center">
                                <div class="max-w-[500px]">
                                    <div id="image-preview" class="border border-dashed border-cyan-300 rounded-lg object-cover mt-4"></div>
                                </div>
                            </div>
                            <div class="flex items-center justify-center w-full my-5">
                                <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">คลิกเพื่อแก้ไขรูป</span> หรือ ลาก วาง</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG หรือ JPEG</p>
                                    </div>
                                    <input id="dropzone-file" name="thumbnail" type="file" class="hidden" accept="image/png, image/jpeg, image/jpg" onchange="previewImageAndRemoveImg(event)" />
                                </label>
                            </div>
                        </div>
                        <div class="mb-5">
                            <label for="title" class="block mb-2 text-sm font-medium text-gray-900">หัวข้อ</label>
                            <input type="text" id="title" name="title" value="<?php echo $courseDetails['courseTitle'] ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="อบรมเรื่อง..." required />
                        </div>
                        <div class="mb-5">
                            <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">หมวดหมู่</label>
                            <select id="category" name="category" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="">-- เลือกหมวดหมู่หลักสูตร --</option>
                                <?php foreach ($categories as $category) : ?>
                                    <option value="<?php echo $category['id'] ?>" <?php echo ($category['id'] == $courseDetails['courseCategoryId']) ? 'selected' : ''; ?>>
                                        <?php echo $category['categoryTitle'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="md:flex gap-x-3 justify-between mb-5">
                            <div class="mb-5 md:mb-0 w-full">
                                <label for="startDate" class="block mb-2 text-sm font-medium text-gray-900">วันที่เริ่มอบรม</label>
                                <input type="text" onkeydown="clearBorder(this)" id="startDateValue" name="startDate" value="<?php echo convertGregorianDateToThai($courseDetails['courseStartDate']) ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5" placeholder="วัน/เดือน/ปี" />
                            </div>
                            <div class="mb-5 md:mb-0 w-full">
                                <label for="endDate" class="block mb-2 text-sm font-medium text-gray-900">วันที่สิ้นสุดอบรม</label>
                                <input type="text" onkeydown="clearBorder(this)" id="endDateValue" name="endDate" value="<?php echo convertGregorianDateToThai($courseDetails['courseEndDate']) ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5" placeholder="วัน/เดือน/ปี" />
                            </div>
                        </div>
                        <div class="md:flex gap-x-3 justify-between mb-5">
                            <div class="mb-5 md:mb-0 w-full">
                                <label for="startEnrollDate" class="block mb-2 text-sm font-medium text-gray-900">วันที่เริ่มลงทะเบียน</label>
                                <input type="text" onkeydown="clearBorder(this)" id="startEnrollDateValue" name="startEnrollDate" value="<?php echo convertGregorianDateToThai($courseDetails['courseStartEnrollDate']) ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5" placeholder="วัน/เดือน/ปี" />
                            </div>
                            <div class="mb-5 md:mb-0 w-full">
                                <label for="endEnrollDate" class="block mb-2 text-sm font-medium text-gray-900">วันที่สิ้นสุดลงทะเบียน</label>
                                <input type="text" onkeydown="clearBorder(this)" id="endEnrollDateValue" name="endEnrollDate" value="<?php echo convertGregorianDateToThai($courseDetails['courseEndEnrollDate']) ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5" placeholder="วัน/เดือน/ปี" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="inline-flex items-center cursor-pointer">
                                <input id="enrollFeeOpen" type="checkbox" <?php echo ($courseDetails['courseEnrollFee'] != 0) ? "checked" : "" ?> value="1" class="sr-only peer">
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-900">มีค่าลงทะเบียน</span>
                            </label>
                        </div>
                        <div class="mb-5" id="enrollFeeForm">
                            <label for="enrollFee" class="block mb-2 text-sm font-medium text-gray-900">ค่าลงทะเบียนจำนวน</label>
                            <input type="number" id="enrollFee" name="enrollFee" value="<?php echo $courseDetails['courseEnrollFee'] ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="" />
                        </div>
                        <div class="mb-3">
                            <label class="inline-flex items-center cursor-pointer">
                                <input id="limitOpen" type="checkbox" <?php echo ($courseDetails['courseLimit'] != 0) ? "checked" : "" ?> class="sr-only peer">
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-900">จำกัดจำนวนผู้ลงทะเบียน</span>
                            </label>
                        </div>
                        <div class="mb-5" id="limitForm">
                            <label for="limit" class="block mb-2 text-sm font-medium text-gray-900">จำนวนผู้ลงทะเบียน</label>
                            <input type="number" id="limit" name="limit" value="<?php echo $courseDetails['courseLimit'] ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="" />
                        </div>
                        <div class="mb-5">
                            <label for="target" class="block mb-2 text-sm font-medium text-gray-900">กลุ่มเป้าหมาย</label>
                            <input type="text" id="target" name="target" value="<?php echo $courseDetails['courseTarget'] ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="วัน/เดือน/ปี - วัน/เดือน/ปี" required />
                        </div>
                        <div class="mb-5">
                            <label for="location" class="block mb-2 text-sm font-medium text-gray-900">สถานที่</label>
                            <input type="text" id="location" name="location" value="<?php echo $courseDetails['courseLocation'] ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="วัน/เดือน/ปี - วัน/เดือน/ปี" required />
                        </div>
                        <div class="mb-3">
                            <label class="inline-flex items-center mr-3 cursor-pointer">
                                <input type="checkbox" name="food" <?php echo ($courseDetails['courseFoodQuestion'] == 1) ? "checked" : "" ?> value="1" class="sr-only peer">
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">มีอาหารแจกในการอบรม</span>
                            </label>
                        </div>
                        <div class="mb-3">
                            <label class="inline-flex items-center mr-3 cursor-pointer">
                                <input type="checkbox" name="online" <?php echo ($courseDetails['courseOnline'] == 1) ? "checked" : "" ?> value="1" class="sr-only peer">
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">เปิดออนไลน์</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="onsite" <?php echo ($courseDetails['courseOnsite'] == 1) ? "checked" : "" ?> value="1" class="sr-only peer">
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">เปิดออนไซต์</span>
                            </label>
                        </div>
                        <div class="mb-3">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="showHomepage" <?php echo ($courseDetails['courseShowHomepage'] == 1) ? "checked" : "" ?> value="1" class="sr-only peer">
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">นำขึ้นหน้าแรกเว็บไซต์</span>
                            </label>
                        </div>
                        <div class="mb-5">
                            <h3 for="dropzone-images" class="mb-4 font-semibold text-gray-900">ภาพรายละเอียดการอบรม</h3>
                            <div class="flex items-center justify-center w-full">
                                <label for="dropzone-images" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">คลิกเพื่ออัปโหลด</span> หรือ ลาก วาง</p>
                                        <p class="mb-2 text-sm text-gray-500">อัปโหลดพร้อมกันหลายไฟล์ได้</p>
                                        <p class="text-xs text-gray-500">PNG, JPG หรือ JPEG</p>
                                    </div>
                                    <input id="dropzone-images" name="new_images[]" type="file" class="hidden" accept="image/png, image/jpeg, image/jpg" multiple onchange="previewImages(event)" />
                                </label>
                            </div>
                            <div class="w-full">
                                <div id="images-preview" class="rounded-lg"></div>
                            </div>
                        </div>
                        <div id="existing-images" class="mb-5 flex flex-wrap items-center justify-center">
                            <?php foreach ($courseDetails['courseImages'] as $index => $courseImage) : ?>
                                <div class="w-full md:w-1/2 p-1 existing-image" data-index="<?php echo $index; ?>">
                                    <div class="transition-all duration-300 ease-in-out hover:drop-shadow-lg relative overflow-hidden rounded-lg">
                                        <img class="transition-transform duration-300 ease-in-out transform hover:scale-[1.05] w-full h-full object-cover border border-[#DDDDDD]" src="/public/courses/images/<?php echo $courseDetails['courseKey']; ?>/<?php echo $courseImage; ?>" alt="Course Image">
                                        <button type="button" class="delete-image absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center cursor-pointer" onclick="removeExistingImage(<?php echo $index; ?>)">×</button>
                                    </div>
                                </div>
                                <input type="hidden" name="existing_images[]" value="<?php echo $courseImage; ?>" />
                            <?php endforeach; ?>
                        </div>
                        <button type="submit" class="transition-all ease-in-out duration-300 w-full text-white bg-purple-500 hover:bg-purple-600 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">แก้ไขหลักสูตร</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <script src="/lib/pickdate-init.js"></script>

</body>
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const enrollFeeOpen = document.getElementById('enrollFeeOpen');
        const enrollFeeForm = document.getElementById('enrollFeeForm');
        const enrollFeeInput = document.getElementById('enrollFee');
        const limitOpen = document.getElementById('limitOpen');
        const limitForm = document.getElementById('limitForm');
        const limitInput = document.getElementById('limit');

        if (enrollFeeForm && enrollFeeOpen) {
            enrollFeeForm.style.display = enrollFeeOpen.checked ? 'block' : 'none';
        }

        if (limitForm && limitOpen) {
            limitForm.style.display = limitOpen.checked ? 'block' : 'none';
        }

        if (enrollFeeOpen && enrollFeeForm && enrollFeeInput) {
            enrollFeeOpen.addEventListener('change', (event) => {
                if (event.target.checked) {
                    enrollFeeForm.style.display = 'block';
                } else {
                    enrollFeeForm.style.display = 'none';
                    enrollFeeInput.value = '';
                }
            });
        }

        if (limitOpen && limitForm && limitInput) {
            limitOpen.addEventListener('change', (event) => {
                if (event.target.checked) {
                    limitForm.style.display = 'block';
                } else {
                    limitForm.style.display = 'none';
                    limitInput.value = '';
                }
            });
        }
    });

    const openModalButton = document.getElementById('openModal');
    const closeModalButton = document.getElementById('closeModal');
    const modal = document.getElementById('modal');

    if (openModalButton && modal) {
        openModalButton.addEventListener('click', function() {
            modal.classList.remove('hidden');
        });
    }

    if (closeModalButton && modal) {
        closeModalButton.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
    }

</script>

</html>
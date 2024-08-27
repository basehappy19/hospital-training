<?php
require_once './link/title.php';
require_once './functions/lib/Files.php';
require_once './functions/lib/GenerateKey.php';
require_once './functions/lib/ConvertThaiDate.php';
require_once './functions/Course/Course.php';
require_once './functions/Course/Category.php';
global $conn;

if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
    $user = getUser($conn, $userId);
    $canPost = $user['canPostCourse'];
    $manageUser = $user['canManageUser'];
}

$categories = getCategory($conn);

if ($_SERVER['REQUEST_METHOD'] == "POST" && !isset($_POST['clearSessionRemove']) && !isset($_POST['clearSessionNewCourse'])) {
    $courseKey = generateKey();
    $title = $_POST['title'];
    $categoryId = (int)$_POST['category'];
    $startDate = convertThaiDateToGregorian($_POST['startDate']);
    $endDate = convertThaiDateToGregorian($_POST['endDate']);
    $startEnrollDate = convertThaiDateToGregorian($_POST['startEnrollDate']);
    $endEnrollDate = convertThaiDateToGregorian($_POST['endEnrollDate']);
    $enrollFee = isset($_POST['enrollFee']) ? (int)$_POST['enrollFee'] : 0;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 0;
    $location = $_POST['location'];
    $food = isset($_POST['food']) ? (int)$_POST['food'] : 0;
    $online = isset($_POST['online']) ? (int)$_POST['online'] : 0;
    $onsite = isset($_POST['onsite']) ? (int)$_POST['onsite'] : 0;
    $thumbnail = '';

    $targetType = isset($_POST['target']) ? $_POST['target'] : [];

    $targetTypeOther = isset($_POST["type-target-other"]) ? $_POST["type-target-other"] : "";

    if (!empty($targetType)) {
        if (in_array('อื่นๆ', $targetType)) {
            $targetType = array_diff($targetType, ['อื่นๆ']);
            if (!empty($targetTypeOther)) {
                $targetType[] = $targetTypeOther;
            }
        }
        $resultTargetType = implode(", ", $targetType);
    } else {
        $resultTargetType = "-";
    }

    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == UPLOAD_ERR_OK) {
        createFolder("./public/courses/thumbnails/{$courseKey}/");
        createFolder("./public/courses/images/{$courseKey}/");
        createFolder("./public/courses/files/{$courseKey}/");
        createFolder("./uploads/payment-proof/{$courseKey}/");
        $thumbnail = upload($_FILES['thumbnail'], "./public/courses/thumbnails/{$courseKey}/", ['image/jpeg', 'image/png', 'image/jpg']);

        $filesName = [];
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $files = $_FILES['images'];
            $folder = "./public/courses/images/{$courseKey}/";
            createFolder($folder);
            $names = $files['name'];
            $tmp_names = $files['tmp_name'];
            $errors = $files['error'];
            $types = $files['type'];

            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

            foreach ($names as $index => $name) {
                if ($errors[$index] == UPLOAD_ERR_OK) {
                    $fileType = $types[$index];

                    if (in_array($fileType, $allowedTypes)) {
                        $fileExtension = pathinfo($name, PATHINFO_EXTENSION);
                        $fileName = uniqid() . '.' . $fileExtension;

                        if (move_uploaded_file($tmp_names[$index], $folder . $fileName)) {
                            $filesName[] = $fileName;
                        } else {
                            $_SESSION["alert"] = '<script>
                                Swal.fire({
                                    title: "อัพโหลดรูปภาพ ' . $name . ' ไม่สำเร็จ",
                                    text: "มีปัญหาบางอย่างเกิดขึ้น หรือ ไฟล์ขนาดใหญ่เกินไป",
                                    icon: "warning",
                                    confirmButtonColor: "#d33",
                                    confirmButtonText: "ลองใหม่",
                                    })
                                </script>';
                            header("Location: " . $_SERVER['REQUEST_URI']);
                            exit();
                        }
                    } else {
                        $_SESSION["alert"] = '<script>
                                Swal.fire({
                                    title: "ประเภทไฟล์ของ ' . $name . ' ไม่อนุญาตให้อัปโหลด",
                                    text: "JPG, JPEG, PNG เท่าันั้น",
                                    icon: "warning",
                                    confirmButtonColor: "#d33",
                                    confirmButtonText: "ลองใหม่",
                                    })
                                </script>';
                        header("Location: " . $_SERVER['REQUEST_URI']);
                        exit();
                    }
                } else {
                    $_SESSION["alert"] = '<script>
                                Swal.fire({
                                    title: "อัพโหลดรูปภาพ ' . $name . ' ไม่สำเร็จ",
                                    text: "มีปัญหาบางอย่างเกิดขึ้น  Error code : ' . $errors[$index] . '",
                                    icon: "warning",
                                    confirmButtonColor: "#d33",
                                    confirmButtonText: "ลองใหม่",
                                    })
                                </script>';
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit();
                }
            }

            if (!empty($filesName)) {
                $_SESSION["alert"] = '<script>
                            Swal.fire({
                                title: "อัปโหลดรูปภาพเรียบร้อย!",
                                text: "ทั้งหมด ' . count($filesName) . ' รูป",
                                icon: "success",
                                confirmButtonColor: "#5fe280",
                                confirmButtonText: "โอเค",
                            })
                        </script>';
            } else {
                $_SESSION["alert"] = '<script>
                    Swal.fire({
                        title: "อัพโหลดรูปภาพทั้งหมดไม่สำเร็จ",
                        text: "มีปัญหาบางอย่างเกิดขึ้น หรือ ไฟล์ขนาดใหญ่เกินไป",
                        icon: "warning",
                        confirmButtonColor: "#d33",
                        confirmButtonText: "ลองใหม่",
                        })
                    </script>';
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            }
        }
        $fileTitle = isset($_POST['fileTitle']) ? $_POST['fileTitle'] : "-";
        $files = [];
        if (isset($_FILES['file']) && !empty($_FILES['file']['name'][0])) {
            $uploadDir = "./public/courses/files/{$courseKey}/";
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
                    $files = [
                        "fileName" => $file,
                        "fileTitle" => $fileTitle,
                        "fileType" => $fileType,
                        "fileSize" => $size,
                    ];
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
        }

        $data = array(
            'courseKey' => $courseKey,
            'courseTitle' => $title,
            'courseCategoryId' => $categoryId,
            'courseThumbnail' => $thumbnail,
            'courseDate' => [
                'courseStartDate' => $startDate,
                'courseEndDate' => $endDate,
                'courseStartEnrollDate' => $startEnrollDate,
                'courseEndEnrollDate' => $endEnrollDate,
            ],
            'courseEnrollFee' => $enrollFee,
            'courseLimit' => $limit,
            'courseTarget' => $resultTargetType,
            'courseLocation' => $location,
            'courseFood' => $food,
            'courseOnline' => $online,
            'courseOnsite' => $onsite,
            'courseImages' => $filesName,
            'files' => $files,
        );
        if (newCourse($conn, $data)) {
            $_SESSION["alert"] = '<script>
                        Swal.fire({
                            title: "เพิ่มหลักสูตรเรียบร้อย!",
                            text: "สามารถแก้ไขข้อมูลได้ตลอด",
                            icon: "success",
                            confirmButtonColor: "#5fe280",
                            confirmButtonText: "โอเค",
                        })
                    </script>';
            $_SESSION["newCourseInfo"] = array(
                "courseTitle" => $title
            );
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        };
    } else {
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
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['clearSessionRemove'])) {
    unset($_SESSION['removeCourseInfo']);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['clearSessionNewCourse'])) {
    unset($_SESSION['newCourseInfo']);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
if (isset($id)) {
    $category = getCategoryById($conn, $id);
    $titleHead = $category[0]['categoryTitle'];
    $categoryId = $category[0]['id'];
} else {
    $titleHead = 'หลักสูตรทั้งหมด';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php title('หลักสูตรทั้งหมด'); ?>
    <?php require './link/styles.php' ?>
    <?php require './link/favicon.php' ?>
    <script src="/lib/sweetalert2.all.min.js"></script>
    <script src="./functions/preview.js"></script>
    <script src="./functions/Course/course.js"></script>
    <script src="./lib/jquery-3.7.1.min.js"></script>
    <?php require './link/pickdate.php' ?>
</head>

<body>
    <?php require_once './layout/Navbar.php' ?>
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
                <h1 class="text-2xl font-semibold"><?php echo $titleHead; ?></h1>
                <div class="my-5">
                    <div class="flex flex-wrap flex-row justify-center items-center">
                        <a class="transition-all bg-purple-300 hover:bg-purple-400 w-full py-2 border border-gray-500" href="/?page=courses">
                            <div class="text-center">
                                <span class="font-semibold">หลักสูตรทั้งหมด</span>
                            </div>
                        </a>
                        <?php foreach ($categories as $category) : ?>
                            <a class="transition-all <?php echo (isset($id) && $category['id'] == $categoryId) ? 'bg-purple-300' : 'bg-purple-200'; ?> hover:bg-purple-400 w-1/2 py-2 border border-gray-500" href="?page=courses&id=<?php echo $category['id'] ?>">
                                <div class="text-center">
                                    <?php if (isset($id) && $category['id'] == $categoryId) { ?>
                                        <span class="font-semibold"><?php echo $category['categoryTitle'] ?> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-circle-dashed-check">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95" />
                                                <path d="M3.69 8.56a9 9 0 0 0 -.69 3.44" />
                                                <path d="M3.69 15.44a9 9 0 0 0 1.95 2.92" />
                                                <path d="M8.56 20.31a9 9 0 0 0 3.44 .69" />
                                                <path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95" />
                                                <path d="M20.31 15.44a9 9 0 0 0 .69 -3.44" />
                                                <path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92" />
                                                <path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69" />
                                                <path d="M9 12l2 2l4 -4" />
                                            </svg>
                                        </span>
                                    <?php } else { ?>
                                        <span class="font-semibold"><?php echo $category['categoryTitle'] ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-circle-dashed-plus">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95" />
                                                <path d="M3.69 8.56a9 9 0 0 0 -.69 3.44" />
                                                <path d="M3.69 15.44a9 9 0 0 0 1.95 2.92" />
                                                <path d="M8.56 20.31a9 9 0 0 0 3.44 .69" />
                                                <path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95" />
                                                <path d="M20.31 15.44a9 9 0 0 0 .69 -3.44" />
                                                <path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92" />
                                                <path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69" />
                                                <path d="M9 12h6" />
                                                <path d="M12 9v6" />
                                            </svg>
                                        </span>
                                    <?php } ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php if (isset($_SESSION['removeCourseInfo'])) : ?>
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
                                <button type="submit" name="clearSessionRemove" class="transition-all md:absolute right-2 top-2 bg-cyan-300 hover:bg-cyan-500 text-black font-bold p-2 rounded">
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
                                ลบข้อมูลหลักสูตร "<?php echo $_SESSION['removeCourseInfo']['courseTitle'] ?>" เรียบร้อย
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['newCourseInfo'])) : ?>
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
                                <button type="submit" name="clearSessionNewCourse" class="transition-all md:absolute right-2 top-2 bg-cyan-300 hover:bg-cyan-500 text-black font-bold p-2 rounded">
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
                                ได้เพิ่มหลักสูตร "<?php echo $_SESSION['newCourseInfo']['courseTitle'] ?>" เรียบร้อย
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['userId']) && $canPost == 1) {
                    require './components/Course/AddCourse.php';
                } ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <?php require './components/Course/CourseCard.php' ?>
                </div>
            </div>
        </div>
    </main>
    <script src="./lib/pickdate-init.js"></script>
</body>

</html>
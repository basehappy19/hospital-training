<?php
require_once './link/title.php';
require_once './functions/Album/Album.php';
require_once './functions/lib/dir.php';
$default_max_file_uploads = 50;

$max_file_uploads = ini_get('max_file_uploads');
if ($max_file_uploads == false || $max_file_uploads == '') {
    $max_file_uploads = $default_max_file_uploads;
} else {
    $max_file_uploads = (int)$max_file_uploads;
}

global $conn;
if (isset($_GET['key']) && $_GET['key'] != '') {
    $albumKey = htmlspecialchars($_GET['key']);
    $albumDetails = getAlbumByKey($conn, $albumKey);

    if (!$albumDetails) {
        header('Location: /index.php');
        exit();
    }
} else {
    header('Location: /index.php'); 
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && !isset($_POST['clearSessionUpdate'])) {
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $files = $_FILES['images'];
        $path = "./public/albums/{$albumKey}/";

        createFolder($path);

        $names = $files['name'];
        $tmp_names = $files['tmp_name'];
        $errors = $files['error'];
        $types = $files['type'];

        $filesName = [];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $totalFiles = count($names);
        $successfulUploads = 0;
        if ($totalFiles > $max_file_uploads) {
            $_SESSION["alert"] = '<script>
                Swal.fire({
                    title: "จำนวนไฟล์เกินกำหนด",
                    text: "คุณสามารถอัพโหลดได้สูงสุด ' . $max_file_uploads . ' ไฟล์ในครั้งเดียว",
                    icon: "warning",
                    confirmButtonColor: "#d33",
                    confirmButtonText: "เข้าใจแล้ว",
                })
            </script>';
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        } else {
            foreach ($names as $index => $name) {
                if ($errors[$index] == UPLOAD_ERR_OK) {
                    $fileType = $types[$index];

                    if (in_array($fileType, $allowedTypes)) {
                        $fileExtension = pathinfo($name, PATHINFO_EXTENSION);
                        $fileName = uniqid() . '.' . $fileExtension;

                        if (move_uploaded_file($tmp_names[$index], $path . $fileName)) {
                            $filesName[] = $fileName;
                            $successfulUploads++;
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
                        }
                    } else {
                        $_SESSION["alert"] = '<script>
                            Swal.fire({
                                title: "ประเภทไฟล์ของ ' . $name . ' ไม่อนุญาตให้อัปโหลด",
                                text: "JPG, JPEG, PNG เท่านั้น",
                                icon: "warning",
                                confirmButtonColor: "#d33",
                                confirmButtonText: "ลองใหม่",
                            })
                        </script>';
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
                }
            }
        }

        if (!empty($filesName)) {
            if ($successfulUploads < $totalFiles) {
                $_SESSION["alert"] = '<script>
                    Swal.fire({
                        title: "อัปโหลดรูปภาพเสร็จสิ้น แต่มีบางส่วนไม่สำเร็จ",
                        text: "อัปโหลดสำเร็จ ' . $successfulUploads . ' จาก ' . $totalFiles . ' รูป",
                        icon: "warning",
                        confirmButtonColor: "#ffa500",
                        confirmButtonText: "เข้าใจแล้ว",
                    })
                </script>';
            } else {
                $_SESSION["alert"] = '<script>
                    Swal.fire({
                        title: "อัปโหลดรูปภาพเรียบร้อย!",
                        text: "ทั้งหมด ' . $successfulUploads . ' รูป",
                        icon: "success",
                        confirmButtonColor: "#5fe280",
                        confirmButtonText: "โอเค",
                    })
                </script>';
            }
            if (uploadImages($conn, $albumKey, $filesName)) {
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            }
        } else {
            $_SESSION["alert"] = '<script>
                Swal.fire({
                    title: "อัพโหลดรูปภาพทั้งหมดไม่สำเร็จ",
                    text: "มีปัญหาบางอย่างเกิดขึ้น หรือ ไฟล์ขนาดใหญ่เกินไป",
                    icon: "error",
                    confirmButtonColor: "#d33",
                    confirmButtonText: "ลองใหม่",
                })
            </script>';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['clearSessionUpdate'])) {
    unset($_SESSION['updateAlbumInfo']);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if ($albumDetails) : ?>
        <?php title($albumDetails['albumName']); ?>
    <?php else : ?>
        <?php title('Album Not Found'); ?>
    <?php endif; ?>
    <?php require './link/favicon.php'; ?>
    <?php require './link/styles.php'; ?>
    <script src="/functions/preview.js"></script>
    <script src="/functions/Album/Album.js"></script>
    <script src="/functions/Album/Upload.js"></script>
    <script src="/lib/sweetalert2.all.min.js"></script>
</head>

<body>
    <?php require_once './layout/Navbar.php'; ?>
    <?php
    if (isset($_SESSION['alert'])) {
        echo $_SESSION['alert'];
        unset($_SESSION['alert']);
    }
    ?>
    <main>
        <div class="md:p-4 w-full mx-auto">
            <div class="shadow-lg p-4 md:p-10 border min-h-screen">
                <?php if (isset($_SESSION['updateAlbumInfo'])) : ?>
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
                                แก้ไขข้อมูลอัลบั้ม "<?php echo $_SESSION['updateAlbumInfo']['albumName'] ?>" เรียบร้อย
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="my-5">
                    <div class="flex justify-between mb-5">
                        <h1 class="text-2xl font-semibold">อัลบั้ม <?php echo $albumDetails['albumName']; ?></h1>
                        <?php if (isset($_SESSION['userId'])) : ?>
                            <div class="flex justify-end items-center">
                                <div class="cursor-pointer font-semibold text-green-400 hover:text-green-500 transition-all p-2 rounded-lg">
                                    <a href="/index.php?page=album_edit&key=<?php echo $albumDetails['albumKey'] ?>">
                                        แก้ไข
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="inline-flex icon icon-tabler icons-tabler-filled icon-tabler-settings">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M14.647 4.081a.724 .724 0 0 0 1.08 .448c2.439 -1.485 5.23 1.305 3.745 3.744a.724 .724 0 0 0 .447 1.08c2.775 .673 2.775 4.62 0 5.294a.724 .724 0 0 0 -.448 1.08c1.485 2.439 -1.305 5.23 -3.744 3.745a.724 .724 0 0 0 -1.08 .447c-.673 2.775 -4.62 2.775 -5.294 0a.724 .724 0 0 0 -1.08 -.448c-2.439 1.485 -5.23 -1.305 -3.745 -3.744a.724 .724 0 0 0 -.447 -1.08c-2.775 -.673 -2.775 -4.62 0 -5.294a.724 .724 0 0 0 .448 -1.08c-1.485 -2.439 1.305 -5.23 3.744 -3.745a.722 .722 0 0 0 1.08 -.447c.673 -2.775 4.62 -2.775 5.294 0zm-2.647 4.919a3 3 0 1 0 0 6a3 3 0 0 0 0 -6z" />
                                        </svg>
                                    </a>
                                </div>
                                <div class="cursor-pointer font-semibold text-red-400 hover:text-red-500 transition-all p-2 rounded-lg">
                                    <a onclick="removeAlbum('/index.php?page=album_remove&key=<?php echo $albumDetails['albumKey'] ?>')">
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
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-y-3 gap-x-2">
                        <?php if (isset($_SESSION['userId'])) {
                            require './components/Album/ImageAdd.php';
                        } ?>
                        <?php require './components/Album/AlbumImages.php' ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>

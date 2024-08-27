<?php
require_once './link/title.php';
require_once './functions/Album/Album.php';
require_once './functions/lib/dir.php';

global $conn;
if (isset($_SESSION['userId'])) {
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
} else {
    header("location: /index.php?page=login"); 
    exit(); 
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $album = array(
        'albumName' => $_POST['name'],
        'courseKey' => isset($_POST['course']) ? $_POST['course'] : ""
    );
    if (updateAlbum($conn, $albumKey, $album)) {
        $_SESSION["alert"] = '<script>
            Swal.fire({
                title: "แก้ไขอัลบั้มเรียบร้อย!",
                text: "สามารถแก้ไขข้อมูลได้ตลอด",
                icon: "success",
                confirmButtonColor: "#5fe280",
                confirmButtonText: "โอเค",
            })
        </script>';
        $_SESSION["updateAlbumInfo"] = array(
            "albumName" => $album['albumName']
        );
        header("Location: /index.php?page=album_share&key={$albumKey}");
        exit();
    };
}

$courseOptions = getCourseOptions($conn);
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
    <div class="md:p-4 w-full mx-auto">
        <div class="shadow-lg p-4 md:p-10 border min-h-screen">
            <h1 class="text-2xl font-semibold">อัลบั้ม <?php echo $albumDetails['albumName']; ?></h1>
            <div class="my-5">
                <form class="mx-auto" action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-5">
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900">ชื่ออัลบั้ม</label>
                        <input type="text" onkeydown="clearBorder(this)" value="<?php echo $albumDetails['albumName'] ?>" id="name" name="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5" placeholder="ชื่ออัลบั้ม" />
                    </div>
                    <div class="mb-5">
                        <label for="course" class="block mb-2 text-sm font-medium text-gray-900">หลักสูตรที่เกี่ยวข้อง <span class="opacity-60">(*ไม่จำเป็นต้องกรอก)</span></label>
                        <select id="course" name="course" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5">
                            <option value="">-- เลือกหลักสูตร --</option>
                            <?php foreach ($courseOptions as $option) : ?>
                                <option value="<?php echo $option['courseKey'] ?>"  <?php echo ($option['courseKey'] == $albumDetails['courseKey']) ? 'selected' : ''; ?>><?php echo $option['courseTitle'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="transition-all w-full text-white bg-purple-500 hover:bg-purple-600 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">แก้ไขอัลบั้ม</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>

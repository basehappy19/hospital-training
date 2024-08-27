<?php
require_once './link/title.php';
require_once './functions/Album/Album.php';
require_once './functions/lib/GenerateKey.php';

global $conn;

if ($_SERVER['REQUEST_METHOD'] == "POST" && !isset($_POST['clearSessionRemove'])) {
    $album = array(
        "albumKey" => generateKey(),
        "albumName" => $_POST['name'],
        "courseKey" => isset($_POST['course']) ? $_POST['course'] : "",
    );
    if (newAlbum($conn, $album)) {
        $_SESSION["alert"] = '<script>
            Swal.fire({
                title: "เพิ่มอัลบั้มเรียบร้อย",
                text: "สามารถแก้ไขได้ตลอด",
                icon: "success",
                confirmButtonColor: "#5fe280",
                confirmButtonText: "โอเค",
            })
        </script>';
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['clearSessionRemove'])) {
    unset($_SESSION['removeAlbumInfo']);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php title('อัลบั้มรูปภาพ'); ?>
    <?php require './link/favicon.php'; ?>
    <?php require './link/styles.php'; ?>
    <script src="/lib/jquery-3.7.1.min.js"></script>
    <script src="/functions/Album/FetchAlbum.js"></script>
    <script src="/functions/Album/Album.js"></script>
    <script src="/lib/sweetalert2.all.min.js"></script>
</head>

<body onload="sendData()">
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
                <h1 class="text-2xl font-semibold">อัลบั้มรูปภาพ</h1>
                <?php if (isset($_SESSION['removeAlbumInfo'])) : ?>
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
                                ลบข้อมูลอัลบั้ม "<?php echo $_SESSION['removeAlbumInfo']['albumName'] ?>" เรียบร้อย
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="my-5">
                    <div class="flex flex-col md:flex-row gap-4 items-center mb-5">
                        <div class="w-full relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input type="search" id="default-search" name="search" class="h-14 w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-purple-500 focus:border-purple-500" placeholder="ค้นหาอัลบั้ม" oninput="sendData()" />
                        </div>
                        <div class="w-full relative flex items-center">
                            <button type="button" id="decrement-button" data-input-counter-decrement="row-input" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-14 focus:ring-gray-100 focus:ring-2 focus:outline-none">
                                <svg class="w-4 h-4 text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                </svg>
                            </button>
                            <input type="text" id="row-input" data-input-counter aria-describedby="helper-text-explanation" class="bg-gray-50 border-x-0 border-gray-300 h-14 text-center text-gray-900 text-sm focus:ring-purple-500 focus:border-purple-500 block w-full" placeholder="10 แถว" oninput="sendData()" value="10" />
                            <button type="button" id="increment-button" data-input-counter-increment="row-input" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-14 focus:ring-gray-100 focus:ring-2 focus:outline-none">
                                <svg class="w-4 h-4 text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                </svg>
                            </button>
                        </div>
                        <?php if (isset($_SESSION['userId'])) : ?>
                            <div class="w-full">
                                <button type="button" id="openModal" class="h-14 transition-all w-full text-white bg-purple-500 hover:bg-purple-600 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">เพิ่มอัลบั้มใหม่</button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div id="albums-container" class="relative overflow-x-auto"></div>
                </div>
                <?php if (isset($_SESSION['userId'])) {
                    require './components/Album/AlbumAdd.php';
                } ?>
            </div>
        </div>
    </main>
</body>

</html>

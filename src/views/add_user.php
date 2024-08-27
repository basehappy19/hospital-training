<?php 
require_once './link/title.php'; 
require_once './config/db.php';
require_once './functions/Users/User.php';
global $conn;

if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
    $user = getUser($conn, $userId);
    $canPost = $user['canPostCourse'];
    $manageUser = $user['canManageUser'];
    if($manageUser != 1) {
        header("location: /index.php"); 
        exit(); 
    }
} else {
    header("location: /index.php?page=login"); 
    exit(); 
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['username'];
    $displayName = $_POST['displayName'];
    $password = $_POST['password'];
    $canPostCourse = isset($_POST['canPostCourse']) ? (int)$_POST['canPostCourse'] : 0;
    $canManageUser = isset($_POST['canManageUser']) ? (int)$_POST['canManageUser'] : 0;

    $canAddUser = checkUser($conn, $username);

    if($canAddUser) {
        $data = array (
            "username" => $username,
            "displayName" => $displayName,
            "password" => $password,
            "canPostCourse" => $canPostCourse,
            "canManageUser" => $canManageUser,
        );
        $user = addUser($conn, $data);
        if($user){
            $_SESSION["alert"] = '<script>
                Swal.fire({
                    title: "เพิ่มผู้ใช้เรียบร้อย!",
                    text: "หากต้องการเปลี่ยนแปลงข้อมูล สามารถแก้ไขได้ตลอด",
                    icon: "success",
                    confirmButtonColor: "#5fe280",
                    confirmButtonText: "โอเค",
                })
            </script>';
            $_SESSION["addUserInfo"] = array(
                "username" => $username,
            );
            header("Location: /index.php?page=manage_users"); 
            exit();
        }
    } else {
        $_SESSION["alert"] = "<script>
            Swal.fire({
                title: \"เพิ่มผู้ใช้ไม่สำเร็จ\",
                text: \"" . addslashes($username) . " มีชื่อผู้ใช้นี้อยู่แล้ว\",
                icon: \"warning\",
                confirmButtonColor: \"#5fe280\",
                confirmButtonText: \"โอเค\"
            });
        </script>";
        header("Location: /index.php?page=manage_user_add"); 
        exit(); 
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php title('เพิ่มผู้ใช้'); ?>
    <?php require './link/styles.php'; ?>
    <?php require './link/favicon.php'; ?>
</head>

<body>
    <?php require_once './layout/Navbar.php'; ?>
    <main>
        <div class="container px-4 md:px-12 mx-auto">
            <div class="shadow-lg p-4 md:p-10 border min-h-screen">
                <h1 class="text-2xl font-semibold">เพิ่มผู้ใช้</h1>
                <form action="" method="POST" class="mx-auto my-6">
                    <div class="mb-5">
                        <label for="username" class="block mb-2 text-sm font-medium text-gray-900">ชื่อผู้ใช้</label>
                        <input type="text" id="username" name="username" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="ชื่อผู้ใช้" required />
                    </div>
                    <div class="mb-5">
                        <label for="displayName" class="block mb-2 text-sm font-medium text-gray-900">ชื่อจริง</label>
                        <input type="text" id="displayName" name="displayName" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="คุณ...." required />
                    </div>
                    <div class="mb-5">
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900">รหัสผ่าน</label>
                        <input type="password" id="password" name="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                    </div>
                    <div class="mb-5">
                        <label for="confirmPassword" class="block mb-2 text-sm font-medium text-gray-900">ยืนยันรหัสผ่าน</label>
                        <input type="password" id="confirmPassword" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                    </div>
                    <div class="mb-5">
                        <label class="inline-flex items-center mr-3 cursor-pointer">
                            <input type="checkbox" name="canPostCourse" value="1" class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">สามารถเพิ่มหลักสูตรได้</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="canManageUser" value="1" class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">สามารถจัดการผู้ใช้ได้</span>
                        </label>
                    </div>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">เพิ่มผู้ใช้</button>
                </form>
            </div>
        </div>
    </main>
</body>

</html>

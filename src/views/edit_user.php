<?php 
require_once './link/title.php'; 
require_once './config/db.php';
require_once './functions/Users/User.php';
global $conn;

$userData = getUser($conn, $id);

if($userData['id'] == 1){
    header("location: /");
}

if(!isset($_SESSION['userId'])) {
    header("location: /");
} else {
    $userId = $_SESSION['userId'];
    $user = getUser($conn, $userId);
    $canPost = $user['canPostCourse'];
    $manageUser = $user['canManageUser'];
    if($manageUser == 0) {
        header("location: /");
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = isset($_POST['username']) ? $_POST['username'] : $user['username'];
    $displayName = isset($_POST['displayName']) ? $_POST['displayName'] : $user['displayName'];
    $password = isset($_POST['password']) && $_POST['password'] != '' ? $_POST['password'] : $user['password'];
    $canPostCourse = isset($_POST['canPostCourse']) && $_POST['canPostCourse'] != 'null' ? (int)$_POST['canPostCourse'] : 0;
    $canManageUser = isset($_POST['canManageUser']) && $_POST['canManageUser'] != 'null' ? (int)$_POST['canManageUser'] : 0;

    $data = array (
        "id" => $id,
        "username" => $username,
        "displayName" => $displayName,
        "password" => $password,
        "canPostCourse" => $canPostCourse,
        "canManageUser" => $canManageUser,
    );
    $user = editUser($conn, $data);
    if($user){
        $_SESSION["alert"] = '<script>
            Swal.fire({
                title: "แก้ไขผู้ใช้เรียบร้อย!",
                text: "หากต้องการเปลี่ยนแปลงข้อมูล สามารถแก้ไขได้ตลอด",
                icon: "success",
                confirmButtonColor: "#5fe280",
                confirmButtonText: "โอเค",
            })
        </script>';
        $_SESSION["editUserInfo"] = array(
            "username" => $username,
        );
        header("Location: /?page=manage_users");
        exit();
    }
};

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php title('แก้ไขผู้ใช้'); ?>
    <?php require './link/styles.php' ?>
    <?php require './link/favicon.php' ?>
    <script src="/functions/Users/switchCheckbox.js"></script>
</head>

<body>
    <?php require_once './layout/Navbar.php' ?>
    <main>
        <div class="container px-4 md:px-12 mx-auto">
            <div class="shadow-lg p-4 md:p-10 border min-h-screen">
                <h1 class="text-2xl font-semibold">แก้ไขผู้ใช้</h1>
                <form action="" method="POST" class="mx-auto my-6">
                    <div class="mb-5">
                        <label for="username" class="block mb-2 text-sm font-medium text-gray-900">ชื่อผู้ใช้</label>
                        <input type="text" value="<?php echo $userData['username'] ?>" id="username" name="username" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="ชื่อผู้ใช้" required />
                    </div>
                    <div class="mb-5">
                        <label for="displayName" class="block mb-2 text-sm font-medium text-gray-900">ชื่อจริง</label>
                        <input type="text" value="<?php echo $userData['displayName'] ?>" id="displayName" name="displayName" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="คุณ...." required />
                    </div>
                    <div class="mb-5">
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900">รหัสผ่าน <span class="opacity-45">*ไม่จำเป็นต้องกรอกหากไม่ต้องการเปลี่ยนรหัสผ่าน</span></label>
                        <input type="password" id="password" name="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="••••••••••" />
                    </div>
                    <div class="mb-5">
                        <label for="confirmPassword" class="block mb-2 text-sm font-medium text-gray-900">ยืนยันรหัสผ่าน <span class="opacity-45">*ไม่จำเป็นต้องกรอกหากไม่ต้องการเปลี่ยนรหัสผ่าน</span></label>
                        <input type="password" id="confirmPassword" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="••••••••••" />
                    </div>
                    <div class="mb-5">
                        <label class="inline-flex items-center mr-3 cursor-pointer">
                            <input id="canPostCourseInput" type="checkbox" name="canPostCourse" <?php echo ($userData['canPostCourse'] == 1) ? "checked" : "" ?> value="1" class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">สามารถเพิ่มหลักสูตรได้</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input id="canManageUserInput" type="checkbox" name="canManageUser" <?php echo ($userData['canManageUser'] == 1) ? "checked" : "" ?> value="1" class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">สามารถจัดการผู้ใช้ได้</span>
                        </label>
                    </div>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">แก้ไข</button>
                </form>
            </div>
        </div>
    </main>
</body>

</html>
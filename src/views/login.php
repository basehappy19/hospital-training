<?php

require_once './link/title.php';
require_once './config/db.php';
require_once './functions/Users/User.php';
require_once './functions/Auth/Login.php';

global $conn;

if (isset($_SESSION['userId'])) {
    header("location: /");
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user = login($conn, $username);
    if ($user) {
        if (password_verify($password, $user[0]['password'])) {
            $_SESSION['userId'] = $user[0]['id'];
            updateLoginAt($conn, $user[0]['id']);
            header("Location: /");
            exit();
        } else {
            $_SESSION['failedAuth'] = '<div class="my-2">
                                    <p class="text-red-400 text-center">รหัสผ่านหรือชื่อผู้ใช้ไม่ถูกต้อง</p>
                                </div>';
            header("Location: /?page=login");
            exit();
        }
    } else {
        $_SESSION['failedAuth'] = '<div class="my-2">
                                    <p class="text-red-400 text-center">รหัสผ่านหรือชื่อผู้ใช้ไม่ถูกต้อง</p>
                                </div>';
        header("Location: /?page=login");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php title('ล็อคอิน'); ?>
    <?php require './link/styles.php' ?>
    <?php require './link/favicon.php' ?>
</head>

<body>
    <?php require_once './layout/Navbar.php' ?>
    <main class="flex min-h-screen flex-col justify-center">
        <div class="sm:mx-auto sm:w-full sm:max-w-lg">
            <div class="px-4 py-8 mx-auto">
                <div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 xl:p-0">
                    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                        <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                            ล็อคอิน
                        </h1>
                        <form class="space-y-4 md:space-y-6" action="" method="POST">
                            <div>
                                <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ชื่อผู้ใช้</label>
                                <input type="text" name="username" id="username" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="user" required="">
                            </div>
                            <div>
                                <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">รหัสผ่าน</label>
                                <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" required="">
                            </div>
                            <?php if (isset($_SESSION['failedAuth'])) {
                                echo $_SESSION['failedAuth']; unset($_SESSION['failedAuth']);
                            } ?>
                            <button type="submit" class="w-full text-white bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">เข้าสู่ระบบ</button>
                        </form>
                    </div>
                </div>
                <p class="mt-10 text-center text-sm text-gray-500">
                    ลืมรหัสผ่าน ?
                    <a href="#" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">ติดต่อผู้ดูแลระบบ</a>
                </p>
            </div>
        </div>
    </main>
</body>

</html>
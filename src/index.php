<?php
session_start();
require_once('./functions/Users/User.php');
require_once('./functions/Course/Course.php');
require_once('./functions/Hotel/Hotel.php');
require_once('./functions/Course/AttendsList.php');
require_once('./functions/Album/Album.php');
require_once('./config/db.php');

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

function load_view($view, $data = []) {
    extract($data);
    require __DIR__ . "/views/{$view}.php";
}

$page = isset($_GET['page']) ? $_GET['page'] : '';

switch ($page) {
    case '':
        load_view('home');
        break;
    
    case 'courses':
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        load_view('courses', ['id' => $id]);
        break;

    case 'course_detail':
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        load_view('course_detail', ['id' => $id]);
        break;

    case 'login':
        load_view('login');
        break;

    case 'logout':
        session_destroy();
        header("Location: /");
        break;

    case 'hotels':
        load_view('hotels');
        break;

    case 'hotel_detail':
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        load_view('hotel_detail', ['id' => $id]);
        break;

    case 'course_enroll':
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        load_view('course_enroll', ['id' => $id]);
        break;

    case 'course_attend_detail':
        $courseId = isset($_GET['courseId']) ? $_GET['courseId'] : null;
        $attendId = isset($_GET['attendId']) ? $_GET['attendId'] : null;
        load_view('detail_attend', ['courseId' => $courseId, 'attendId' => $attendId]);
        break;
    
    case 'course_attend_verify':
        $courseId = isset($_GET['courseId']) ? $_GET['courseId'] : null;
        $courseKey = isset($_GET['courseKey']) ? $_GET['courseKey'] : null;
        $enrollCode = isset($_GET['enrollCode']) ? $_GET['enrollCode'] : null;
        
        if(!isset($_SESSION['userId'])) {
            header("Location: /");
            exit();
        }
        
        global $conn;
        $attend = checkAttendPaymentUpdate($conn, $courseKey, $enrollCode);
        
        if($attend) {
            $update = updatePayment($conn, $courseKey, $enrollCode);
            if ($update) {
                $_SESSION["alert"] = "<script>
                Swal.fire({
                    title: \"ยืนยันการชำระเงินเรียบร้อย\",
                    text: \"" . $attend['fullname'] . " ยืนยันเรียบร้อย\",
                    icon: \"success\",
                    confirmButtonColor: \"#5fe280\",
                    confirmButtonText: \"โอเค\"
                });
                </script>";
                header("Location: /?page=course_detail&courseId=" . $courseId);
                exit();
            } else {
                $_SESSION["alert"] = "<script>
                Swal.fire({
                    title: \"ยืนยันการชำระเงินไม่สำเร็จ\",
                    text: \"ไม่พบชื่อนี้ หรือมีปัญหาบางอย่าง\",
                    icon: \"warning\",
                    confirmButtonColor: \"#d33\",
                    confirmButtonText: \"ลองใหม่\"
                });
                </script>";
                header("Location: /?page=courses");
                exit();
            }
        }
        break;

    case 'course_edit':
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        load_view('course_edit', ['id' => $id]);
        break;

    case 'hotel_edit':
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        load_view('hotel_edit', ['id' => $id]);
        break;

    case 'manage_users':
        load_view('manage_user');
        break;

    case 'manage_user_add':
        load_view('add_user');
        break;

    case 'manage_user_edit':
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if($id == 1){
            header("location: /");
        }
        load_view('edit_user', ['id' => $id]);
        break;

    case 'manage_user_remove':
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if($id == 1){
            header("location: /");
        }
        if (!isset($_SESSION['userId'])) {
            header("location: /index.php?page=login");
            exit();
        }
        global $conn;
        $user = getUser($conn, $id);
        if ($user) {
            $remove = RemoveUser($conn, $id);
            if ($remove) {
                $_SESSION["alert"] = "<script>Swal.fire({ title: \"ลบผู้ใช้สำเร็จ\", text: \"ได้ลบผู้ใช้เรียบร้อย\", icon: \"success\", confirmButtonColor: \"#5fe280\", confirmButtonText: \"โอเค\" });</script>";
                $_SESSION["removeUserInfo"] = array("username" => $user[0]['username']);
                header("Location: /index.php?page=manage_users");
                exit();
            } else {
                $_SESSION["alert"] = "<script>Swal.fire({ title: \"ลบผู้ใช้ไม่สำเร็จ\", text: \"ไม่พบผู้ใช้ หรือมีปัญหาบางอย่าง\", icon: \"warning\", confirmButtonColor: \"#5fe280\", confirmButtonText: \"โอเค\" });</script>";
                header("Location: /index.php?page=manage_users");
                exit();
            }
        } else {
            $_SESSION["alert"] = "<script>Swal.fire({ title: \"ลบผู้ใช้ไม่สำเร็จ\", text: \"ไม่พบผู้ใช้ หรือมีปัญหาบางอย่าง\", icon: \"warning\", confirmButtonColor: \"#5fe280\", confirmButtonText: \"โอเค\" });</script>";
            header("Location: /index.php?page=manage_users");
            exit();
        }

    case 'course_remove':
        $key = isset($_GET['key']) ? $_GET['key'] : null;
        if (!isset($_SESSION['userId'])) {
            header("location: /index.php?page=login");
            exit();
        }
        global $conn;
        $course = getCourseByKeyForRemove($conn, $key);
        if ($course) {
            $remove = RemoveCourse($conn, $key);
            if ($remove) {
                $_SESSION["alert"] = "<script>Swal.fire({ title: \"ลบหลักสูตรสำเร็จ\", text: \"ได้ลบหลักสูตรเรียบร้อย\", icon: \"success\", confirmButtonColor: \"#5fe280\", confirmButtonText: \"โอเค\" });</script>";
                $_SESSION["removeCourseInfo"] = array("courseTitle" => $course['courseTitle']);
                header("Location: /index.php?page=courses");
                exit();
            } else {
                $_SESSION["alert"] = "<script>Swal.fire({ title: \"ลบหลักสูตรไม่สำเร็จ\", text: \"ไม่พบหลักสูตร หรือมีปัญหาบางอย่าง\", icon: \"warning\", confirmButtonColor: \"#d33\", confirmButtonText: \"โอเค\" });</script>";
                header("Location: /index.php?page=courses");
                exit();
            }
        } else {
            $_SESSION["alert"] = "<script>Swal.fire({ title: \"ลบหลักสูตรไม่สำเร็จ\", text: \"ไม่พบหลักสูตร หรือมีปัญหาบางอย่าง\", icon: \"warning\", confirmButtonColor: \"#d33\", confirmButtonText: \"โอเค\" });</script>";
            header("Location: /index.php?page=courses");
            exit();
        }

    case 'hotel_remove':
        $key = isset($_GET['key']) ? $_GET['key'] : null;
        if (!isset($_SESSION['userId'])) {
            header("location: /index.php?page=login");
            exit();
        }
        global $conn;
        $hotel = getHotelByKeyForRemove($conn, $key);
        if ($hotel) {
            $remove = removeHotel($conn, $key);
            if ($remove) {
                $_SESSION["alert"] = "<script>Swal.fire({ title: \"ลบโรงแรมสำเร็จ\", text: \"ได้ลบโรงแรมเรียบร้อย\", icon: \"success\", confirmButtonColor: \"#5fe280\", confirmButtonText: \"โอเค\" });</script>";
                $_SESSION["removeHotelInfo"] = array("hotelName" => $hotel['name']);
                header("Location: /index.php?page=hotels");
                exit();
            } else {
                $_SESSION["alert"] = "<script>Swal.fire({ title: \"ลบโรงแรมไม่สำเร็จ\", text: \"ไม่พบโรงแรม หรือมีปัญหาบางอย่าง\", icon: \"warning\", confirmButtonColor: \"#d33\", confirmButtonText: \"โอเค\" });</script>";
                header("Location: /index.php?page=hotels");
                exit();
            }
        } else {
            $_SESSION["alert"] = "<script>Swal.fire({ title: \"ลบโรงแรมไม่สำเร็จ\", text: \"ไม่พบโรงแรม หรือมีปัญหาบางอย่าง\", icon: \"warning\", confirmButtonColor: \"#d33\", confirmButtonText: \"โอเค\" });</script>";
            header("Location: /index.php?page=hotels");
            exit();
        }

    case 'fetchAlbum':
        load_view('fetchAlbum');
        break;

    case 'albums':
        load_view('albums');
        break;

    case 'album_share':
        $key = isset($_GET['key']) ? $_GET['key'] : null;
        load_view('album_photos', ['key' => $key]);
        break;

    case 'album_remove':
        $key = isset($_GET['key']) ? $_GET['key'] : null;
        if (!isset($_SESSION['userId'])) {
            header("location: /index.php?page=login");
            exit();
        }
        global $conn;
        $album = getAlbumByKeyForRemove($conn, $key);
        if ($album) {
            if (removeAlbum($conn, $key)) {
                $_SESSION["alert"] = "<script>
                Swal.fire({
                    title: \"ลบอัลบั้มสำเร็จ\",
                    text: \"ได้ลบอัลบั้มเรียบร้อย\",
                    icon: \"success\",
                    confirmButtonColor: \"#5fe280\",
                    confirmButtonText: \"โอเค\"
                });
                </script>";
                $_SESSION["removeAlbumInfo"] = array(
                    "albumName" => $album['albumName'],
                );
                header("Location: /index.php?page=albums");
                exit();
            } else {
                $_SESSION["alert"] = "<script>
                Swal.fire({
                    title: \"ลบอัลบั้มไม่สำเร็จ\",
                    text: \"ไม่พบอัลบั้ม หรือมีปัญหาบางอย่าง\",
                    icon: \"warning\",
                    confirmButtonColor: \"#d33\",
                    confirmButtonText: \"โอเค\"
                });
                </script>";
                header("Location: /index.php?page=albums");
                exit();
            }
        } else {
            $_SESSION["alert"] = "<script>
            Swal.fire({
                title: \"ลบอัลบั้มไม่สำเร็จ\",
                text: \"ไม่พบอัลบั้ม หรือมีปัญหาบางอย่าง\",
                icon: \"warning\",
                confirmButtonColor: \"#d33\",
                confirmButtonText: \"โอเค\"
            });
            </script>";
            header("Location: /index.php?page=albums");
            exit();
        }

    case 'album_image_remove':
        $albumKey = isset($_GET['albumKey']) ? $_GET['albumKey'] : null;
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if (!isset($_SESSION['userId'])) {
            header("location: /index.php?page=login");
            exit();
        }
        global $conn;
        $image = getImageByIdForRemove($conn, $albumKey, $id);
        if ($image) {
            if (removeImage($conn, $albumKey, $id, $image['ImageName'])) {
                $_SESSION["alert"] = "<script>
                Swal.fire({
                    title: \"ลบรูปภาพสำเร็จ\",
                    text: \"ได้ลบรูปภาพเรียบร้อย\",
                    icon: \"success\",
                    confirmButtonColor: \"#5fe280\",
                    confirmButtonText: \"โอเค\"
                });
                </script>";
                header("Location: /index.php?page=album_share&key={$albumKey}");
                exit();
            } else {
                $_SESSION["alert"] = "<script>
                Swal.fire({
                    title: \"ลบรูปภาพไม่สำเร็จ\",
                    text: \"ไม่พบรูปภาพ หรือมีปัญหาบางอย่าง\",
                    icon: \"warning\",
                    confirmButtonColor: \"#d33\",
                    confirmButtonText: \"โอเค\"
                });
                </script>";
                header("Location: /index.php?page=album_share&key={$albumKey}");
                exit();
            }
        } else {
            $_SESSION["alert"] = "<script>
            Swal.fire({
                title: \"ลบรูปภาพไม่สำเร็จ\",
                text: \"ไม่พบรูปภาพ หรือมีปัญหาบางอย่าง\",
                icon: \"warning\",
                confirmButtonColor: \"#d33\",
                confirmButtonText: \"โอเค\"
            });
            </script>";
            header("Location: /index.php?page=album_share&key={$albumKey}");
            exit();
        }
    case 'album_edit':
        $key = isset($_GET['key']) ? $_GET['key'] : null;
        load_view('album_edit', ['key' => $key]);
        break;

    case '404':
    default:
        http_response_code(404);
        load_view('404');
        break;
}
?>

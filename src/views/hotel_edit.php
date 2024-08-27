<?php
require_once './link/title.php';
require_once './config/db.php';
require_once './functions/Hotel/Hotel.php';
global $conn;

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

if (isset($id) && $id != '') {
    $hotelId = htmlspecialchars($id);
    $hotelDetails = getHotelById($conn, $hotelId);
    if (!$hotelDetails) {
        header('Location: /');
        exit();
    }
} else {
    header('Location: /');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $hotelKey = $hotelDetails['hotelKey'];
    $name = $_POST['name'];
    $roomSize = (int)isset($_POST['roomSize']) ? $_POST['roomSize'] : 0;
    $phone = isset($_POST['phone']) ? $_POST['phone'] : "";
    $price = isset($_POST['price']) ? $_POST['price'] : "";

    $singleBedded = (int)isset($_POST['singleBedded']) ? $_POST['singleBedded'] : 0;
    $twinBedded = (int)isset($_POST['twinBedded']) ? $_POST['twinBedded'] : 0;
    $kingSize = (int)isset($_POST['kingSize']) ? $_POST['kingSize'] : 0;

    $windows = (int)isset($_POST['windows']) ? $_POST['windows'] : 0;
    $freeWifi = (int)isset($_POST['freeWifi']) ? $_POST['freeWifi'] : 0;
    $airConditioner = (int)isset($_POST['airConditioner']) ? $_POST['airConditioner'] : 0;
    $privateBathroom = (int)isset($_POST['privateBathroom']) ? $_POST['privateBathroom'] : 0;
    $bath = (int)isset($_POST['bath']) ? $_POST['bath'] : 0;
    $fridge = (int)isset($_POST['fridge']) ? $_POST['fridge'] : 0;

    $geocode = $_POST['geocode'];
    $address = isset($_POST['address']) ? $_POST['address'] : "ไม่พบข้อมูล";
    $country = isset($_POST['country']) ? $_POST['country'] : "ไม่พบข้อมูล";
    $province = isset($_POST['province']) ? $_POST['province'] : "ไม่พบข้อมูล";
    $district = isset($_POST['district']) ? $_POST['district'] : "ไม่พบข้อมูล";
    $subdistrict = isset($_POST['subdistrict']) ? $_POST['subdistrict'] : "ไม่พบข้อมูล";
    $postcode = isset($_POST['postcode']) ? $_POST['postcode'] : "ไม่พบข้อมูล";
    $elevation = isset($_POST['elevation'])? $_POST['elevation'] : "ไม่พบข้อมูล";
    $road = isset($_POST['road']) ? $_POST['road'] : "ไม่พบข้อมูล";
    $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : "ไม่พบข้อมูล";
    $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : "ไม่พบข้อมูล";

    $thumbnail = $hotelDetails['hotelThumbnail'];

    $existingImages = $_POST['existing_images'] ?? [];
    $newImages = $_FILES['new_images'] ?? [];
    $currentImages = scandir("./public/hotels/images/{$hotelKey}");

    foreach ($currentImages as $image) {
        if ($image != '.' && $image != '..' && !in_array($image, $existingImages)) {
            unlink("./public/hotels/images/{$hotelKey}/" . $image);
        }
    }

    if (!empty($newImages['name'][0])) {
        foreach ($newImages['tmp_name'] as $key => $tmp_name) {
            $fileExtension = pathinfo($newImages['name'][$key], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $fileExtension;
            move_uploaded_file($tmp_name, "./public/hotels/images/{$hotelKey}/" . $fileName);
            $existingImages[] = $fileName;
        }
    }

    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = "./public/hotels/thumbnails/{$hotelKey}/";
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
    $hotel = array(
        "hotelKey" => $hotelKey,
        "name" => $name,
        "roomSize" => $roomSize,
        "phone" => $phone,
        "price" => $price,
        "beds" => [
            "singleBedded" => $singleBedded,
            "twinBedded" => $twinBedded,
            "kingSize" => $kingSize,
        ],
        "services" => [
            "windows" => $windows,
            "freeWifi" => $freeWifi,
            "airConditioner" => $airConditioner,
            "privateBathroom" => $privateBathroom,
            "bath" => $bath,
            "fridge" => $fridge,
        ],
        "hotelThumbnail" => $thumbnail,
        "location" => [
            "geocode" => $geocode,
            "address" => $address,
            "country" => $country,
            "province" => $province,
            "district" => $district,
            "subdistrict" => $subdistrict,
            "postcode" => $postcode,
            "elevation" => $elevation,
            "road" => $road,
            "latitude" => $latitude,
            "longitude" => $longitude,
        ],
        "hotelImages" => $existingImages,
    );
    if (updateHotel($conn, $hotel['hotelKey'], $hotel)) {
        $_SESSION["alert"] = '<script>
            Swal.fire({
                title: "แก้ไขข้อมูลโรงแรมเรียบร้อย!",
                text: "สามารถแก้ไขข้อมูลได้ตลอด",
                icon: "success",
                confirmButtonColor: "#5fe280",
                confirmButtonText: "โอเค",
            })
        </script>';
        $_SESSION["updateHotelInfo"] = array(
            "hotelName" => $name
        );
        header("Location: /?page=hotel_detail&id={$hotelDetails['id']}");
        exit();
    };
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if ($hotelDetails) : ?>
        <?php title($hotelDetails['name']); ?>
    <?php else : ?>
        <?php title('Course Not Found'); ?>
    <?php endif; ?>
    <?php require './link/styles.php' ?>
    <?php require './link/favicon.php' ?>
    <script src="/functions/preview.js"></script>
    <script src="https://api.longdo.com/map/?key=04af67a657fef741145c00d9249a12e7"></script>
    <script src="/lib/sweetalert2.all.min.js"></script>
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
                    <h1 class="text-2xl font-medium text-white text-center bg-cyan-400 p-4"><?php echo $hotelDetails['name'] ?></h1>
                </div>
                <div class="md:p-10 p-4">
                    <form class="mx-auto" action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-5">
                            <h3 for="dropzone-file" class="mb-4 font-semibold text-gray-900">ภาพปกโรงแรม</h3>
                            <div class="flex justify-center" id="imgBefore">
                                <img class="max-w-[350px] object-fit" src="/public/hotels/thumbnails/<?php echo $hotelDetails['hotelKey'] ?>/<?php echo $hotelDetails['hotelThumbnail']; ?>" alt="<?php echo $hotelDetails['hotelThumbnail']; ?>">
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
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">ชื่อโรงแรม</label>
                            <input type="text" onkeydown="clearBorder(this)" id="name" name="name" value="<?php echo $hotelDetails['name'] ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="ชื่อโรงแรม" required />
                        </div>
                        <div class="mb-5">
                            <label for="phone" class="block mb-2 text-sm font-medium text-gray-900">เบอร์โทรศัพท์</label>
                            <input type="number" onkeydown="clearBorder(this)" id="phone" name="phone" value="<?php echo $hotelDetails['phone'] ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="เบอร์โทรศัพท์" required />
                        </div>
                        <div class="mb-5">
                            <h1 class="mb-3">รายละเอียดเพิ่มเติม <span class="opacity-60">(*ไม่จำเป็นต้องกรอก)</span></h1>
                            <div class="mb-5">
                                <label for="roomSize" class="block mb-2 text-sm font-medium text-gray-900">ขนาดห้อง (ตร.ม)</label>
                                <input type="number" onkeydown="clearBorder(this)" id="roomSize" name="roomSize" value="<?php echo $hotelDetails['roomSize'] ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="ตร.ม" required />
                            </div>
                            <div class="mb-5">
                                <label for="singleBedded" class="block mb-2 text-sm font-medium text-gray-900">เตียงเดี่ยว (จำนวน)</label>
                                <input type="number" onkeydown="clearBorder(this)" id="singleBedded" value="<?php echo $hotelDetails['singleBedded'] ?>" name="singleBedded" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="เตียงเดี่ยว" required />
                            </div>
                            <div class="mb-5">
                                <label for="twinBedded" class="block mb-2 text-sm font-medium text-gray-900">เตียงคู่ (จำนวน)</label>
                                <input type="number" onkeydown="clearBorder(this)" id="twinBedded" value="<?php echo $hotelDetails['twinBedded'] ?>" name="twinBedded" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="เตียงคู่" required />
                            </div>
                            <div class="mb-5">
                                <label for="kingSize" class="block mb-2 text-sm font-medium text-gray-900">เตียงคิงไซส์ (จำนวน)</label>
                                <input type="number" onkeydown="clearBorder(this)" id="kingSize" value="<?php echo $hotelDetails['kingSize'] ?>" name="kingSize" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="เตียงคิงไซส์" required />
                            </div>
                            <h1 class="mb-3">สิ่งอำนวยความสะดวก <span class="opacity-60">(*ไม่จำเป็นต้องกรอก)</span></h1>
                            <div class="mb-5">
                                <label class="inline-flex items-center mr-3 cursor-pointer">
                                    <input type="checkbox" name="windows" <?php echo ($hotelDetails['windows'] == 1) ? "checked" : "" ?> value="1" class="sr-only peer">
                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">มีหน้าต่าง</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="freeWifi" <?php echo ($hotelDetails['freeWifi'] == 1) ? "checked" : "" ?> value="1" class="sr-only peer">
                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Wi-Fi ฟรี</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="airConditioner" <?php echo ($hotelDetails['airConditioner'] == 1) ? "checked" : "" ?> value="1" class="sr-only peer">
                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">เครื่องปรับอากาศ</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="privateBathroom" <?php echo ($hotelDetails['privateBathroom'] == 1) ? "checked" : "" ?> value="1" class="sr-only peer">
                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">ห้องอาบน้ำส่วนตัว</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="bath" <?php echo ($hotelDetails['bath'] == 1) ? "checked" : "" ?> value="1" class="sr-only peer">
                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">อ่างอาบน้ำ</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="fridge" <?php echo ($hotelDetails['fridge'] == 1) ? "checked" : "" ?> value="1" class="sr-only peer">
                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">ตู้เย็น</span>
                                </label>
                            </div>
                            <hr>
                        </div>
                        <div class="mb-5 flex items-end space-x-4">
                            <div class="flex-1">
                                <label for="latitude" class="block mb-2 text-sm font-medium text-gray-900">ละติจูด</label>
                                <input type="text" onkeydown="clearBorder(this)" id="latitude" name="latitude" value="<?php echo $hotelDetails['latitude'] ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="ตำแหน่งละติจูด" required />
                            </div>
                            <div class="flex-1">
                                <label for="longitude" class="block mb-2 text-sm font-medium text-gray-900">ลองติจูด</label>
                                <input type="text" onkeydown="clearBorder(this)" id="longitude" name="longitude" value="<?php echo $hotelDetails['longitude'] ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="ตำแหน่งลองติจูด" required />
                            </div>
                            <div>
                                <button type="button" onclick="searchLocation()" class="transition-all text-white bg-purple-500 hover:bg-purple-600 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center h-[42px]">ค้นหาโรงแรม</button>
                                <input type="hidden" value="<?php echo $hotelDetails['geocode'] ?>" id="geocode" name="geocode">
                                <input type="hidden" value="<?php echo $hotelDetails['country'] ?>" id="country" name="country">
                                <input type="hidden" value="<?php echo $hotelDetails['district'] ?>" id="district" name="district">
                                <input type="hidden" value="<?php echo $hotelDetails['elevation'] ?>" id="elevation" name="elevation">
                                <input type="hidden" value="<?php echo $hotelDetails['postcode'] ?>" id="postcode" name="postcode">
                                <input type="hidden" value="<?php echo $hotelDetails['province'] ?>" id="province" name="province">
                                <input type="hidden" value="<?php echo $hotelDetails['subdistrict'] ?>" id="subdistrict" name="subdistrict">
                                <input type="hidden" value="<?php echo $hotelDetails['road'] ?>" id="road" name="road">
                            </div>
                        </div>
                        <div class="mb-5 hidden" id="mapContainer">
                            <label for="map" class="block mb-2 text-sm font-medium text-gray-900">ที่ตั้งบน Maps</label>
                            <div id="map" class="w-full h-64 bg-gray-200 rounded-lg"></div>
                        </div>
                        <div id="address" class="mb-5 bg-red-200 p-2 text-gray-700 rounded-sm">
                            <div class="flex items-center gap-1">
                                <div id="notPinned">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-exclamation-circle">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M17 3.34a10 10 0 1 1 -15 8.66l.005 -.324a10 10 0 0 1 14.995 -8.336m-5 11.66a1 1 0 0 0 -1 1v.01a1 1 0 0 0 2 0v-.01a1 1 0 0 0 -1 -1m0 -7a1 1 0 0 0 -1 1v4a1 1 0 0 0 2 0v-4a1 1 0 0 0 -1 -1" />
                                    </svg>
                                </div>
                                <div id="pinned" class="hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-map">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 7l6 -3l6 3l6 -3v13l-6 3l-6 -3l-6 3v-13" />
                                        <path d="M9 4v13" />
                                        <path d="M15 7v13" />
                                    </svg>
                                </div>
                                <div id="address-text">ยังไม่ได้ปักหมุดโรงแรม</div>
                            </div>
                            <input type="hidden" name="address" id="address-value">
                        </div>
                        <div class="mb-5">
                            <label for="price" class="block mb-2 text-sm font-medium text-gray-900">ราคา</label>
                            <input type="text" onkeydown="clearBorder(this)" id="price" name="price" value="<?php echo $hotelDetails['price'] ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required placeholder="สอบถามโรงแรม" />
                        </div>
                        <div class="mb-5">
                            <h3 for="dropzone-images" class="mb-4 font-semibold text-gray-900">ภาพรายละเอียดห้องพัก</h3>
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
                            <?php foreach ($hotelDetails['hotelImages'] as $index => $hotelImage) : ?>
                                <div class="w-full md:w-1/2 p-1 existing-image" data-index="<?php echo $index; ?>">
                                    <div class="transition-all duration-300 ease-in-out hover:drop-shadow-lg relative overflow-hidden rounded-lg">
                                        <img class="transition-transform duration-300 ease-in-out transform hover:scale-[1.05] w-full h-full object-cover border border-[#DDDDDD]" src="/public/hotels/images/<?php echo $hotelDetails['hotelKey'] ?>/<?php echo $hotelImage; ?>" alt="<?php echo $hotelImage; ?>">
                                        <button type="button" class="delete-image absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center cursor-pointer" onclick="removeExistingImage(<?php echo $index; ?>)">×</button>
                                    </div>
                                </div>
                                <input type="hidden" name="existing_images[]" value="<?php echo $hotelImage; ?>" />
                            <?php endforeach; ?>
                        </div>
                        <button type="submit" class="transition-all ease-in-out duration-300 w-full text-white bg-purple-500 hover:bg-purple-600 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">แก้ไขโรงแรม</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <script src="/functions/Hotel/Maps.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (!empty($hotelDetails['latitude']) && !empty($hotelDetails['longitude'])) : ?>
                searchLocation();
            <?php endif; ?>
        });
    </script>
</body>

</html>
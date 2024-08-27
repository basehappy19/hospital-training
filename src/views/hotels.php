<?php
require_once './link/title.php';
require_once './functions/lib/GenerateKey.php';
require_once './functions/lib/Files.php';
require_once './functions/Hotel/Hotel.php';

global $conn;
$view = 0;
if (isset($_SESSION['userId'])) {
    $view = 1;
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == UPLOAD_ERR_OK) {
    $hotelKey = generateKey();
    $folder = "./public/hotels/images/{$hotelKey}/";
    createFolder($folder);
    createFolder("./public/hotels/thumbnails/{$hotelKey}/");
    $filesName = [];
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $files = $_FILES['images'];
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
        }
    }

    $name = $_POST['name'];
    $roomSize = (int)isset($_POST['roomSize']) && strlen($_POST['roomSize']) > 0 ? $_POST['roomSize'] : 0;
    $phone = $_POST['phone'];
    $price = isset($_POST['price']) ? $_POST['price'] : "";

    $singleBedded = (int)isset($_POST['singleBedded']) && strlen($_POST['singleBedded']) > 0 ? $_POST['singleBedded'] : 0;
    $twinBedded = (int)isset($_POST['twinBedded']) && strlen($_POST['twinBedded']) > 0 ? $_POST['twinBedded'] : 0;
    $kingSize = (int)isset($_POST['kingSize']) && strlen($_POST['kingSize']) > 0 ? $_POST['kingSize'] : 0;

    $windows = (int)isset($_POST['windows']) && strlen($_POST['windows']) > 0 ? $_POST['windows'] : 0;
    $freeWifi = (int)isset($_POST['freeWifi']) && strlen($_POST['freeWifi']) > 0 ? $_POST['freeWifi'] : 0;
    $airConditioner = (int)isset($_POST['airConditioner']) && strlen($_POST['airConditioner']) > 0 ? $_POST['airConditioner'] : 0;
    $privateBathroom = (int)isset($_POST['privateBathroom']) && strlen($_POST['privateBathroom']) > 0 ? $_POST['privateBathroom'] : 0;
    $bath = (int)isset($_POST['bath']) && strlen($_POST['bath']) > 0 ? $_POST['bath'] : 0;
    $fridge = (int)isset($_POST['fridge']) && strlen($_POST['fridge']) > 0 ? $_POST['fridge'] : 0;

    $geocode = $_POST['geocode'];
    $address = isset($_POST['address']) && strlen($_POST['address']) > 0 ? $_POST['address'] : "ไม่พบข้อมูล";
    $country = isset($_POST['country']) && strlen($_POST['country']) > 0 ? $_POST['country'] : "ไม่พบข้อมูล";
    $province = isset($_POST['province']) && strlen($_POST['province']) > 0 ? $_POST['province'] : "ไม่พบข้อมูล";
    $district = isset($_POST['district']) && strlen($_POST['district']) > 0 ? $_POST['district'] : "ไม่พบข้อมูล";
    $subdistrict = isset($_POST['subdistrict']) && strlen($_POST['subdistrict']) > 0 ? $_POST['subdistrict'] : "ไม่พบข้อมูล";
    $postcode = isset($_POST['postcode']) && strlen($_POST['postcode']) > 0 ? $_POST['postcode'] : "ไม่พบข้อมูล";
    $elevation = isset($_POST['elevation']) && strlen($_POST['elevation']) > 0 ? $_POST['elevation'] : "ไม่พบข้อมูล";
    $road = isset($_POST['road']) && strlen($_POST['road']) > 0 ? $_POST['road'] : "ไม่พบข้อมูล";
    $latitude = isset($_POST['latitude']) && strlen($_POST['latitude']) > 0 ? $_POST['latitude'] : "ไม่พบข้อมูล";
    $longitude = isset($_POST['longitude']) && strlen($_POST['longitude']) > 0 ? $_POST['longitude'] : "ไม่พบข้อมูล";

    $thumbnail = upload($_FILES['thumbnail'], "./public/hotels/thumbnails/{$hotelKey}/", ['image/jpeg', 'image/png', 'image/jpg']);

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
        "hotelImages" => $filesName,
    );
    if (newHotel($conn, $hotel['hotelKey'], $hotel)) {
        $_SESSION["alert"] = '<script>
                Swal.fire({
                    title: "เพิ่มโรงแรมเรียบร้อย!",
                    text: "สามารถแก้ไขข้อมูลได้ตลอด",
                    icon: "success",
                    confirmButtonColor: "#5fe280",
                    confirmButtonText: "โอเค",
                })
            </script>';
        $_SESSION["newHotelInfo"] = array(
            "hotelName" => $name
        );
    } else {
        $_SESSION["alert"] = '<script>
                Swal.fire({
                    title: "เพิ่มโรงแรมไม่ได้",
                    text: "มีปัญหาบางอย่างเกิดขึ้น",
                    icon: "warning",
                    confirmButtonColor: "#d33",
                    confirmButtonText: "ลองใหม่",
                })
            </script>';
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['clearSessionNewHotel'])) {
    unset($_SESSION['newHotelInfo']);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['clearSessionRemove'])) {
    unset($_SESSION['removeHotelInfo']);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php title('โรงแรมที่พัก'); ?>
    <?php require './link/styles.php' ?>
    <?php require './link/favicon.php' ?>
    <script src="/functions/preview.js"></script>
    <script src="https://api.longdo.com/map/?key=04af67a657fef741145c00d9249a12e7"></script>
    <script src="/lib/sweetalert2.all.min.js"></script>
</head>

<body>
    <?php require_once './layout/Navbar.php' ?>
    <?php
    if (isset($_SESSION['alert'])) {
        echo $_SESSION['alert'];
        unset($_SESSION['alert']);
    }
    ?>
    <main>
        <div class="container px-4 md:px-12 mx-auto">
            <div class="shadow-lg p-4 md:p-10 border min-h-screen">
                <h1 class="text-2xl font-semibold">โรงแรมที่พัก</h1>
                <div class="my-5">
                    <?php if (isset($_SESSION['userId'])) {
                        require_once './components/Hotel/AddHotel.php';
                    } ?>
                </div>
                <?php if (isset($_SESSION['removeHotelInfo'])) : ?>
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
                                ลบโรงแรม "<?php echo $_SESSION['removeHotelInfo']['hotelName'] ?>" เรียบร้อย
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['newHotelInfo'])) : ?>
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
                                <button type="submit" name="clearSessionNewHotel" class="transition-all md:absolute right-2 top-2 bg-cyan-300 hover:bg-cyan-500 text-black font-bold p-2 rounded">
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
                                ได้เพิ่มโรงแรม "<?php echo $_SESSION['newHotelInfo']['hotelName'] ?>" เรียบร้อย
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <?php require './components/Hotel/HotelCard.php' ?>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
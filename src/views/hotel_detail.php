<?php
require_once './link/title.php';
require_once './config/db.php';
require_once './functions/Hotel/Hotel.php';
global $conn;

if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
    $user = getUser($conn, $userId);
    $canPost = $user['canPostCourse'];
    $manageUser = $user['canManageUser'];
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

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['clearSessionUpdate'])) {
    unset($_SESSION['updateHotelInfo']);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
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
        <?php title('Hotel Not Found'); ?>
    <?php endif; ?>
    <?php require './link/styles.php' ?>
    <?php require './link/favicon.php' ?>
    <script src="/lib/sweetalert2.all.min.js"></script>
    <script src="/functions/Hotel/Remove.js"></script>
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
        <div class="container px-4 md:px-12 mx-auto">
            <div class="shadow-lg md:p-10 border min-h-screen">
                <div>
                    <h1 class="text-2xl font-medium text-white text-center bg-cyan-400 p-4"><?php echo $hotelDetails['name'] ?></h1>
                </div>
                <div class="md:p-10 p-4">
                    <?php if ($hotelDetails) : ?>
                        <div class="md:flex w-full items-center justify-center mb-3">
                            <img class="md:w-1/2 object-fit" src="/public/hotels/thumbnails/<?php echo $hotelDetails['hotelKey']; ?>/<?php echo $hotelDetails['hotelThumbnail']; ?>" alt="<?php echo $hotelDetails['hotelThumbnail']; ?>">
                        </div>
                        <?php if (isset($_SESSION['userId']) && $canPost == 1) : ?>
                            <div class="flex justify-end items-center mb-5">
                                <div class="cursor-pointer font-semibold text-green-400 hover:text-green-500 transition-all p-2 rounded-lg">
                                    <a href="/?page=hotel_edit&id=<?php echo $hotelDetails['id'] ?>">
                                        แก้ไข
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="inline-flex icon icon-tabler icons-tabler-filled icon-tabler-settings">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M14.647 4.081a.724 .724 0 0 0 1.08 .448c2.439 -1.485 5.23 1.305 3.745 3.744a.724 .724 0 0 0 .447 1.08c2.775 .673 2.775 4.62 0 5.294a.724 .724 0 0 0 -.448 1.08c1.485 2.439 -1.305 5.23 -3.744 3.745a.724 .724 0 0 0 -1.08 .447c-.673 2.775 -4.62 2.775 -5.294 0a.724 .724 0 0 0 -1.08 -.448c-2.439 1.485 -5.23 -1.305 -3.745 -3.744a.724 .724 0 0 0 -.447 -1.08c-2.775 -.673 -2.775 -4.62 0 -5.294a.724 .724 0 0 0 .448 -1.08c-1.485 -2.439 1.305 -5.23 3.744 -3.745a.722 .722 0 0 0 1.08 -.447c.673 -2.775 4.62 -2.775 5.294 0zm-2.647 4.919a3 3 0 1 0 0 6a3 3 0 0 0 0 -6z" />
                                        </svg>
                                    </a>
                                </div>
                                <div class="cursor-pointer font-semibold text-red-400 hover:text-red-500 transition-all p-2 rounded-lg">
                                    <a onclick="removeHotel('/?page=hotel_remove&key=<?php echo $hotelDetails['hotelKey'] ?>')">
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
                        <hr>
                        <div class="my-5" id="detail">
                            <?php if (isset($_SESSION['updateHotelInfo'])) : ?>
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
                                            แก้ไขข้อมูลโรงแรม "<?php echo $_SESSION['updateHotelInfo']['hotelName'] ?>" เรียบร้อย
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="mb-5">
                                <h1 class="text-2xl font-bold mb-2">รายละเอียดโรงแรม</h1>
                                <div class="text-lg font-bold text-cyan-600 mb-2">ราคา <?php echo $hotelDetails['price'] ?></div>

                                <?php if ($hotelDetails['roomSize'] > 0) : ?>
                                    <div class="mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-5 h-5 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 5h11"></path>
                                            <path d="M12 7l2-2-2-2"></path>
                                            <path d="M5 3L3 5l2 2"></path>
                                            <path d="M19 10v11"></path>
                                            <path d="M17 19l2 2 2-2"></path>
                                            <path d="M21 12l-2-2-2 2"></path>
                                            <path d="M3 10m0 2a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-7a2 2 0 0 1-2-2z"></path>
                                        </svg>
                                        <span class="font-semibold">ขนาดห้อง: <?php echo $hotelDetails['roomSize'] ?> ตร.ม</span>
                                    </div>
                                <?php endif; ?>
                                <div class="flex flex-wrap gap-2 mb-2">
                                    <?php if ($hotelDetails['singleBedded'] > 0) : ?>
                                        <span class="bg-gray-100 px-2 py-1 rounded-full text-sm">เตียงเดี่ยว x<?php echo $hotelDetails['singleBedded'] ?></span>
                                    <?php endif; ?>
                                    <?php if ($hotelDetails['twinBedded'] > 0) : ?>
                                        <span class="bg-gray-100 px-2 py-1 rounded-full text-sm">เตียงคู่ x<?php echo $hotelDetails['twinBedded'] ?></span>
                                    <?php endif; ?>
                                    <?php if ($hotelDetails['kingSize'] > 0) : ?>
                                        <span class="bg-gray-100 px-2 py-1 rounded-full text-sm">เตียงคิงไซส์ x<?php echo $hotelDetails['kingSize'] ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex flex-wrap gap-x-3">
                                    <?php if ($hotelDetails['windows'] == 1) : ?>
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-window">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M12 3c-3.866 0 -7 3.272 -7 7v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1 -1v-10c0 -3.728 -3.134 -7 -7 -7z" />
                                                <path d="M5 13l14 0" />
                                                <path d="M12 3l0 18" />
                                            </svg>
                                            <span>มีหน้าต่าง</span>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($hotelDetails['freeWifi'] == 1) : ?>
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-wifi">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M12 18l.01 0" />
                                                <path d="M9.172 15.172a4 4 0 0 1 5.656 0" />
                                                <path d="M6.343 12.343a8 8 0 0 1 11.314 0" />
                                                <path d="M3.515 9.515c4.686 -4.687 12.284 -4.687 17 0" />
                                            </svg>
                                            <span>Wi-Fi ฟรี</span>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($hotelDetails['airConditioner'] == 1) : ?>
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-air-conditioning">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M8 16a3 3 0 0 1 -3 3" />
                                                <path d="M16 16a3 3 0 0 0 3 3" />
                                                <path d="M12 16v4" />
                                                <path d="M3 5m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                                                <path d="M7 13v-3a1 1 0 0 1 1 -1h8a1 1 0 0 1 1 1v3" />
                                            </svg>
                                            <span>เครื่องปรับอากาศ</span>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($hotelDetails['privateBathroom'] == 1) : ?>
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-ripple">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M3 7c3 -2 6 -2 9 0s6 2 9 0" />
                                                <path d="M3 17c3 -2 6 -2 9 0s6 2 9 0" />
                                                <path d="M3 12c3 -2 6 -2 9 0s6 2 9 0" />
                                            </svg>
                                            <span>ห้องอาบน้ำส่วนตัว</span>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($hotelDetails['bath'] == 1) : ?>
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-bath">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 12h16a1 1 0 0 1 1 1v3a4 4 0 0 1 -4 4h-10a4 4 0 0 1 -4 -4v-3a1 1 0 0 1 1 -1z" />
                                                <path d="M6 12v-7a2 2 0 0 1 2 -2h3v2.25" />
                                                <path d="M4 21l1 -1.5" />
                                                <path d="M20 21l-1 -1.5" />
                                            </svg>
                                            <span>อ่างอาบน้ำ</span>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($hotelDetails['fridge'] == 1) : ?>
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-fridge">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M5 3m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
                                                <path d="M5 10h14" />
                                                <path d="M9 13v3" />
                                                <path d="M9 6v1" />
                                            </svg>
                                            <span>ตู้เย็น</span>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-phone">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
                                        </svg>
                                    </span>
                                    <span>เบอร์โทรศัพท์ : <a class="text-blue-500 hover:underline" href="tel:<?php echo $hotelDetails['phone'] ?>"><?php echo $hotelDetails['phone'] ?></a></span>
                                </div>
                                <div class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 mt-1 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                    <span>ที่อยู่ : <?php echo $hotelDetails['address'] ?></span>
                                </div>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold mb-2">รูปภาพห้องพัก</h1>
                            </div>
                            <div class="mt-3 flex flex-wrap items-center justify-center">
                                <?php foreach ($hotelDetails['hotelImages'] as $hotelImage) : ?>
                                    <div class="w-full md:w-1/2 p-1">
                                        <div class="transition-all duration-300 ease-in-out hover:drop-shadow-lg relative overflow-hidden rounded-lg">
                                            <img class="transition-transform duration-300 ease-in-out transform hover:scale-[1.05] w-full h-full object-cover border border-[#DDDDDD]" src="/public/hotels/images/<?php echo $hotelDetails['hotelKey']; ?>/<?php echo $hotelImage; ?>" alt="<?php echo $hotelImage; ?>" srcset="">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <p>Hotel not found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
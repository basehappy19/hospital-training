<?php
require_once("./config/db.php");
require_once("./functions/Course/Course.php");

$view = 0;
if (isset($_SESSION['userId'])) {
    $view = 1;
}

if (isset($id)) {
    $courses = getCourseFilter($conn, $id, $view);
} else {
    $courses = getCourse($conn, $view);
}

if (!empty($courses)) : ?>
    <?php foreach ($courses as $course) : ?>
        <div class="group bg-white rounded-lg shadow-md overflow-hidden h-full flex flex-col">
            <h2 class="text-center rounded-t-lg text-white bg-purple-500"><?php echo $course['courseTitle']; ?></h2>
            <div class="relative z-0">
                <div class="image-container relative transition-all w-full h-48 overflow-hidden">
                    <img class="w-full h-full max-h-[500px] object-cover object-center image cursor-pointer" src="/public/courses/thumbnails/<?php echo $course['courseKey']; ?>/<?php echo $course['courseThumbnail']; ?>" alt="<?php echo $course['courseTitle']; ?>">
                    <div class="image-overlay cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="expand-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <polyline points="9 21 3 21 3 15"></polyline>
                            <line x1="21" y1="3" x2="14" y2="10"></line>
                            <line x1="3" y1="21" x2="10" y2="14"></line>
                        </svg>
                    </div>
                </div>
            </div>
            <div id="lightbox" class="lightbox fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-40 p-4">
                <div class="lightbox-content rounded-lg relative">
                    <button id="close-lightbox" class="rounded-lg bg-white transition-all ease-in-out text-red-500 font-medium hover:text-red-700 absolute top-2 right-2 z-40">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-x">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M18 6l-12 12" />
                            <path d="M6 6l12 12" />
                        </svg>
                    </button>
                    <img id="lightbox-img" class="transition-all max-w-full max-h-[80vh] rounded-lg">
                </div>
            </div>
            <div class="flex-grow">
                <div class="flex flex-col">
                    <div class="w-full bg-cyan-400 text-center font-semibold">อบรมวันที่ <?php echo convertGregorianDateToThai($course['courseStartEnrollDate']) ?> ถึง <?php echo convertGregorianDateToThai($course['courseEndEnrollDate']) ?></div>
                    <?php if ($view == 1 && $course['courseOpen'] == 0) : ?>
                        <div class="w-full py-2 bg-red-400 text-center font-semibold">ยังไม่ได้เปิดให้ลงทะเบียน มีเพียงแค่แอดมินที่เห็น ***</div>
                    <?php endif; ?>
                    <div class="flex flex-row">
                        <div class="group relative">
                            <div class="transition-all duration-500 w-0 h-full group-hover:w-5 bg-blue-600">
                                <div class="transition-all duration-500 flex items-center justify-center h-full transform -rotate-90 text-center opacity-0 group-hover:opacity-100">
                                    <span class="font-medium text-white whitespace-nowrap">รายละเอียด</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <div>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-category-2">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M14 4h6v6h-6z" />
                                        <path d="M4 14h6v6h-6z" />
                                        <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                        <path d="M7 7m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                    </svg>
                                </span>
                                <span class="font-medium">หมวดหมู่ : </span>
                                <span><?php echo $course['categoryTitle'] ?></span>
                            </div>
                            <div>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-calendar-event">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                        <path d="M16 3l0 4" />
                                        <path d="M8 3l0 4" />
                                        <path d="M4 11l16 0" />
                                        <path d="M8 15h2v2h-2z" />
                                    </svg>
                                </span>
                                <span class="font-medium">ลงทะเบียนวันที่ : </span>
                                <span><?php echo convertGregorianDateToThai($course['courseStartDate']) ?> ถึง <?php echo convertGregorianDateToThai($course['courseEndDate']) ?></span>
                            </div>
                            <div>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-coins">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 14c0 1.657 2.686 3 6 3s6 -1.343 6 -3s-2.686 -3 -6 -3s-6 1.343 -6 3z" />
                                        <path d="M9 14v4c0 1.656 2.686 3 6 3s6 -1.344 6 -3v-4" />
                                        <path d="M3 6c0 1.072 1.144 2.062 3 2.598s4.144 .536 6 0c1.856 -.536 3 -1.526 3 -2.598c0 -1.072 -1.144 -2.062 -3 -2.598s-4.144 -.536 -6 0c-1.856 .536 -3 1.526 -3 2.598z" />
                                        <path d="M3 6v10c0 .888 .772 1.45 2 2" />
                                        <path d="M3 11c0 .888 .772 1.45 2 2" />
                                    </svg>
                                </span>
                                <span class="font-medium">ค่าลงทะเบียน : </span>
                                <span>
                                    <?php
                                    if ($course['courseEnrollFee'] > 0) {
                                        echo $course['courseEnrollFee'] . " บาท";
                                    } else {
                                        echo "ฟรี";
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="<?php echo $course['participants'] == $course['courseLimit'] && $course['courseLimit'] != 0 ? "text-red-600" : "" ?>">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-users">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                        <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                    </svg>
                                </span>
                                <span class="font-medium">รับจำนวน : </span>
                                <?php if ($course['courseLimit'] != 0) {
                                    if ($course['participants'] <= $course['courseLimit']) : ?>
                                        <span><?php echo $course['participants'] ?>/<?php echo $course['courseLimit'] ?> คน</span>
                                    <?php endif;
                                } else { ?>
                                    <span>ไม่จำกัดจำนวนคน</span>
                                <?php } ?>
                            </div>
                            <div>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-target-arrow">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                        <path d="M12 7a5 5 0 1 0 5 5" />
                                        <path d="M13 3.055a9 9 0 1 0 7.941 7.945" />
                                        <path d="M15 6v3h3l3 -3h-3v-3z" />
                                        <path d="M15 9l-3 3" />
                                    </svg>
                                </span>
                                <span class="font-medium">กลุ่มเป้าหมาย : </span><span><?php echo $course['courseTarget'] ?></span>
                            </div>
                            <div>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-map-pin">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                                    </svg>
                                </span>
                                <span class="font-medium">สถานที่ : </span><span><?php echo $course['courseLocation'] ?></span>
                            </div>
                            <div>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-wifi">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 18l.01 0" />
                                        <path d="M9.172 15.172a4 4 0 0 1 5.656 0" />
                                        <path d="M6.343 12.343a8 8 0 0 1 11.314 0" />
                                        <path d="M3.515 9.515c4.686 -4.687 12.284 -4.687 17 0" />
                                    </svg>
                                </span>
                                <span class="font-medium">เปิดออนไลน์ : </span>
                                <span>
                                    <?php if ($course['courseOnline'] == 1) { ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500 inline-flex icon icon-tabler icons-tabler-outline icon-tabler-rosette-discount-check">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M5 7.2a2.2 2.2 0 0 1 2.2 -2.2h1a2.2 2.2 0 0 0 1.55 -.64l.7 -.7a2.2 2.2 0 0 1 3.12 0l.7 .7c.412 .41 .97 .64 1.55 .64h1a2.2 2.2 0 0 1 2.2 2.2v1c0 .58 .23 1.138 .64 1.55l.7 .7a2.2 2.2 0 0 1 0 3.12l-.7 .7a2.2 2.2 0 0 0 -.64 1.55v1a2.2 2.2 0 0 1 -2.2 2.2h-1a2.2 2.2 0 0 0 -1.55 .64l-.7 .7a2.2 2.2 0 0 1 -3.12 0l-.7 -.7a2.2 2.2 0 0 0 -1.55 -.64h-1a2.2 2.2 0 0 1 -2.2 -2.2v-1a2.2 2.2 0 0 0 -.64 -1.55l-.7 -.7a2.2 2.2 0 0 1 0 -3.12l.7 -.7a2.2 2.2 0 0 0 .64 -1.55v-1" />
                                            <path d="M9 12l2 2l4 -4" />
                                        </svg>
                                    <?php } else { ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-500 inline-flex icon icon-tabler icons-tabler-outline icon-tabler-circle-dashed-x">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95" />
                                            <path d="M3.69 8.56a9 9 0 0 0 -.69 3.44" />
                                            <path d="M3.69 15.44a9 9 0 0 0 1.95 2.92" />
                                            <path d="M8.56 20.31a9 9 0 0 0 3.44 .69" />
                                            <path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95" />
                                            <path d="M20.31 15.44a9 9 0 0 0 .69 -3.44" />
                                            <path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92" />
                                            <path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69" />
                                            <path d="M14 14l-4 -4" />
                                            <path d="M10 14l4 -4" />
                                        </svg>
                                    <?php } ?>
                                </span>
                            </div>
                            <div>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-building-estate">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 21h18" />
                                        <path d="M19 21v-4" />
                                        <path d="M19 17a2 2 0 0 0 2 -2v-2a2 2 0 1 0 -4 0v2a2 2 0 0 0 2 2z" />
                                        <path d="M14 21v-14a3 3 0 0 0 -3 -3h-4a3 3 0 0 0 -3 3v14" />
                                        <path d="M9 17v4" />
                                        <path d="M8 13h2" />
                                        <path d="M8 9h2" />
                                    </svg>
                                </span>
                                <span class="font-medium">เปิดออนไซต์ : </span>
                                <span>
                                    <?php if ($course['courseOnsite'] == 1) { ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500 inline-flex icon icon-tabler icons-tabler-outline icon-tabler-rosette-discount-check">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M5 7.2a2.2 2.2 0 0 1 2.2 -2.2h1a2.2 2.2 0 0 0 1.55 -.64l.7 -.7a2.2 2.2 0 0 1 3.12 0l.7 .7c.412 .41 .97 .64 1.55 .64h1a2.2 2.2 0 0 1 2.2 2.2v1c0 .58 .23 1.138 .64 1.55l.7 .7a2.2 2.2 0 0 1 0 3.12l-.7 .7a2.2 2.2 0 0 0 -.64 1.55v1a2.2 2.2 0 0 1 -2.2 2.2h-1a2.2 2.2 0 0 0 -1.55 .64l-.7 .7a2.2 2.2 0 0 1 -3.12 0l-.7 -.7a2.2 2.2 0 0 0 -1.55 -.64h-1a2.2 2.2 0 0 1 -2.2 -2.2v-1a2.2 2.2 0 0 0 -.64 -1.55l-.7 -.7a2.2 2.2 0 0 1 0 -3.12l.7 -.7a2.2 2.2 0 0 0 .64 -1.55v-1" />
                                            <path d="M9 12l2 2l4 -4" />
                                        </svg>
                                    <?php } else { ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-500 inline-flex icon icon-tabler icons-tabler-outline icon-tabler-circle-dashed-x">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95" />
                                            <path d="M3.69 8.56a9 9 0 0 0 -.69 3.44" />
                                            <path d="M3.69 15.44a9 9 0 0 0 1.95 2.92" />
                                            <path d="M8.56 20.31a9 9 0 0 0 3.44 .69" />
                                            <path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95" />
                                            <path d="M20.31 15.44a9 9 0 0 0 .69 -3.44" />
                                            <path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92" />
                                            <path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69" />
                                            <path d="M14 14l-4 -4" />
                                            <path d="M10 14l4 -4" />
                                        </svg>
                                    <?php } ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-gray-100 mt-auto">
                <div class="grid grid-cols-2 gap-2">
                    <?php if ($course['courseLimit'] != 0) {
                        if ($course['participants'] < $course['courseLimit']) { ?>
                            <a class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-4 rounded text-center text-sm flex items-center justify-center" href="?page=course_detail&id=<?php echo $course['id'] ?>">ดูรายละเอียด</a>
                            <a class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded text-center text-sm flex items-center justify-center" href="?page=course_enroll&id=<?php echo $course['id'] ?>">สมัครเลย</a>
                        <?php } else { ?>
                            <a class="rounded-b-lg transition-all py-2 bg-cyan-300 group-hover:bg-cyan-400 w-full text-center font-bold" href="?page=course_detail&id=<?php echo $course['id'] ?>">ดูรายละเอียด</a>
                        <?php } ?>
                    <?php } else { ?>
                        <a class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-4 rounded text-center text-sm flex items-center justify-center" href="?page=course_detail&id=<?php echo $course['id'] ?>">ดูรายละเอียด</a>
                        <a class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded text-center text-sm flex items-center justify-center" href="?page=course_enroll&id=<?php echo $course['id'] ?>">สมัครเลย</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php include './lib/image-container.php' ?>
<?php else : ?>
    <div class="my-5 col-span-2 w-full">
        <div class="bg-gray-100 p-8 rounded-lg text-center text-gray-500">
            ไม่พบหลักสูตรในขณะนี้
        </div>
    </div>
<?php endif; ?>
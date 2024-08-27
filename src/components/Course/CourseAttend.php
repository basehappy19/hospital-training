<?php
require_once './functions/Course/AttendsList.php';
global $conn;

$limit = 10;
$current_page = isset($_GET['page_row']) ? (int)$_GET['page_row'] : 1;
$current_page = max($current_page, 1);

$offset = ($current_page - 1) * $limit;
$courseId = htmlspecialchars($id);
$total_records = getTotalCourseAttends($conn, $courseDetails['courseKey']);
$total_pages = ceil($total_records / $limit);

$courseAttends = getCourseAttendsList($conn, $courseDetails['courseKey'], $limit, $offset);

?>
<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    ชื่อ - นามสกุล
                </th>
                <th scope="col" class="px-6 py-3">
                    เบอร์โทรศัพท์
                </th>
                <th scope="col" class="px-6 py-3">
                    อีเมล
                </th>
                <th scope="col" class="px-6 py-3">
                    หน่วยงาน
                </th>
                <th scope="col" class="px-6 py-3">
                    ลงทะเบียน
                </th>
                <th scope="col" class="px-6 py-3">
                    รับอาหาร
                </th>
                <th scope="col" class="px-6 py-3">
                    ลงทะเบียนเวลา
                </th>
                <th scope="col" class="px-6 py-3">
                    #
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($courseAttends)) { ?>
                <?php foreach ($courseAttends as $attend) : ?>
                    <tr class="odd:bg-white even:bg-gray-50 border-b">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            <?php echo $attend['fullname'] ?>
                        </th>
                        <td class="px-6 py-4 text-blue-500 font-medium">
                            <a href="tel:<?php echo $attend['phone'] ?>"><?php echo $attend['phone'] ?></a>
                        </td>
                        <td class="px-6 py-4 text-blue-500 font-medium">
                            <a href="mailto:<?php echo $attend['email'] ?>"><?php echo $attend['email'] ?></a>
                        </td>
                        <td class="px-6 py-4">
                            <?php echo $attend['institution'] ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php echo $attend['enrollType'] ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php echo $attend['foodType'] ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php
                            date_default_timezone_set('Asia/Bangkok');
                            $enrolTime = strtotime($attend['enrollTime']);
                            $formattedDate = date("d/m/Y H:i:s", strtotime('+543 year', $enrolTime));
                            echo $formattedDate;
                            ?>
                        </td>
                        <td class="px-6 py-4">
                            <a href="/?page=course_attend_detail&courseId=<?php echo $courseId ?>&attendId=<?php echo $attend['id'] ?>" class="font-medium text-blue-600 hover:underline">รายละเอียด</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php } else { ?>
                <tr class="odd:bg-white even:bg-gray-50 border-b">
                    <td class="w-full text-center p-4" colspan="8">
                        ไม่มีข้อมูลผู้สมัคร ณ ตอนนี้
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
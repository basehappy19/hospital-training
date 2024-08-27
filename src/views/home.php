<?php
require_once './link/title.php';
require_once './functions/Course/Course.php';
require_once './functions/Course/Category.php';
global $conn;
$view = 0;
if (isset($_SESSION['userId'])) {
    $view = 1;
}
$courses = getCourseHomePage($conn, $view);
$categories = getCategoryWithCourses($conn, $view);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php title('หน้าแรก'); ?>
    <?php require './link/favicon.php' ?>
    <?php require './link/styles.php' ?>
    <script src="/lib/sweetalert2.all.min.js"></script>
</head>

<body>
    <?php require_once './layout/Navbar.php' ?>
    <main>
        <div class="container px-4 md:px-12 mx-auto">
            <div class="shadow-lg p-4 md:p-10 border min-h-screen">
                <div class="md:flex gap-3">
                    <div class="w-full md:w-1/2 md:max-w-[400px] mx-auto">
                        <div class="flex flex-col">
                            <?php foreach ($categories as $category) : ?>
                                <div class="mb-3 text-center">
                                    <a href="/?page=courses&id=<?php echo $category['id'] ?>">
                                        <div class="bg-purple-300 py-2 border border-gray-500">
                                            <h1 class="text-xl font-semibold"><?php echo $category['categoryTitle'] ?></h1>
                                        </div>
                                    </a>
                                    <?php if (isset($category['courses']) && is_array($category['courses']) && !empty($category['courses'])) : ?>
                                        <?php foreach ($category['courses'] as $course) : ?>
                                            <a href="/?page=course_detail&id=<?php echo $course['id'] ?>">
                                                <div class="bg-purple-100 py-3 border border-gray-500">
                                                    <?php echo $course['title'] ?>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <div class="bg-gray-100 py-3 border border-gray-300 text-gray-500 text-center">
                                            ไม่มีหลักสูตรในหมวดหมู่นี้
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="my-6 md:my-0 w-full md:w-1/2">
                        <div class="flex flex-col">
                            <?php if (!empty($courses)) : ?>
                                <?php foreach ($courses as $course) : ?>
                                    <div class="mb-12">
                                        <a href="/?page=course_detail&id=<?php echo $course['id'] ?>">
                                            <div class="transition-all duration-300 ease-in-out hover:drop-shadow-lg relative overflow-hidden rounded-lg max-h-[500px] mb-3">
                                                <img class="transition-transform duration-300 ease-in-out transform hover:scale-[1.05] w-full h-full border border-[#DDDDDD] object-cover" src="/public/courses/thumbnails/<?php echo $course['courseKey'] ?>/<?php echo $course['courseThumbnail'] ?>" alt="course" srcset="">
                                            </div>
                                        </a>
                                        <div class="bg-purple-300 font-semibold text-center mb-3"><?php echo $course['courseTitle'] ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <div class="mb-12">
                                    <div class="bg-gray-100 p-8 rounded-lg text-center text-gray-500">
                                        ไม่พบหลักสูตรในขณะนี้
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
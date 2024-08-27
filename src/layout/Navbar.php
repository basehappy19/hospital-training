<?php
require_once('./config/db.php');
global $conn;
require_once('./functions/Users/User.php');

if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
    $user = getUser($conn, $userId);
    $canPost = $user['canPostCourse'];
    $manageUser = $user['canManageUser'];
}
?>
<style>
    .nav {
        padding: 1rem 0;
    }

    .nav-scrolled {
        padding: 0.1rem 0;
    }

    .logo-container {
        transition: all 0.3s ease-in-out;
    }

    .logo-image {
        transition: all 0.3s ease-in-out;
    }

    .logo-text {
        transition: opacity 0.3s ease-in-out, max-height 0.3s ease-in-out, margin 0.3s ease-in-out;
        opacity: 1;
        max-height: 100px;
        margin-left: 0.75rem;
    }

    .logo-text.hidden {
        opacity: 0;
        max-height: 0;
        margin-left: 0;
    }

    .logo-expanded {
        transform: scale(1.2);
    }

    #logoText {
        font-size: 18px; 
        max-width: calc(100vw - 120px); 
        word-wrap: break-word;
    }

    @media screen and (max-width: 767px) {
        #logoText {
            font-size: 14px; 
            max-width: calc(100vw - 120px); 
            word-wrap: break-word;
        }
    }

</style>
<script src="/functions/navbar.js"></script>
<nav id="navbar" class="ease-in-out p-2 nav fixed top-0 w-full z-30 bg-[#c3ebbd] border-[#84beb2] border-b-4 transition-all duration-300">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-start mx-auto gap-y-3 p-4">
        <div class="mb-5 md:mb-0 flex items-center gap-x-3">
            <a href="/" class="flex-shrink-0">
                <img id="logoImage" src="/public/logo.png?time=<?php echo date("Y/m/d") ?>" width="96px" class="transition-all ease-in-out duration-300" alt="Logo" />
            </a>
            <div id="logoText" class="font-semibold leading-7 transition-all ease-in-out duration-300 flex flex-col logo-text">
                <span>งานฝึกอบรม ถ่ายทอดวิชาการและรับรองมาตรฐานวิชาชีพ</span>
                <span>ด้าน การแพทย์ การพยาบาล และการสาธารณสุข</span>
                <span>โรงพยาบาลเมตตาประชารักษ์ (วัดไร่ขิง)</span>
            </div>
        </div>


        <button id="menu-button" type="button" class="inline-flex items-center p-2 w-10 h-10 text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15" />
            </svg>
        </button>

        <div class="hidden w-full md:block md:w-auto" id="navbar-dropdown">
            <ul class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 rounded-lg md:space-x-2 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0">
                <li class="transition-all bg-white border rounded-lg md:mb-0 mb-3 hover:bg-purple-300">
                    <a href="/" class="inline-block w-full md:w-auto font-semibold py-2 px-3 text-gray-900 rounded-lg md:border-0">หน้าแรก</a>
                </li>
                <li class="transition-all bg-white border rounded-lg md:mb-0 mb-3 hover:bg-purple-300">
                    <a href="/?page=courses" class="inline-block w-full md:w-auto font-semibold py-2 px-3 text-gray-900 rounded-lg md:border-0">หลักสูตร</a>
                </li>
                <li class="transition-all bg-white border rounded-lg md:mb-0 mb-3 hover:bg-purple-300">
                    <a href="/?page=albums" class="inline-block w-full md:w-auto font-semibold py-2 px-3 text-gray-900 rounded-lg md:border-0">อัลบั้มรูปภาพ</a>
                </li>
                <li class="transition-all bg-white border rounded-lg md:mb-0 mb-3 hover:bg-purple-300">
                    <a href="/?page=hotels" class="inline-block w-full md:w-auto font-semibold py-2 px-3 text-gray-900 rounded-lg md:border-0">ติดต่อโรงแรมที่พัก</a>
                </li>
                <li class="transition-all bg-white border rounded-lg md:mb-0 mb-3 hover:bg-purple-300">
                    <a href="/" class="inline-block w-full md:w-auto font-semibold py-2 px-3 text-gray-900 rounded-lg md:border-0">ติดต่อเจ้าหน้าที่</a>
                </li>
                <?php if (!isset($user)) : ?>
                    <li class="bg-white border rounded-lg md:mb-0 mb-3">
                        <a href="/?page=login" class="inline-block w-full md:w-auto font-semibold py-2 px-3 text-gray-900 rounded-lg md:border-0">เข้าสู่ระบบ</a>
                    </li>
                <?php endif; ?>
                <?php if (isset($user)) : ?>
                    <div class="relative">
                        <div id="dropdownInformationButton" class="bg-white cursor-pointer group border-2 border-purple-400 hover:bg-purple-200 rounded-lg py-2 px-3">
                            <button class="inline-block font-semibold transition-all text-gray-900 text-center items-center" type="button"><?php echo $user['displayName'] ?>
                                <svg class="transition-all -rotate-90 group-hover:rotate-0 group-focus:rotate-0 group-focus:text-pink-500 group-hover:text-pink-500 inline-flex w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                </svg>
                            </button>
                        </div>
                        <div id="dropdownInformation" class="min-w-32 md:left-1/2 transform md:-translate-x-1/2 divide-y absolute z-10 hidden bg-white divide-gray-100 rounded-lg shadow">
                            <div class="px-4 py-3 text-sm text-gray-900">
                                <div>ล็อคอินล่าสุด</div>
                                <div class="font-medium truncate">
                                    <?php
                                    date_default_timezone_set('Asia/Bangkok');
                                    $loginAt = strtotime($user['loginAt']);
                                    $formattedDate = date("d/m/Y H:i:s", strtotime('+543 year', $loginAt));
                                    echo $formattedDate;
                                    ?>
                                </div>
                            </div>
                            <?php if ($canPost == 1 || $manageUser == 1) : ?>
                                <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownInformationButton">
                                    <?php if ($canPost == 1) : ?>
                                        <li>
                                            <a href="/?page=courses" class="block px-4 py-2 hover:bg-gray-100">เพื่มหลักสูตร</a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if ($manageUser == 1) : ?>
                                        <li>
                                            <a href="/?page=manage_users" class="block px-4 py-2 hover:bg-gray-100">จัดการผู้ใช้</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            <?php endif; ?>
                            <div class="py-2">
                                <a href="/?page=logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">ออกจากระบบ</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<script>
    const navbar = document.getElementById('navbar');
    const logoText = document.getElementById('logoText');
    const logoImage = document.getElementById('logoImage');
    let lastScrollTop = 0;

    window.addEventListener('scroll', () => {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > lastScrollTop && scrollTop > 50) {
            navbar.classList.remove('nav');
            navbar.classList.add('nav-scrolled');
            logoImage.classList.remove('logo-expanded');
        } else if (scrollTop <= 50) {
            navbar.classList.remove('nav');
            navbar.classList.remove('nav-scrolled');
            logoImage.classList.add('logo-expanded');
        }

        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    });
</script>
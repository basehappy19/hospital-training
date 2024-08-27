<?php if (!empty($albums)) : ?>
    <table class="w-full text-sm text-left rtl:text-right text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3">
                    อัลบั้ม
                </th>
                <th scope="col" class="px-6 py-3">
                    หลักสูตรที่เกี่ยวข้อง
                </th>
                <th scope="col" class="px-6 py-3">
                    วันที่
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($albums as $album) : ?>
                <tr class="bg-white border-b">
                    <th scope="row" class="w-2/4 px-6 py-4">
                        <a href="/page=album_share&key=<?php echo $album['albumKey'] ?>" class="text-blue-600 whitespace-nowrap text-lg font-semibold max-w-xs truncate">
                            <?php echo $album['albumName'] ?>
                        </a>
                    </th>
                    <td class="w-1/4 px-6 py-4">
                        <?php if(isset($album['courseId'])) : ?>
                            <a class="text-blue-600" target="_blank" href="/?page=course_detail&id=<?php echo isset($album['courseId']) ? $album['courseId'] : "-" ?>"><?php echo isset($album['courseTitle']) ? $album['courseTitle'] : "-" ?></a>
                        <?php else : ?>
                            <?php echo isset($album['courseTitle']) ? $album['courseTitle'] : "-" ?>
                        <?php endif; ?>
                    </td>
                    <td class="w-1/4 px-6 py-4 text-sm">
                        <?php
                        date_default_timezone_set('Asia/Bangkok');
                        $loginAt = strtotime($album['createdAt']);
                        $formattedDate = date("d/m/Y", strtotime('+543 year', $loginAt));
                        echo $formattedDate;
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else : ?>
    <div class="my-5 col-span-2 w-full">
        <div class="bg-gray-100 p-8 rounded-lg text-center text-gray-500">
            ไม่พบอัมบั้มรูปภาพในขณะนี้
        </div>
    </div>
<?php endif; ?>
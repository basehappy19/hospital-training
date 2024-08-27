<?php $courseOptions = getCourseOptions($conn); ?>
<div id="modal" class="p-8 fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="relative p-5 border w-full md:w-1/2 shadow-lg rounded-md bg-white max-h-[80vh] overflow-y-auto">
        <div class="mt-3">
            <form id="addAlbum" class="mx-auto" action="" method="POST" enctype="multipart/form-data">
                <div class="mb-5">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900">ชื่ออัลบั้ม</label>
                    <input type="text" onkeydown="clearBorder(this)" id="name" name="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5" placeholder="ชื่ออัลบั้ม"/>
                </div>
                <div class="mb-5">
                    <label for="course" class="block mb-2 text-sm font-medium text-gray-900">หลักสูตรที่เกี่ยวข้อง <span class="opacity-60">(*ไม่จำเป็นต้องกรอก)</span></label>
                    <select id="course" name="course" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5">
                        <option value="">-- เลือกหลักสูตร --</option>
                        <?php foreach ($courseOptions as $option) : ?>
                            <option value="<?php echo $option['courseKey'] ?>"><?php echo $option['courseTitle'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="button" onclick="addAlbum()" class="transition-all w-full text-white bg-purple-500 hover:bg-purple-600 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">เพิ่มอัลบั้ม</button>
            </form>
            <button id="closeModal" class="transition-all absolute top-0 right-0 px-4 py-2 text-red-500 text-base font-medium hover:text-red-700">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-square-rounded-x">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M10 10l4 4m0 -4l-4 4" />
                    <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z" />
                </svg>
            </button>
        </div>
    </div>
</div>
<script src="/functions/Modal.js"></script>
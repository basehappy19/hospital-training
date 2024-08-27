<?php
require_once './functions/Course/Category.php';
$categories = getCategory($conn);

?>
<div id="modal" class="p-8 fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="relative p-5 border w-full md:w-1/2 shadow-lg rounded-md bg-white max-h-[80vh] overflow-y-auto">
        <div class="mt-3">
            <form id="addCourse" class="mx-auto" action="" method="POST" enctype="multipart/form-data">
                <div class="mb-5">
                    <h3 for="dropzone-images" class="mb-4 font-semibold text-gray-900">ภาพปกการอบรม</h3>
                    <div id="div-thumbnail" class="flex items-center justify-center w-full">
                        <label onclick="clearBorderThumbnail()" for="dropzone-thumbnail" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                </svg>
                                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">คลิกเพื่ออัปโหลด</span> หรือ ลาก วาง</p>
                                <p class="text-xs text-gray-500">PNG, JPG หรือ JPEG</p>
                            </div>
                            <input id="dropzone-thumbnail" name="thumbnail" type="file" class="hidden" accept="image/png, image/jpeg, image/jpg" onchange="previewThumbnail(event)" />
                        </label>
                    </div>
                    <div class="flex justify-center">
                        <div class="max-w-[350px]">
                            <div id="thumbnail-preview" class="border border-dashed border-cyan-300 rounded-lg object-cover mt-4"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-5">
                    <label for="title" class="block mb-2 text-sm font-medium text-gray-900">หัวข้อ</label>
                    <input onkeydown="clearBorder(this)" type="text" id="title" name="title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5" placeholder="อบรมเรื่อง..." />
                </div>
                <div class="mb-5">
                    <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">หมวดหมู่</label>
                    <select onclick="clearBorder(this)" id="category" name="category" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500">
                        <option value="">-- เลือกหมวดหมู่หลักสูตร --</option>
                        <?php foreach ($categories as $category) : ?>
                            <option value="<?php echo $category['id'] ?>"><?php echo $category['categoryTitle'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="md:flex gap-x-3 justify-between mb-5">
                    <div class="mb-5 md:mb-0 w-full">
                        <label for="startDate" class="block mb-2 text-sm font-medium text-gray-900">วันที่เริ่มอบรม</label>
                        <input type="text" onclick="clearBorder(this)" id="startDate" name="startDate" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 w-full p-2.5" placeholder="วัน/เดือน/ปี" />
                    </div>
                    <div class="mb-5 md:mb-0 w-full">
                        <label for="endDate" class="block mb-2 text-sm font-medium text-gray-900">วันที่สิ้นสุดอบรม</label>
                        <input type="text" onclick="clearBorder(this)" id="endDate" name="endDate" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 w-full p-2.5" placeholder="วัน/เดือน/ปี" />
                    </div>
                </div>
                <div class="md:flex gap-x-3 justify-between mb-5">
                    <div class="mb-5 md:mb-0 w-full">
                        <label for="startEnrollDate" class="block mb-2 text-sm font-medium text-gray-900">วันที่เริ่มลงทะเบียน</label>
                        <input type="text" onclick="clearBorder(this)" id="startEnrollDate" name="startEnrollDate" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 w-full p-2.5" placeholder="วัน/เดือน/ปี" />
                    </div>
                    <div class="mb-5 md:mb-0 w-full">
                        <label for="endEnrollDate" class="block mb-2 text-sm font-medium text-gray-900">วันที่สิ้นสุดลงทะเบียน</label>
                        <input type="text" onclick="clearBorder(this)" id="endEnrollDate" name="endEnrollDate" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 w-full p-2.5" placeholder="วัน/เดือน/ปี" />
                    </div>
                </div>
                <div class="mb-3">
                    <label class="inline-flex items-center cursor-pointer">
                        <input id="enrollFeeOpen" type="checkbox" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600"></div>
                        <span class="ms-3 text-sm font-medium text-gray-900">มีค่าลงทะเบียน</span>
                    </label>
                </div>
                <div class="mb-5" id="enrollFeeForm">
                    <label for="enrollFee" class="block mb-2 text-sm font-medium text-gray-900">ค่าลงทะเบียนจำนวน</label>
                    <input type="number" onkeydown="clearBorder(this)" id="enrollFee" name="enrollFee" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5" placeholder="" />
                </div>
                <div class="mb-3">
                    <label class="inline-flex items-center cursor-pointer">
                        <input id="limitOpen" type="checkbox" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600"></div>
                        <span class="ms-3 text-sm font-medium text-gray-900">จำกัดจำนวนผู้ลงทะเบียน</span>
                    </label>
                </div>
                <div class="mb-5" id="limitForm">
                    <label for="limit" class="block mb-2 text-sm font-medium text-gray-900">จำนวนผู้ลงทะเบียน</label>
                    <input type="number" onkeydown="clearBorder(this)" id="limit" name="limit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5" placeholder="" />
                </div>
                <div class="mb-5">
                    <label for="target" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">กลุ่มเป้าหมาย</label>
                    <div class="space-y-2">
                        <?php $options = [
                            ['value' => 'แพทย์', 'label' => 'แพทย์'],
                            ['value' => 'พยาบาล', 'label' => 'พยาบาล'],
                            ['value' => 'เจ้าหน้าที่โรงพยาบาล', 'label' => 'เจ้าหน้าที่โรงพยาบาล'],
                            ['value' => 'อื่นๆ', 'label' => 'อื่นๆ'],
                        ];
                        foreach ($options as $option) : ?>
                            <label class="gap-x-3 flex items-center">
                                <input onclick="clearBorderTarget()" type="checkbox" name="target[]" value="<?php echo htmlspecialchars($option['value']); ?>" <?php echo isset($selectedOptions) && in_array($option['value'], $selectedOptions) ? 'checked' : ''; ?> class="w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded-lg focus:ring-purple-500 focus:ring-2" <?php if ($option['value'] == 'อื่นๆ') echo 'id="other-checkbox"'; ?>>
                                <span class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($option['label']); ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="mb-5" id="type-target-other-form">
                    <label for="type-target-other-input" class="block mb-2 text-sm font-medium">กลุ่มเป้าหมาย อื่น ๆ</label>
                    <input type="text" id="type-target-other-input" name="type-target-other" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5" placeholder="กลุ่มเป้าหมายอื่น ๆ" onkeydown="clearBorder(this)" />
                </div>
                <div class="mb-5">
                    <label for="location" class="block mb-2 text-sm font-medium text-gray-900">สถานที่</label>
                    <input type="text" onkeydown="clearBorder(this)" id="location" name="location" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5" placeholder="โรงพยาบาลเมตตาประชารักษ์ (วัดไร่ขิง)" value="โรงพยาบาลเมตตาประชารักษ์ (วัดไร่ขิง)" />
                </div>
                <div class="mb-3">
                    <label class="inline-flex items-center mr-3 cursor-pointer">
                        <input type="checkbox" name="food" value="1" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600"></div>
                        <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">มีอาหารแจกในการอบรม</span>
                    </label>
                </div>
                <div class="mb-5">
                    <label class="inline-flex items-center mr-3 cursor-pointer">
                        <input type="checkbox" name="online" value="1" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600"></div>
                        <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">เปิดออนไลน์</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="onsite" value="1" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600"></div>
                        <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">เปิดออนไซต์</span>
                    </label>
                </div>
                <div class="mb-5">
                    <h3 for="dropzone-images" class="mb-4 font-semibold text-gray-900">ภาพรายละเอียดการอบรม</h3>
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
                            <input id="dropzone-images" name="images[]" type="file" class="hidden" accept="image/png, image/jpeg, image/jpg" multiple onchange="previewImages(event)" />
                        </label>
                    </div>
                    <div class="w-full">
                        <div id="images-preview" class="rounded-lg"></div>
                    </div>
                </div>
                <div class="mb-5" id="fileZone">
                    <h3 class="text-xl mb-5 font-semibold text-gray-900">เอกสารประกอบหลักสูตร</h3>
                    <div class="border rounded-lg">
                        <div class="flex flex-col">
                            <div class="border-b flex gap-2 md:gap-0 justify-normal md:justify-between items-center px-4 py-5">
                                <div class="transition-all ease-in-out duration-300 font-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-upload">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                        <path d="M7 9l5 -5l5 5" />
                                        <path d="M12 4l0 12" />
                                    </svg>
                                </div>
                                <div class="transition-all ease-in-out duration-300 font-bold">
                                    อัปโหลดไฟล์
                                </div>
                            </div>
                            <div class="p-4 w-full border-b">
                                <form action="" method="post" enctype="multipart/form-data">
                                    <div class="mb-5">
                                        <label for="fileTitle" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ชื่อไฟล์</label>
                                        <input type="text" name="fileTitle" id="fileTitle" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5" placeholder="กำหนดการหลักสูตร" />
                                    </div>
                                    <div class="flex items-center justify-center mb-5">
                                        <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                                </svg>
                                                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">คลิกเพื่ออัปโหลด</span> หรือ ลาก วาง</p>
                                                <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLS, XLW, PPTX, ZIP, RAR</p>
                                            </div>
                                            <input id="dropzone-file" name="file" type="file" class="hidden" />
                                        </label>
                                    </div>
                                    <div id="file-preview" class="hidden">
                                        <h3 class="text-lg font-semibold mb-2">ตัวอย่างไฟล์:</h3>
                                        <div class="bg-gray-100 p-4 rounded-lg">
                                            <p id="file-name" class="font-medium"></p>
                                            <p id="file-size" class="text-sm text-gray-600"></p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="addCourse()" class="transition-all w-full text-white bg-purple-500 hover:bg-purple-600 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">เพิ่มหลักสูตร</button>
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
<div class="col-span-1 h-full w-full py-2" id="openModal">
    <div class="flex items-center justify-center w-full h-full">
        <label for="openModal" class="flex flex-col items-center justify-center w-full h-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100">
            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                </svg>
                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">เพิ่มหลักสูตรใหม่</span></p>
                <p class="text-xs text-gray-500">กดที่นี้เพื่อเพิ่มหลักสูตร</p>
            </div>
        </label>
    </div>
</div>
<script src="/functions/Modal.js"></script>
<script>
    document.getElementById('dropzone-file').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const filePreview = document.getElementById('file-preview');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');

            filePreview.classList.remove('hidden');
            fileName.textContent = `ชื่อไฟล์: ${file.name}`;
            fileSize.textContent = `ขนาดไฟล์: ${(file.size / (1024 * 1024)).toFixed(2)} MB`;

            const fileNameWithoutExtension = file.name.split('.').slice(0, -1).join('.');
            document.getElementById('fileTitle').value = fileNameWithoutExtension;
        }
    });
    document.addEventListener('DOMContentLoaded', (event) => {
        const enrollFeeOpen = document.getElementById('enrollFeeOpen');
        const enrollFeeForm = document.getElementById('enrollFeeForm');
        const enrollFeeInput = document.getElementById('enrollFee');
        const limitOpen = document.getElementById('limitOpen');
        const limitForm = document.getElementById('limitForm');
        const limitInput = document.getElementById('limit');
        const targetOtherForm = document.getElementById("type-target-other-form");
        const inputOtherTarget = document.getElementById("type-target-other-input");
        const otherCheckbox = document.getElementById("other-checkbox");

        function toggleOtherInput() {
            if (otherCheckbox.checked) {
                targetOtherForm.style.display = "block";
            } else {
                targetOtherForm.style.display = "none";
                inputOtherTarget.value = "";
            }
        }

        otherCheckbox.addEventListener("change", toggleOtherInput);

        toggleOtherInput();

        enrollFeeForm.style.display = enrollFeeOpen.checked ? 'block' : 'none';
        limitForm.style.display = enrollFeeOpen.checked ? 'block' : 'none';

        enrollFeeOpen.addEventListener('change', (event) => {
            if (event.target.checked) {
                enrollFeeForm.style.display = 'block';
            } else {
                enrollFeeForm.style.display = 'none';
                enrollFee.style.border = '';
                enrollFeeInput.value = '';
            }
        });

        limitOpen.addEventListener('change', (event) => {
            if (event.target.checked) {
                limitForm.style.display = 'block';
            } else {
                limitForm.style.display = 'none';
                limitInput.style.border = '';
                limitInput.value = '';
            }
        });
    });
</script>
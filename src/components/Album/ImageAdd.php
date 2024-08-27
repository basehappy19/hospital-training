<div id="modal" class="z-50 p-8 fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
    <div class="relative p-5 border w-full md:w-1/2 shadow-lg rounded-md bg-white max-h-[80vh] overflow-y-auto">
        <div class="mt-3">
            <form id="addImages" class="mx-auto" action="" method="POST" enctype="multipart/form-data">
                <div class="mb-5">
                    <h3 for="dropzone-images" class="mb-4 font-semibold text-gray-900">เพิ่มรูปภาพ</h3>
                    <div class="flex items-center justify-center w-full" id="div-images">
                        <label onclick="clearBorderThumbnail()" for="dropzone-images" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50">
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
                <button type="button" onclick="uploadImages()" class="transition-all w-full text-white bg-purple-500 hover:bg-purple-600 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">อัปโหลด</button>
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
<div class="col-span-full w-full min-h-64 overflow-hidden" id="openModal">
    <div class="flex items-center justify-center w-full h-full">
        <label for="openModal" class="flex flex-col items-center justify-center w-full h-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100">
            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                </svg>
                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">เพิ่มรูปภาพ</span></p>
                <p class="text-xs text-gray-500">กดที่นี้เพื่ออัปโหลดรูป</p>
            </div>
        </label>
    </div>
</div>
<script src="/functions/Modal.js"></script>
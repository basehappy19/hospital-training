<div id="modal" class="z-50 p-8 fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
    <div class="relative p-5 border w-full md:w-1/2 shadow-lg rounded-md bg-white max-h-[80vh] overflow-y-auto">
        <div class="mt-3">
            <form id="addHotel" class="mx-auto" action="" method="POST" enctype="multipart/form-data">
                <div class="mb-5">
                    <h3 for="dropzone-images" class="mb-4 font-semibold text-gray-900">ภาพปกโรงแรม</h3>
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
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900">ชื่อโรงแรม</label>
                    <input type="text" onkeydown="clearBorder(this)" id="name" name="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="ชื่อโรงแรม" required />
                </div>
                <div class="mb-5">
                    <label for="phone" class="block mb-2 text-sm font-medium text-gray-900">เบอร์โทรศัพท์</label>
                    <input type="number" onkeydown="clearBorder(this)" id="phone" name="phone" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="เบอร์โทรศัพท์" required />
                </div>
                <div class="mb-5">
                    <h1 class="mb-3">รายละเอียดเพิ่มเติม <span class="opacity-60">(*ไม่จำเป็นต้องกรอก)</span></h1>
                    <div class="mb-5">
                        <label for="roomSize" class="block mb-2 text-sm font-medium text-gray-900">ขนาดห้อง (ตร.ม)</label>
                        <input type="number" onkeydown="clearBorder(this)" id="roomSize" name="roomSize" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="ตร.ม" required />
                    </div>
                    <div class="mb-5">
                        <label for="singleBedded" class="block mb-2 text-sm font-medium text-gray-900">เตียงเดี่ยว (จำนวน)</label>
                        <input type="number" onkeydown="clearBorder(this)" id="singleBedded" name="singleBedded" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="เตียงเดี่ยว" required />
                    </div>
                    <div class="mb-5">
                        <label for="twinBedded" class="block mb-2 text-sm font-medium text-gray-900">เตียงคู่ (จำนวน)</label>
                        <input type="number" onkeydown="clearBorder(this)" id="twinBedded" name="twinBedded" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="เตียงคู่" required />
                    </div>
                    <div class="mb-5">
                        <label for="kingSize" class="block mb-2 text-sm font-medium text-gray-900">เตียงคิงไซส์ (จำนวน)</label>
                        <input type="number" onkeydown="clearBorder(this)" id="kingSize" name="kingSize" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="เตียงคิงไซส์" required />
                    </div>
                    <h1 class="mb-3">สิ่งอำนวยความสะดวก <span class="opacity-60">(*ไม่จำเป็นต้องกรอก)</span></h1>
                    <div class="mb-5">
                        <label class="inline-flex items-center mr-3 cursor-pointer">
                            <input type="checkbox" name="windows" value="1" class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">มีหน้าต่าง</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="freeWifi" value="1" class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Wi-Fi ฟรี</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="airConditioner" value="1" class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">เครื่องปรับอากาศ</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="privateBathroom" value="1" class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">ห้องอาบน้ำส่วนตัว</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="bath" value="1" class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">อ่างอาบน้ำ</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="fridge" value="1" class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">ตู้เย็น</span>
                        </label>
                    </div>
                    <hr>
                </div>
                <div class="mb-2 flex items-end space-x-4">
                    <div class="flex-1">
                        <label for="latitude" class="block mb-2 text-sm font-medium text-gray-900">ละติจูด</label>
                        <input type="text" onkeydown="clearBorder(this)" id="latitude" name="latitude" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="ตำแหน่งละติจูด" required />
                    </div>
                    <div class="flex-1">
                        <label for="longitude" class="block mb-2 text-sm font-medium text-gray-900">ลองติจูด</label>
                        <input type="text" onkeydown="clearBorder(this)" id="longitude" name="longitude" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="ตำแหน่งลองติจูด" required />
                    </div>
                    <div>
                        <button type="button" onclick="searchLocation()" class="transition-all text-white bg-purple-500 hover:bg-purple-600 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center h-[42px]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-search">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                <path d="M21 21l-6 -6" />
                            </svg></button>
                        <input type="hidden" id="geocode" name="geocode">
                        <input type="hidden" id="country" name="country">
                        <input type="hidden" id="district" name="district">
                        <input type="hidden" id="elevation" name="elevation">
                        <input type="hidden" id="postcode" name="postcode">
                        <input type="hidden" id="province" name="province">
                        <input type="hidden" id="subdistrict" name="subdistrict">
                        <input type="hidden" id="road" name="road">
                    </div>
                </div>
                <div class="mb-2">
                    <a href="https://youtu.be/_IIUxC-kaNA?t=86" target="_blank"><span class="opacity-70">วิธีดูละติจูด, ลองติจูด </span><span class="font-bold text-blue-500">กดที่นี้</span></a>
                </div>
                <div class="mb-5 hidden" id="mapContainer">
                    <label for="map" class="block mb-2 text-sm font-medium text-gray-900">ที่ตั้งบน Maps</label>
                    <div id="map" class="w-full h-64 bg-gray-200 rounded-lg"></div>
                </div>
                <div id="address" class="mb-5 bg-red-200 p-2 text-gray-700 rounded-sm">
                    <div class="flex items-center gap-1">
                        <div id="notPinned">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-exclamation-circle">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M17 3.34a10 10 0 1 1 -15 8.66l.005 -.324a10 10 0 0 1 14.995 -8.336m-5 11.66a1 1 0 0 0 -1 1v.01a1 1 0 0 0 2 0v-.01a1 1 0 0 0 -1 -1m0 -7a1 1 0 0 0 -1 1v4a1 1 0 0 0 2 0v-4a1 1 0 0 0 -1 -1" />
                            </svg>
                        </div>
                        <div id="pinned" class="hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-map">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 7l6 -3l6 3l6 -3v13l-6 3l-6 -3l-6 3v-13" />
                                <path d="M9 4v13" />
                                <path d="M15 7v13" />
                            </svg>
                        </div>
                        <div id="address-text">ยังไม่ได้ปักหมุดโรงแรม</div>
                    </div>
                    <input type="hidden" name="address" id="address-value">
                </div>
                <div class="mb-5">
                    <label for="price" class="block mb-2 text-sm font-medium text-gray-900">ราคา</label>
                    <input type="text" onkeydown="clearBorder(this)" id="price" name="price" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required placeholder="สอบถามโรงแรม" />
                </div>
                <div class="mb-5">
                    <h3 for="dropzone-images" class="mb-4 font-semibold text-gray-900">ภาพรายละเอียดห้องพัก</h3>
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
                <button type="button" onclick="addHotel()" class="transition-all w-full text-white bg-purple-500 hover:bg-purple-600 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">เพิ่มโรงแรม</button>
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
                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">เพิ่มโรงแรมใหม่</span></p>
                <p class="text-xs text-gray-500">กดที่นี้เพื่อเพิ่มโรงแรม</p>
            </div>
        </label>
    </div>
</div>
<script src="/functions/Modal.js"></script>
<script src="./functions/Hotel/Maps.js"></script>

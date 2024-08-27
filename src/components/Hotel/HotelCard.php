<?php
require_once("./config/db.php");
require_once("./functions/Hotel/Hotel.php");

$hotels = getHotel($conn);
if (!empty($hotels)) : ?>
    <?php foreach ($hotels as $hotel) : ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden h-full flex flex-col">
            <div class="relative z-0">
                <div class="image-container relative transition-all w-full h-48 overflow-hidden">
                    <img class="w-full h-full max-h-[500px] object-cover object-center image cursor-pointer" src="/public/hotels/thumbnails/<?php echo $hotel['hotelKey']; ?>/<?php echo $hotel['hotelThumbnail']; ?>" alt="<?php echo $hotel['name']; ?>">
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
            <div class="p-4 flex-grow">
                <div class="text-lg font-bold text-cyan-600 mb-2">
                    <?php if (ctype_digit($hotel['price'])) :?>
                        ราคา
                    <?php endif; ?>
                    <?php echo $hotel['price'] ?></div>
                <?php if ($hotel['roomSize'] > 0) : ?>
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
                        <span class="font-semibold">ขนาดห้อง: <?php echo $hotel['roomSize'] ?> ตร.ม</span>
                    </div>
                <?php endif; ?>
                <div class="flex flex-wrap gap-2 mb-2">
                    <?php if ($hotel['singleBedded'] > 0) : ?>
                        <span class="bg-gray-100 px-2 py-1 rounded-full text-sm">เตียงเดี่ยว x<?php echo $hotel['singleBedded'] ?></span>
                    <?php endif; ?>
                    <?php if ($hotel['twinBedded'] > 0) : ?>
                        <span class="bg-gray-100 px-2 py-1 rounded-full text-sm">เตียงคู่ x<?php echo $hotel['twinBedded'] ?></span>
                    <?php endif; ?>
                    <?php if ($hotel['kingSize'] > 0) : ?>
                        <span class="bg-gray-100 px-2 py-1 rounded-full text-sm">เตียงคิงไซส์ x<?php echo $hotel['kingSize'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="flex gap-2 mb-4">
                    <?php if ($hotel['windows'] == 1) : ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-window">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 3c-3.866 0 -7 3.272 -7 7v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1 -1v-10c0 -3.728 -3.134 -7 -7 -7z" />
                            <path d="M5 13l14 0" />
                            <path d="M12 3l0 18" />
                        </svg>
                    <?php endif; ?>
                    <?php if ($hotel['freeWifi'] == 1) : ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-wifi">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 18l.01 0" />
                            <path d="M9.172 15.172a4 4 0 0 1 5.656 0" />
                            <path d="M6.343 12.343a8 8 0 0 1 11.314 0" />
                            <path d="M3.515 9.515c4.686 -4.687 12.284 -4.687 17 0" />
                        </svg>
                    <?php endif; ?>
                    <?php if ($hotel['airConditioner'] == 1) : ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-air-conditioning">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M8 16a3 3 0 0 1 -3 3" />
                            <path d="M16 16a3 3 0 0 0 3 3" />
                            <path d="M12 16v4" />
                            <path d="M3 5m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                            <path d="M7 13v-3a1 1 0 0 1 1 -1h8a1 1 0 0 1 1 1v3" />
                        </svg>
                    <?php endif; ?>
                    <?php if ($hotel['privateBathroom'] == 1) : ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-ripple">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M3 7c3 -2 6 -2 9 0s6 2 9 0" />
                            <path d="M3 17c3 -2 6 -2 9 0s6 2 9 0" />
                            <path d="M3 12c3 -2 6 -2 9 0s6 2 9 0" />
                        </svg>
                    <?php endif; ?>
                    <?php if ($hotel['bath'] == 1) : ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-bath">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 12h16a1 1 0 0 1 1 1v3a4 4 0 0 1 -4 4h-10a4 4 0 0 1 -4 -4v-3a1 1 0 0 1 1 -1z" />
                            <path d="M6 12v-7a2 2 0 0 1 2 -2h3v2.25" />
                            <path d="M4 21l1 -1.5" />
                            <path d="M20 21l-1 -1.5" />
                        </svg>
                    <?php endif; ?>
                    <?php if ($hotel['fridge'] == 1) : ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-fridge">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 3m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
                            <path d="M5 10h14" />
                            <path d="M9 13v3" />
                            <path d="M9 6v1" />
                        </svg>
                    <?php endif; ?>
                </div>
                <div class="text-sm">
                    <div class="flex items-center mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                        <a href="tel:<?php echo $hotel['phone'] ?>" class="text-blue-500 hover:underline"><?php echo $hotel['phone'] ?></a>
                    </div>
                    <div class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 mt-1 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <span><?php echo $hotel['address'] ?></span>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-gray-100 mt-auto">
                <div class="grid grid-cols-2 gap-2">
                    <a href="/?page=hotel_detail&id=<?php echo $hotel['id'] ?>" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-4 rounded text-center text-sm flex items-center justify-center">ดูรายละเอียด</a>
                    <a href="https://www.google.com/maps/search/<?php echo $hotel['latitude'] ?>,+<?php echo $hotel['longitude'] ?>" target="_blank" rel="noopener noreferrer" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded text-center text-sm flex items-center justify-center">ดูบน Google Maps</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php include './lib/image-container.php' ?>
<?php else : ?>
    <div class="my-5 col-span-2 w-full">
        <div class="bg-gray-100 p-8 rounded-lg text-center text-gray-500">
            ไม่พบโรงแรมในขณะนี้
        </div>
    </div>
<?php endif; ?>
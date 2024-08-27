<?php
require_once("./config/db.php");
require_once("./functions/Album/Album.php");

$album = getAlbumWithImages($conn, $key);
if (!empty($album)) : ?>
    <?php foreach ($album['albumImages'] as $image) : ?>
        <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden">
            <div class="image-container relative">
                <img class="h-full max-h-[500px] w-full object-contain object-center image cursor-pointer" src="/public/albums/<?php echo $album['albumKey']; ?>/<?php echo $image['ImageName']; ?>" alt="<?php echo $image['ImageName']; ?>">
                <div class="image-overlay cursor-pointer absolute inset-0 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="expand-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <polyline points="9 21 3 21 3 15"></polyline>
                        <line x1="21" y1="3" x2="14" y2="10"></line>
                        <line x1="3" y1="21" x2="10" y2="14"></line>
                    </svg>
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
                    <?php if (isset($_SESSION['userId'])) : ?>
                        <button class="cursor-pointer rounded-lg bg-white transition-all ease-in-out text-red-500 font-medium hover:text-red-700 absolute top-2 right-12 z-40" onclick="removeImage('/album/<?php echo $albumKey ?>/image/remove/<?php echo $image['id'] ?>')" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 7l16 0" />
                                <path d="M10 11l0 6" />
                                <path d="M14 11l0 6" />
                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                            </svg>
                        </button>
                    <?php endif; ?>
                    <img id="lightbox-img" class="transition-all max-w-full max-h-[80vh] rounded-lg">
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php include './lib/image-container.php' ?>
<?php else : ?>
    <div class="my-5 col-span-2 w-full">
        <div class="bg-gray-100 p-8 rounded-lg text-center text-gray-500">
            ไม่พบรูปภาพในขณะนี้
        </div>
    </div>
<?php endif; ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageContainers = document.querySelectorAll('.image-container');
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        const closeLightbox = document.getElementById('close-lightbox');

        if (!lightbox || !lightboxImg || !closeLightbox) {
            return;
        }

        if (imageContainers.length != 0) {
            imageContainers.forEach(container => {
                container.addEventListener('click', function() {
                    const image = this.querySelector('.image');
                    if (image) {
                        lightboxImg.src = image.src;
                        lightbox.style.display = 'flex';
                        setTimeout(() => {
                            lightbox.classList.add('active');
                        }, 10);
                    } else {
                        console.warn('No image found in this container', this);
                    }
                });
            });
        }

        function closeLightboxHandler() {
            lightbox.classList.remove('active');
            setTimeout(() => {
                lightbox.style.display = 'none';
            }, 300);
        }

        closeLightbox.addEventListener('click', closeLightboxHandler);

        lightbox.addEventListener('click', function(event) {
            if (event.target == lightbox) {
                closeLightboxHandler();
            }
        });
    });
</script>
<style>
    .lightbox {
        transition: opacity 0.3s ease-in-out;
        opacity: 0;
        pointer-events: none;
    }

    .lightbox.active {
        opacity: 1;
        pointer-events: auto;
    }

    .lightbox-content {
        transition: transform 0.3s ease-in-out;
        transform: scale(0.9);
    }

    .lightbox.active .lightbox-content {
        transform: scale(1);
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }

    .image-container {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .image-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .image-container:hover .image-overlay {
        opacity: 1;
    }

    .expand-icon {
        color: white;
        font-size: 2rem;
    }
</style>
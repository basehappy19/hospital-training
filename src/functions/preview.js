function previewPaymentProof(event) {
  const input = event.target;
  const previewContainer = document.getElementById("paymentProof-preview");

  if (previewContainer) {
    previewContainer.innerHTML = "";

    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function (e) {
        const imgElement = document.createElement("img");
        imgElement.src = e.target.result;
        imgElement.classList.add("max-w-full", "h-auto", "rounded-lg");
        previewContainer.appendChild(imgElement);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
}

function previewThumbnail(event) {
  const input = event.target;
  const previewContainer = document.getElementById("thumbnail-preview");

  if (previewContainer) {
    previewContainer.innerHTML = "";

    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function (e) {
        const imgElement = document.createElement("img");
        imgElement.src = e.target.result;
        imgElement.classList.add("max-w-full", "h-auto", "rounded-lg");
        previewContainer.appendChild(imgElement);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
}

function previewImageAndRemoveImg(event) {
  const input = event.target;
  const previewContainer = document.getElementById("image-preview");

  if (previewContainer) {
    previewContainer.innerHTML = "";

    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function (e) {
        const imgElement = document.createElement("img");
        imgElement.src = e.target.result;
        imgElement.classList.add("max-w-full", "h-auto", "rounded-lg");
        previewContainer.appendChild(imgElement);

        const imgBefore = document.getElementById("imgBefore");
        if (imgBefore) {
          imgBefore.remove();
        }
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
}

let selectedFiles = [];
let existingImages = [];

function initializeExistingImages() {
  const existingImagesContainer = document.getElementById("existing-images");
  if (existingImagesContainer) {
    existingImages = Array.from(
      existingImagesContainer.querySelectorAll(".existing-image")
    ).map((div) => ({
      element: div,
      src: div.querySelector("img").src,
    }));
    updateImageCount();
  }
}

function previewImages(event) {
  const input = event.target;
  const previewContainer = document.getElementById("images-preview");

  if (previewContainer) {
    previewContainer.innerHTML = "";

    if (input.files && input.files.length > 0) {
      selectedFiles = Array.from(input.files);

      const countDiv = document.createElement("div");
      countDiv.classList.add("text-center", "my-2", "text-gray-600");
      countDiv.id = "image-count";
      previewContainer.appendChild(countDiv);

      const imageContainer = document.createElement("div");
      imageContainer.classList.add(
        "border",
        "border-dashed",
        "border-cyan-300",
        "grid",
        "grid-cols-2",
        "gap-3",
        "p-4"
      );
      previewContainer.appendChild(imageContainer);

      selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function (e) {
          const imgWrapper = createImageWrapper(e.target.result, index, true);
          imageContainer.appendChild(imgWrapper);
        };
        reader.readAsDataURL(file);
      });

      updateImageCount();
    }
  }
}

function createImageWrapper(src, index, isNew = false) {
  const imgWrapper = document.createElement("div");
  imgWrapper.classList.add("col-span-1", "relative");
  imgWrapper.setAttribute("data-index", index);

  const imgElement = document.createElement("img");
  imgElement.src = src;
  imgElement.classList.add("w-full", "h-auto", "rounded-lg", "object-cover");

  const deleteButton = document.createElement("button");
  deleteButton.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-square-rounded-x">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M10 10l4 4m0 -4l-4 4" />
                  <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z" />
              </svg>`;
  deleteButton.classList.add(
    "transition-all",
    "px-4",
    "py-2",
    "text-red-500",
    "text-base",
    "font-medium",
    "hover:text-red-700",
    "absolute",
    "top-0",
    "right-0"
  );
  deleteButton.onclick = function () {
    if (isNew) {
      removeNewImage(index);
    } else {
      removeExistingImage(index);
    }
  };

  imgWrapper.appendChild(imgElement);
  imgWrapper.appendChild(deleteButton);
  return imgWrapper;
}

function removeNewImage(index) {
  selectedFiles.splice(index, 1);
  updatePreview();
  updateImageCount();
}

function removeExistingImage(index) {
  const existingImagesContainer = document.getElementById("existing-images");
  if (existingImagesContainer) {
    const imageElement = existingImagesContainer.querySelector(
      `.existing-image[data-index="${index}"]`
    );
    if (imageElement) {
      imageElement.remove();
      const inputElement = document.querySelector(
        `input[name="existing_images[]"][value="${imageElement
          .querySelector("img")
          .src.split("/")
          .pop()}"]`
      );
      if (inputElement) {
        inputElement.remove();
      }
    }
  }
}

function updatePreview() {
  const previewContainer = document.getElementById("images-preview");

  if (previewContainer) {
    previewContainer.innerHTML = "";

    const countDiv = document.createElement("div");
    countDiv.classList.add("text-center", "my-2", "text-gray-600");
    countDiv.id = "image-count";
    previewContainer.appendChild(countDiv);

    const imageContainer = document.createElement("div");
    imageContainer.classList.add(
      "border",
      "border-dashed",
      "border-cyan-300",
      "grid",
      "grid-cols-2",
      "gap-3",
      "p-4"
    );
    previewContainer.appendChild(imageContainer);

    selectedFiles.forEach((file, index) => {
      const reader = new FileReader();
      reader.onload = function (e) {
        const imgWrapper = createImageWrapper(e.target.result, index, true);
        imageContainer.appendChild(imgWrapper);
      };
      reader.readAsDataURL(file);
    });

    updateImageCount();
  }
}

function updateImageCount() {
  const totalCount = existingImages.length + selectedFiles.length;
  const countDiv = document.getElementById("image-count");
  if (countDiv) {
    countDiv.textContent = `จำนวนรูปภาพที่เลือก: ${totalCount}`;
  }
}

function prepareImageDataForSubmit() {
  const imagesToKeep = existingImages.map((img) => img.src.split("/").pop());
  const formData = new FormData();

  imagesToKeep.forEach((imageName) => {
    formData.append("existing_images[]", imageName);
  });

  selectedFiles.forEach((file) => {
    formData.append("new_images[]", file);
  });

  return formData;
}

document.addEventListener("DOMContentLoaded", function () {
  initializeExistingImages();
});

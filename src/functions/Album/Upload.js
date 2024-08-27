function uploadImages() {
    if (validateAdd()) {
        let addForm = document.getElementById('addImages');
                
        Swal.fire({
            title: "ยืนยันการอัปโหลดรูปภาพ",
            text: "แน่ใจหรือไม่",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#5fe280",
            cancelButtonColor: "#d33",
            cancelButtonText: "ยกเลิก",
            confirmButtonText: "ยืนยัน",
            timer: 10000,  
            timerProgressBar: true  
        }).then((result) => {
            if (result.isConfirmed) {
                addForm.submit();                
            }
        });
        
    }
}

function validateAdd() {
    let pass = true;
    let images = document.getElementById('dropzone-images');
    let divImages = document.getElementById('div-images');

    if (images.files.length <= 0) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาอัปโหลดรูปภาพก่อน",
            text: "โปรดอัปโหลดรูปภาพก่อน",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true, 
        });
        divImages.style.border = "1px dashed red"
        divImages.style.borderRadius = "8px";
    }
    if (!pass) {
        return pass;  
    }

    return pass;
}

function clearBorderThumbnail() {
    document.getElementById("div-images").style.border = "0px solid #d1d5db"
}
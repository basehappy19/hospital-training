function addAlbum() {
    if (validateAdd()) {
        let addForm = document.getElementById('addAlbum');
                
        Swal.fire({
            title: "ยืนยันการเพิ่มอัลบั้ม",
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
    let albumName = document.getElementById('name');

    if (!albumName.value) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาตั้งชื่ออัลบั้ม",
            text: "โปรดตั้งชื่ออัลบั้ม",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true, 
        });
        albumName.style.border = "1px solid red";
    } 
    if (!pass) {
        return pass;  
    }

    return pass;
}

function removeAlbum(url) {
    Swal.fire({
        title: "ลบอัลบั้ม",
        text: "แน่ใจหรือไม่",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#5fe280",
        cancelButtonColor: "#e25f5f",
        cancelButtonText: "ยกเลิก",
        confirmButtonText: "แน่ใจ",
        timer: 10000,
        timerProgressBar: true,
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "ยืนยันการลบอัลบั้ม",
                text: "กรุณายืนยันอีกครั้ง",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#5fe280",
                cancelButtonColor: "#e25f5f",
                confirmButtonText: "ยืนยัน",
                cancelButtonText: "ยกเลิก",
                }).then((secondResult) => {
                if (secondResult.isConfirmed) {
                    window.location.href = url;  
                }
                });
            }
    });
}

function removeImage(url) {
    Swal.fire({
        title: "ลบรูปภาพ",
        text: "แน่ใจหรือไม่",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#5fe280",
        cancelButtonColor: "#e25f5f",
        cancelButtonText: "ยกเลิก",
        confirmButtonText: "แน่ใจ",
        timer: 10000,
        timerProgressBar: true,
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "ยืนยันการลบรูปภาพ",
                text: "กรุณายืนยันอีกครั้ง",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#5fe280",
                cancelButtonColor: "#e25f5f",
                confirmButtonText: "ยืนยัน",
                cancelButtonText: "ยกเลิก",
                }).then((secondResult) => {
                if (secondResult.isConfirmed) {
                    window.location.href = url;  
                }
                });
            }
    });
}

function clearBorder(e) {
    document.querySelector(`#${e.id}`).style.border = "1px solid #d1d5db"
}
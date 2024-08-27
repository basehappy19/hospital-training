function removeHotel(url) {
    Swal.fire({
        title: "ลบโรงแรม",
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
                title: "ยืนยันการลบโรงแรม",
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
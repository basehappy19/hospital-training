function addCourse() {
    if (validateAdd()) {
        let addForm = document.getElementById('addCourse');
                
        Swal.fire({
            title: "ยืนยันการเพิ่มหลักสูตร",
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

function parseThaiDate(dateString) {
    const [day, month, year] = dateString.split('-').map(Number);
    return new Date(year - 543, month - 1, day); 
}

function validateDateRange(startDateStr, endDateStr) {
    const startDate = parseThaiDate(startDateStr);
    const endDate = parseThaiDate(endDateStr);

    if (startDate > endDate) {
        return false;
    }
    return true;
}

function validateAdd() {
    let pass = true;
    let thumbnail = document.getElementById('dropzone-thumbnail');
    let divThumbnail = document.getElementById('div-thumbnail');
    let title = document.getElementById('title');
    let category = document.getElementById('category');
    let startDate = document.getElementById('startDate');
    let endDate = document.getElementById('endDate');
    let startEnrollDate = document.getElementById('startEnrollDate');
    let endEnrollDate = document.getElementById('endEnrollDate');
    let enrollFeeOpen = document.getElementById('enrollFeeOpen');
    let enrollFee = document.getElementById('enrollFee');
    let limitOpen = document.getElementById('limitOpen');
    let limit = document.getElementById('limit');
    let checkboxes = document.querySelectorAll('input[name="target[]"]');
    let otherCheckbox = document.getElementById('other-checkbox');
    let typeOtherTarget = document.getElementById("type-target-other-input");
    let location = document.getElementById('location');

    let isAnyChecked = false;
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            isAnyChecked = true;
        }
    });

    if (thumbnail.files.length <= 0) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาเพิ่มภาพปกหลักสูตร",
            text: "โปรดเพิ่มภาพปกหลักสูตร",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true, 
        });
        divThumbnail.style.border = "1px dashed red";
        divThumbnail.style.borderRadius = "8px";
    } else if (!title.value) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาระบุหัวข้อ",
            text: "โปรดระบุหัวข้อ",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true, 
        });
        title.style.border = "1px solid red";
    } else if (!category.value) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาระบุหมวดหมู่หลักสูตร",
            text: "โปรดระบุหมวดหมู่หลักสูตร",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true,
        });
        category.style.border = "1px solid red";
    } else if (!startDate.value) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาระบุวันที่เริ่มอบรม",
            text: "โปรดระบุวันเริ่มอบรม",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true,
        });
        startDate.style.border = "1px solid red";
    } else if (!endDate.value) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาระบุวันที่สิ้นสุดอบรม",
            text: "โปรดระบุวันที่สิ้นสุดอบรม",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true,
        });
        endDate.style.border = "1px solid red";
    } else if (!validateDateRange(startDate.value, endDate.value)) {
        pass = false;
        Swal.fire({
            icon: "error",
            title: `วันที่ไม่ถูกต้อง`,
            text: `วันที่เริ่มต้นต้องไม่มากกว่าวันที่สิ้นสุด`,
            confirmButtonColor: "#d33",
            confirmButtonText: "ตกลง",
            timer: 3000,
            timerProgressBar: true,
        });
        startDate.style.border = "1px solid red";
        endDate.style.border = "1px solid red";
    } else if (!startEnrollDate.value) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาระบุวันที่เริ่มต้นลงทะเบียน",
            text: "โปรดระบุวันที่เริ่มต้นลงทะเบียน",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true,
        });
        startEnrollDate.style.border = "1px solid red";
    } else if (!endEnrollDate.value) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาระบุวันที่สิ้นสุดลงทะเบียน",
            text: "โปรดระบุวันที่สิ้นสุดลงทะเบียน",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true,
        });
        endEnrollDate.style.border = "1px solid red";
    } else if (!validateDateRange(startEnrollDate.value, endEnrollDate.value, "ลงทะเบียน")) {
        pass = false;
        Swal.fire({
            icon: "error",
            title: `วันที่ไม่ถูกต้อง`,
            text: `วันที่เริ่มต้นต้องไม่มากกว่าวันที่สิ้นสุด`,
            confirmButtonColor: "#d33",
            confirmButtonText: "ตกลง",
            timer: 3000,
            timerProgressBar: true,
        });
        startEnrollDate.style.border = "1px solid red";
        endEnrollDate.style.border = "1px solid red";
    } else if (enrollFeeOpen.checked && !enrollFee.value) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาระบุค่าลงทะเบียน",
            text: "โปรดระบุค่าลงทะเบียน",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true,
        });
        enrollFee.style.border = "1px solid red";
    } else if (limitOpen.checked && !limit.value) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาระบุจำนวนผู้ลงทะเบียน",
            text: "โปรดระบุจำนวนผู้ลงทะเบียน",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true,
        });
        limit.style.border = "1px solid red";
    } else if (!isAnyChecked) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาระบุกลุ่มเป้าหมาย",
            text: "โปรดเลือกอย่างน้อยหนึ่งกลุ่มเป้าหมาย",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,
            timerProgressBar: true,
        });
        checkboxes.forEach(checkbox => {
            checkbox.style.outline = "2px solid red";
        });
    } else if (otherCheckbox.checked && !typeOtherTarget.value.trim()) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาระบุกลุ่มเป้าหมายอื่นๆ",
            text: "โปรดระบุกลุ่มเป้าหมายอื่นๆ ที่คุณเลือก",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,
            timerProgressBar: true,
        });
        typeOtherTarget.style.border = "2px solid red";
    } else if (!location.value) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาระบุสถานที่",
            text: "โปรดระบุสถานที่",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true,
        });
        location.style.border = "1px solid red";
    }

    if (!pass) {
        return pass;  
    }

    return pass;
}

function clearBorder(e) {
    document.querySelector(`#${e.id}`).style.border = "1px solid #d1d5db"
}

function clearBorderThumbnail() {
    document.getElementById("div-thumbnail").style.border = "0px solid #d1d5db"
}

function clearBorderTarget() {
    let checkboxes = document.querySelectorAll('input[name="target[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.style.outline = "0px solid #d1d5db";
    });
}

function removeCourse(url) {
    Swal.fire({
        title: "ลบหลักสูตร",
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
                title: "ยืนยันการลบหลักสูตร",
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
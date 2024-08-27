function enroll() {
    if (validateEnroll()) {
        let enrollForm = document.getElementById('enrollForm');
                
        Swal.fire({
            title: "ยืนยันการลงทะเบียนหลักสูตร",
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
                enrollForm.submit();                
            }
        });
        
    }
}

function validateEnroll() {
    let pass = true;
    let fullname = document.getElementById('fullname');
    let institution = document.getElementById('institution');
    let phone = document.getElementById('phone');
    let email = document.getElementById('email');
    let selectedTypeFood = document.querySelector('input[name="type-food"]:checked');
    let selectedTypeEnroll = document.querySelector('input[name="type-enroll"]:checked');
    let typeOtherFood = document.getElementById("type-food-other-input");
    let typeFoodForm = document.getElementById("type-food-form");
    let typeEnroll = document.getElementById("type-enroll-form");
    let paymentProof = document.getElementById("dropzone-file");

    if (!fullname.value) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาระบุชื่อ - นามสกุล",
            text: "โปรดระบุชื่อ - นามสกุล",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true, 
        });
        fullname.style.border = "1px solid red";
    } else if (!institution.value) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาระบุหน่วยงาน",
            text: "โปรดระบุหน่วยงาน",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true, 
        });
        institution.style.border = "1px solid red";
    } else if (phone.value.length != 10 || !(/^\d+$/.test(phone.value))) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาระบุเบอร์โทรศัพท์",
            text: "โปรดระบุเบอร์โทรศัพท์",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true,
        });
        phone.style.border = "1px solid red";
    } else if (!email.value) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาระบุอีเมล",
            text: "โปรดระบุอีเมล",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true,
        });
        email.style.border = "1px solid red";
    } else if (selectedTypeFood) {
        if (!selectedTypeFood) {
            pass = false;
            Swal.fire({
                icon: "question",
                title: "กรุณาระบุประเภทอาหาร",
                text: "โปรดระบุประเภทอาหาร",
                confirmButtonColor: "#d33",
                confirmButtonText: "ลองใหม่",
                timer: 3000,  
                timerProgressBar: true,
            });
            typeFoodForm.style.border = "1px solid red";
    } else if (selectedTypeFood.value == "อื่นๆ" && !typeOtherFood.value) {
            pass = false;
            Swal.fire({
                icon: "question",
                title: "กรุณาระบุประเภทอาหารที่ต้องการ",
                text: "โปรดระบุประเภทอาหารที่ต้องการ",
                confirmButtonColor: "#d33",
                confirmButtonText: "ลองใหม่",
                timer: 3000,  
                timerProgressBar: true,
            });
            typeOtherFood.style.border = "1px solid red";
    } 
    } else if (selectedTypeEnroll) {
        if (!selectedTypeEnroll) {
            pass = false;
            Swal.fire({
                icon: "question",
                title: "กรุณาระบุประเภทลงทะเบียน",
                text: "โปรดระบุประเภทลงทะเบียน",
                confirmButtonColor: "#d33",
                confirmButtonText: "ลองใหม่",
                timer: 3000,  
                timerProgressBar: true,
            });
        typeEnroll.style.border = "1px solid red"; 
    }
    } else if (paymentProof) {
        if (paymentProof.files.length <= 0) {
            pass = false;
            Swal.fire({
                icon: "question",
                title: "กรุณาอัพโหลดสลิปการโอน",
                text: "โปรดอัพโหลดหลักฐานค่าลงทะเบียน",
                confirmButtonColor: "#d33",
                confirmButtonText: "ลองใหม่",
                timer: 3000,  
                timerProgressBar: true,
            });
        }
    } 

    if (!pass) {
        return pass;  
    }
    return pass;
}

function clearBorder(e) {
    document.querySelector(`#${e.id}`).style.border = "1px solid #d1d5db"
}

function clearBorderCheckedFood() {
    document.getElementById("type-food-form").style.border = "1px solid #d1d5db"
}

function clearBorderCheckedEnroll() {
    document.getElementById("type-enroll-form").style.border = "1px solid #d1d5db"
}

document.addEventListener("DOMContentLoaded", function() {
    let foodOtherForm = document.getElementById("type-food-other-form");
    let inputOtherFood = document.getElementById("type-food-other-input");
    let horizontalTypeOther = document.getElementById("horizontal-type-food-other");

    if (foodOtherForm && inputOtherFood && horizontalTypeOther) {
        foodOtherForm.style.display = "none";

        if (horizontalTypeOther.checked) {
            foodOtherForm.style.display = "block";
        }

        let foodInputs = document.querySelectorAll("input[name='type-food']");
        foodInputs.forEach(function(input) {
            input.addEventListener("change", function() {
                if (this.value == "อื่นๆ") {
                    foodOtherForm.style.display = "block";
                    inputOtherFood.value = "";
                } else {
                    foodOtherForm.style.display = "none";
                    inputOtherFood.value = "";
                }
            });
        });
    }
});


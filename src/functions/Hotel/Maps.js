function addHotel() {
    if (validateAdd()) {
        let addForm = document.getElementById('addHotel');
                
        Swal.fire({
            title: "ยืนยันการเพิ่มโรงแรม",
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
    let thumbnail = document.getElementById('dropzone-thumbnail');
    let divThumbnail = document.getElementById('div-thumbnail');
    let name = document.getElementById('name');
    let phone = document.getElementById('phone');
    let latitude = document.getElementById('latitude');
    let longitude = document.getElementById('longitude');
    let price = document.getElementById('price');
    let geocode = document.getElementById('geocode');
    let country = document.getElementById('country');
    let district = document.getElementById('district');
    let elevation = document.getElementById('elevation');
    let postcode = document.getElementById('postcode');
    let province = document.getElementById('province');
    let subdistrict = document.getElementById('subdistrict');
    let road = document.getElementById('road');

    if (!geocode.value && !country.value && !district.value && !elevation.value && !postcode.value && !province.value && !subdistrict.value && !road.value) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาปักหมุดโรงแรมก่อน",
            text: "โปรดปักหมุดโรงแรมก่อน",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true, 
        });
    } else if (thumbnail.files.length <= 0) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาเพิ่มภาพปกโรมแรม",
            text: "โปรดเพิ่มภาพปกโรมแรม",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true, 
        });
        divThumbnail.style.border = "1px dashed red";
        divThumbnail.style.borderRadius = "8px";
    } else if (!name.value) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาระบุชื่อโรงแรม",
            text: "โปรดระบุชื่อโรงแรม",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true, 
        });
        name.style.border = "1px dashed red";
    } else if (phone.value.length != 10) {
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
    } else if (!latitude.value) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาระบุละติจูด",
            text: "โปรดระบุละติจูด",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true,
        });
        latitude.style.border = "1px solid red";
    } else if (!longitude.value) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาระบุลองติจูด",
            text: "โปรดระบุลองติจูด",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true,
        });
        longitude.style.border = "1px solid red";
    } else if (!price.value) {
        pass = false;
        Swal.fire({
            icon: "question",
            title: "กรุณาระบุราคาโรงแรม",
            text: "โปรดระบุราคาโรงแรม",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true,
        });
        price.style.border = "1px solid red";
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

function searchLocation() {
    const latitude = parseFloat(document.getElementById('latitude').value);
    const longitude = parseFloat(document.getElementById('longitude').value);
    let map;
    let marker;
    if (!isNaN(latitude) && !isNaN(longitude)) {
        if (!map) {
            map = new longdo.Map({
                placeholder: document.getElementById('map')
            });
        }
        reverseGeocoding(longitude, latitude);

        const location = { lon: longitude, lat: latitude };
        map.location(location, true);

        if (marker) {
            map.Overlays.remove(marker);
        }

        marker = new longdo.Marker(location);
        map.Overlays.add(marker);
        document.getElementById('mapContainer').classList.remove('hidden');
    } else {
        Swal.fire({
            icon: "question",
            title: "กรุณาระบุละติจูดและลองติจูดที่ถูกต้อง",
            text: "โปรดระบุละติจูดและลองติจูดที่ถูกต้อง",
            confirmButtonColor: "#d33",
            confirmButtonText: "ลองใหม่",
            timer: 3000,  
            timerProgressBar: true,
        });
    }
}

function reverseGeocoding(longitude, latitude) { 
    const apiKey = "04af67a657fef741145c00d9249a12e7";
    const url = `https://api.longdo.com/map/services/address?key=${apiKey}&lon=${longitude}&lat=${latitude}`;

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(results => {
            const geocode = results.geocode || '';
            const country = results.country || '';
            const district = results.district || '';
            const elevation = results.elevation || '';
            const postcode = results.postcode || '';
            const province = results.province || '';
            const subdistrict = results.subdistrict || '';
            const road = results.road || '';
            document.getElementById('geocode').value = geocode;
            document.getElementById('country').value = country;
            document.getElementById('district').value = district;
            document.getElementById('elevation').value = elevation;
            document.getElementById('postcode').value = postcode;
            document.getElementById('province').value = province;
            document.getElementById('subdistrict').value = subdistrict;
            document.getElementById('road').value = road;
            
            document.getElementById('address').classList.remove('bg-red-200');
            document.getElementById('address').classList.add('bg-green-200');

            document.getElementById('notPinned').classList.add('hidden');
            document.getElementById('pinned').classList.remove('hidden');

            let addressText = '';
            if (province) addressText += ` ${province}`;
            if (district) addressText += ` ${district}`;
            if (subdistrict) addressText += ` ${subdistrict}`;
            if (road) addressText += ` ${road}`;
            if (postcode) addressText += ` รหัสไปรษณีย์ ${postcode}`;
            else addressText = addressText.replace(/, $/, '');  

            document.getElementById('address-text').innerHTML = 'ปักหมุดโรงแรมแล้ว ที่อยู่ : ' + addressText.trim() || 'ไม่พบข้อมูล';
            document.getElementById('address-value').value = addressText.trim() || 'ไม่พบข้อมูล';
        })
        .catch(error => {
            console.error('failed to fetch maps',);
    });
}


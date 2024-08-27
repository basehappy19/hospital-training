<?php

function getHotel($conn) {
    $sql = 'SELECT 
    hotels.id, 
    hotels.hotelKey, 
    hotels.name, 
    hotels.roomSize, 
    hotels.phone,
    hotels.price, 
    bed.singleBedded, 
    bed.twinBedded, 
    bed.kingSize, 
    service.windows, 
    service.freeWifi, 
    service.airConditioner, 
    service.privateBathroom, 
    service.bath, 
    service.fridge, 
    location.geocode, 
    location.address, 
    location.country, 
    location.province, 
    location.district,
    location.subdistrict, 
    location.postcode, 
    location.elevation, 
    location.road, 
    location.latitude, 
    location.longitude, 
    thumbnail.hotelImageName AS hotelThumbnail
    FROM hotels
    LEFT JOIN hotel_beds AS bed ON hotels.hotelKey = bed.hotelKey
    LEFT JOIN hotel_services AS service ON hotels.hotelKey = service.hotelKey
    LEFT JOIN hotel_locations AS location ON hotels.hotelKey = location.hotelKey
    LEFT JOIN hotel_thumbnails AS thumbnail ON hotels.hotelKey = thumbnail.hotelKey
    ORDER BY hotels.id DESC;';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

function getHotelByKeyForRemove($conn, $hotelKey) {
    $sql = 'SELECT hotels.name
            FROM hotels
            WHERE hotels.hotelKey = :hotelKey';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getHotelById($conn, $hotelId) {
    $sql = 'SELECT 
    hotels.id, 
    hotels.hotelKey, 
    hotels.name, 
    hotels.roomSize, 
    hotels.phone,
    hotels.price, 
    bed.singleBedded, 
    bed.twinBedded, 
    bed.kingSize, 
    service.windows, 
    service.freeWifi, 
    service.airConditioner, 
    service.privateBathroom, 
    service.bath, 
    service.fridge, 
    location.geocode, 
    location.address, 
    location.country, 
    location.province, 
    location.district,
    location.subdistrict, 
    location.postcode, 
    location.elevation, 
    location.road, 
    location.latitude, 
    location.longitude, 
    thumbnail.hotelImageName AS hotelThumbnail
    FROM hotels
    LEFT JOIN hotel_beds AS bed ON hotels.hotelKey = bed.hotelKey
    LEFT JOIN hotel_services AS service ON hotels.hotelKey = service.hotelKey
    LEFT JOIN hotel_locations AS location ON hotels.hotelKey = location.hotelKey
    LEFT JOIN hotel_thumbnails AS thumbnail ON hotels.hotelKey = thumbnail.hotelKey
    WHERE hotels.id = :hotelId ORDER BY hotels.id DESC;';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hotelId', $hotelId, PDO::PARAM_INT);
    $stmt->execute();

    $hotelDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($hotelDetails) {
        $sqlImages = 'SELECT hotelImageName AS hotelImage FROM hotel_images WHERE hotelKey = :hotelKey';
        $stmtImages = $conn->prepare($sqlImages);
        $stmtImages->bindParam(':hotelKey', $hotelDetails['hotelKey'], PDO::PARAM_STR);
        $stmtImages->execute();
        $hotelImages = $stmtImages->fetchAll(PDO::FETCH_COLUMN);

        $hotelDetails['hotelImages'] = $hotelImages;

    }

    return $hotelDetails;
}
function newHotel($conn, $hotelKey, $data) {
    newHotelBed($conn, $hotelKey, $data['beds']);
    newHotelService($conn, $hotelKey, $data['services']);
    newHotelLocation($conn, $hotelKey, $data['location']);
    newHotelThumbnail($conn,  $hotelKey, $data);
    foreach($data['hotelImages'] as $image){
        newHotelImage($conn, $hotelKey, $image);
    };
    $sql = "INSERT INTO hotels (hotelKey, roomSize, name, phone, price) VALUES (:hotelKey, :roomSize, :name, :phone, :price)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR);
    $stmt->bindParam(':roomSize', $data['roomSize'], PDO::PARAM_INT);
    $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
    $stmt->bindParam(':phone', $data['phone'], PDO::PARAM_STR);
    $stmt->bindParam(':price', $data['price'], PDO::PARAM_STR);
    return $stmt->execute();
}
function newHotelBed($conn, $hotelKey, $data) {
    $sql = "INSERT INTO hotel_beds 
    (hotelKey, singleBedded, twinBedded, kingSize)
    VALUES (:hotelKey, :singleBedded, :twinBedded, :kingSize)
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR);
    $stmt->bindParam(':singleBedded', $data['singleBedded'], PDO::PARAM_INT);
    $stmt->bindParam(':twinBedded', $data['twinBedded'], PDO::PARAM_INT);
    $stmt->bindParam(':kingSize', $data['kingSize'], PDO::PARAM_INT);
    return $stmt->execute();
}
function newHotelService($conn, $hotelKey, $data) {
    $sql = "INSERT INTO hotel_services 
    (hotelKey, windows, freeWifi, airConditioner, privateBathroom, bath, fridge)
    VALUES (:hotelKey, :windows, :freeWifi, :airConditioner, :privateBathroom, :bath, :fridge)
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR);
    $stmt->bindParam(':windows', $data['windows'], PDO::PARAM_INT);
    $stmt->bindParam(':freeWifi', $data['freeWifi'], PDO::PARAM_INT);
    $stmt->bindParam(':airConditioner', $data['airConditioner'], PDO::PARAM_INT);
    $stmt->bindParam(':privateBathroom', $data['privateBathroom'], PDO::PARAM_INT);
    $stmt->bindParam(':bath', $data['bath'], PDO::PARAM_INT);
    $stmt->bindParam(':fridge', $data['fridge'], PDO::PARAM_INT);
    return $stmt->execute();
}
function newHotelLocation($conn, $hotelKey, $data) {
    $sql = "INSERT INTO hotel_locations 
    (hotelKey, geocode, address, country, province, district, subdistrict, postcode, elevation, road, latitude, longitude)
    VALUES (:hotelKey, :geocode, :address, :country, :province, :district, :subdistrict, :postcode, :elevation, :road, :latitude, :longitude)
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR);
    $stmt->bindParam(':geocode', $data['geocode'], PDO::PARAM_STR);
    $stmt->bindParam(':address', $data['address'], PDO::PARAM_STR);
    $stmt->bindParam(':country', $data['country'], PDO::PARAM_STR);
    $stmt->bindParam(':province', $data['province'], PDO::PARAM_STR);
    $stmt->bindParam(':district', $data['district'], PDO::PARAM_STR);
    $stmt->bindParam(':subdistrict', $data['subdistrict'], PDO::PARAM_STR);
    $stmt->bindParam(':postcode', $data['postcode'], PDO::PARAM_STR);
    $stmt->bindParam(':elevation', $data['elevation'], PDO::PARAM_STR);
    $stmt->bindParam(':road', $data['road'], PDO::PARAM_STR);
    $stmt->bindParam(':latitude', $data['latitude'], PDO::PARAM_STR);
    $stmt->bindParam(':longitude', $data['longitude'], PDO::PARAM_STR);
    return $stmt->execute();
}
function newHotelThumbnail($conn, $hotelKey, $data) {
    $sql = "INSERT INTO hotel_thumbnails (hotelKey, hotelImageName) VALUES (:hotelKey, :hotelImageName)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR);
    $stmt->bindParam(':hotelImageName', $data['hotelThumbnail'], PDO::PARAM_STR);
    return $stmt->execute();
}
function newHotelImage($conn, $hotelKey ,$image){
    $sql = "INSERT INTO hotel_images (hotelKey, hotelImageName) VALUES (:hotelKey, :hotelImageName)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR);
    $stmt->bindParam(':hotelImageName', $image, PDO::PARAM_STR);
    return $stmt->execute();
}
function updateHotel($conn, $hotelKey, $data) {
    updateHotelBed($conn, $hotelKey, $data['beds']);
    updateHotelService($conn, $hotelKey, $data['services']);
    updateHotelLocation($conn, $hotelKey, $data['location']);
    updateHotelThumbnail($conn, $hotelKey, $data['hotelThumbnail']);
    updateHotelImages($conn, $hotelKey, $data['hotelImages']);
    $sql = "UPDATE hotels 
    SET name = :name,
    roomSize = :roomSize,
    phone = :phone,
    price = :price
    WHERE hotelKey = :hotelKey;";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR);
    $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
    $stmt->bindParam(':roomSize', $data['roomSize'], PDO::PARAM_INT);
    $stmt->bindParam(':phone', $data['phone'], PDO::PARAM_STR);
    $stmt->bindParam(':price', $data['price'], PDO::PARAM_STR);
    return $stmt->execute();
}
function updateHotelBed($conn, $hotelKey, $data) {
    $sql = "UPDATE hotel_beds
    SET singleBedded = :singleBedded,
    twinBedded = :twinBedded,
    kingSize = :kingSize
    WHERE hotelKey = :hotelKey;";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR);
    $stmt->bindParam(':singleBedded', $data['singleBedded'], PDO::PARAM_INT);
    $stmt->bindParam(':twinBedded', $data['twinBedded'], PDO::PARAM_INT);
    $stmt->bindParam(':kingSize', $data['kingSize'], PDO::PARAM_INT);
    return $stmt->execute();
}
function updateHotelService($conn, $hotelKey, $data) {
    $sql = "UPDATE hotel_services
    SET windows = :windows,
    freeWifi = :freeWifi,
    airConditioner = :airConditioner,
    privateBathroom = :privateBathroom,
    bath = :bath,
    fridge = :fridge
    WHERE hotelKey = :hotelKey;";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR);
    $stmt->bindParam(':windows', $data['windows'], PDO::PARAM_INT);
    $stmt->bindParam(':freeWifi', $data['freeWifi'], PDO::PARAM_INT);
    $stmt->bindParam(':airConditioner', $data['airConditioner'], PDO::PARAM_INT);
    $stmt->bindParam(':privateBathroom', $data['privateBathroom'], PDO::PARAM_INT);
    $stmt->bindParam(':bath', $data['bath'], PDO::PARAM_INT);
    $stmt->bindParam(':fridge', $data['fridge'], PDO::PARAM_INT);
    return $stmt->execute();
}
function updateHotelLocation($conn, $hotelKey, $data) {
    $sql = "UPDATE hotel_locations
    SET geocode = :geocode,
    address = :address,
    country = :country,
    province = :province,
    district = :district,
    subdistrict = :subdistrict,
    postcode = :postcode,
    elevation = :elevation,
    road = :road,
    latitude = :latitude,
    longitude = :longitude
    WHERE hotelKey = :hotelKey;";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR);
    $stmt->bindParam(':geocode', $data['geocode'], PDO::PARAM_STR);
    $stmt->bindParam(':address', $data['address'], PDO::PARAM_STR);
    $stmt->bindParam(':country', $data['country'], PDO::PARAM_STR);
    $stmt->bindParam(':province', $data['province'], PDO::PARAM_STR);
    $stmt->bindParam(':district', $data['district'], PDO::PARAM_STR);
    $stmt->bindParam(':subdistrict', $data['subdistrict'], PDO::PARAM_STR);
    $stmt->bindParam(':postcode', $data['postcode'], PDO::PARAM_STR);
    $stmt->bindParam(':elevation', $data['elevation'], PDO::PARAM_STR);
    $stmt->bindParam(':road', $data['road'], PDO::PARAM_STR);
    $stmt->bindParam(':latitude', $data['latitude'], PDO::PARAM_STR);
    $stmt->bindParam(':longitude', $data['longitude'], PDO::PARAM_STR);
    return $stmt->execute();
}
function updateHotelThumbnail($conn, $hotelKey, $data) {
    $sql = "UPDATE hotel_thumbnails SET hotelImageName = :hotelImageName WHERE hotelKey = :hotelKey;";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR);
    $stmt->bindParam(':hotelImageName', $data, PDO::PARAM_STR);
    return $stmt->execute();
}
function updateHotelImages($conn, $hotelKey, $images) {
    $sql = "SELECT hotelImageName FROM hotel_images WHERE hotelKey = :hotelKey";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR);
    $stmt->execute();
    $currentImages = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($currentImages as $currentImage) {
        if (!in_array($currentImage, $images)) {
            $sql = "DELETE FROM hotel_images WHERE hotelKey = :hotelKey AND hotelImageName = :hotelImageName";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR);
            $stmt->bindParam(':hotelImageName', $currentImage, PDO::PARAM_STR);
            $stmt->execute();
        }
    }

    foreach ($images as $newImage) {
        if (!in_array($newImage, $currentImages)) {
            $sql = "INSERT INTO hotel_images (hotelKey, hotelImageName) VALUES (:hotelKey, :hotelImageName)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR);
            $stmt->bindParam(':hotelImageName', $newImage, PDO::PARAM_STR);
            $stmt->execute();
        }
    }
}
function removeHotel($conn, $hotelKey) {
    removeHotelBed($conn, $hotelKey);
    removeHotelService($conn, $hotelKey);
    removeHotelLocation($conn, $hotelKey);
    removeHotelThumbnail($conn, $hotelKey);
    removeHotelImages($conn, $hotelKey);
    $sql = 'DELETE FROM hotels WHERE hotelKey = :hotelKey';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR); 
    return $stmt->execute();
}
function removeHotelBed($conn, $hotelKey) {
    $sql = 'DELETE FROM hotel_beds WHERE hotelKey = :hotelKey';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR); 
    return $stmt->execute();
}
function removeHotelService($conn, $hotelKey) {
    $sql = 'DELETE FROM hotel_services WHERE hotelKey = :hotelKey';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR); 
    return $stmt->execute();
}
function removeHotelLocation($conn, $hotelKey) {
    $sql = 'DELETE FROM hotel_locations WHERE hotelKey = :hotelKey';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR); 
    return $stmt->execute();
}
function removeHotelThumbnail($conn, $hotelKey) {
    removeFolder("./public/hotels/thumbnails/{$hotelKey}");
    $sql = 'DELETE FROM hotel_thumbnails WHERE hotelKey = :hotelKey';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR); 
    return $stmt->execute();
}
function removeHotelImages($conn, $hotelKey) {
    removeFolder("./public/hotels/images/{$hotelKey}");
    $sql = 'DELETE FROM hotel_images WHERE hotelKey = :hotelKey';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hotelKey', $hotelKey, PDO::PARAM_STR); 
    return $stmt->execute();
}
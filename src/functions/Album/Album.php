<?php 
require_once './functions/lib/dir.php';
function getAlbums($conn, $filterText = '', $numRows = 10, $offset = 0) {
    $sql = "SELECT 
        albums.albumKey, 
        albums.albumName, 
        albums.courseKey, 
        albums.createdAt,
        course.id as courseId,
        course.courseTitle
    FROM albums 
    LEFT JOIN courses AS course ON albums.courseKey = course.courseKey
    WHERE 1=1 ";
    
    $params = [];
    
    if (!empty($filterText)) {
        $sql .= "AND (albums.albumName LIKE :filterText OR course.courseTitle LIKE :filterText) ";
        $params[':filterText'] = '%' . $filterText . '%';
    }
    
    $sql .= "ORDER BY albums.createdAt DESC
             LIMIT :offset, :numRows";
    
    $stmt = $conn->prepare($sql);
    
    foreach ($params as $key => &$val) {
        $stmt->bindParam($key, $val, PDO::PARAM_STR);
    }
    
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':numRows', $numRows, PDO::PARAM_INT);
    
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}
function getAlbumByKey($conn, $albumKey) {
    $sql = 'SELECT 
        albums.albumKey, 
        albums.albumName, 
        albums.courseKey, 
        albums.createdAt,
        course.courseTitle
        FROM albums 
        LEFT JOIN courses AS course ON albums.courseKey = course.courseKey
        WHERE albumKey = :albumKey;';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':albumKey', $albumKey, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); 
}
function getAlbumWithImages($conn, $albumKey) {
    $sqlAlbum = 'SELECT 
        albums.albumName, 
        albums.albumKey
        FROM albums 
        WHERE albumKey = :albumKey;';
    $stmtAlbum = $conn->prepare($sqlAlbum);
    $stmtAlbum->bindParam(':albumKey', $albumKey, PDO::PARAM_STR);
    $stmtAlbum->execute();
    $albumDetails = $stmtAlbum->fetch(PDO::FETCH_ASSOC); 

    if($albumDetails){
        $sqlImages = 'SELECT id, ImageName FROM album_images WHERE albumKey = :albumKey';
        $stmtImages = $conn->prepare($sqlImages);
        $stmtImages->bindParam(':albumKey', $albumKey, PDO::PARAM_STR);
        $stmtImages->execute();
        $images = $stmtImages->fetchAll(PDO::FETCH_ASSOC);
        $albumDetails['albumImages'] = $images;
    }
    return $albumDetails;
}
function getCourseOptions($conn) {
    $sql = 'SELECT courses.courseKey, courses.courseTitle FROM courses;';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function newAlbum($conn, $album) {
    createFolder("./public/albums/{$album['albumKey']}/");
    $sql = "INSERT INTO albums (albumKey, albumName, courseKey) VALUES (:albumKey, :albumName, :courseKey)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':albumKey', $album['albumKey'], PDO::PARAM_STR);
    $stmt->bindParam(':albumName', $album['albumName'], PDO::PARAM_STR);
    $stmt->bindParam(':courseKey', $album['courseKey'], PDO::PARAM_STR);
    return $stmt->execute();
}
function uploadImages($conn, $albumKey, $images) {
    $success = true;
    foreach ($images as $image) {
        $sql = "INSERT INTO album_images (albumKey, ImageName) VALUES (:albumKey, :ImageName)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':albumKey', $albumKey, PDO::PARAM_STR);
        $stmt->bindParam(':ImageName', $image, PDO::PARAM_STR);
        if (!$stmt->execute()) {
            $success = false;
            break;
        }
    }
    return $success;
}
function getAlbumByKeyForRemove($conn, $albumKey) {
    $sql = 'SELECT albums.albumName
            FROM albums
            WHERE albums.albumKey = :albumKey';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':albumKey', $albumKey, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getImageByIdForRemove($conn, $albumKey, $imageId) {
    $sql = 'SELECT ImageName
            FROM album_images as image
            WHERE image.albumKey = :albumKey AND image.id = :imageId';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':albumKey', $albumKey, PDO::PARAM_STR);
    $stmt->bindParam(':imageId', $imageId, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function updateAlbum($conn, $albumKey, $data) {
    $sql = "UPDATE albums 
    SET albumName = :albumName,
    courseKey = :courseKey
    WHERE albumKey = :albumKey;";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':albumKey', $albumKey, PDO::PARAM_STR);
    $stmt->bindParam(':albumName', $data['albumName'], PDO::PARAM_STR);
    $stmt->bindParam(':courseKey', $data['courseKey'], PDO::PARAM_STR);
    return $stmt->execute();
}
function removeAlbum($conn, $albumKey) {
    removeImages($conn, $albumKey);
    $sql = 'DELETE FROM albums WHERE albumKey = :albumKey';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':albumKey', $albumKey, PDO::PARAM_STR); 
    return $stmt->execute();
}
function removeImage($conn, $albumKey, $imageId, $imageName) {
    removeFile("./public/albums/{$albumKey}/{$imageName}");
    $sql = 'DELETE FROM album_images WHERE albumKey = :albumKey AND id = :imageId';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':albumKey', $albumKey, PDO::PARAM_STR); 
    $stmt->bindParam(':imageId', $imageId, PDO::PARAM_STR); 
    return $stmt->execute();
}
function removeImages($conn, $albumKey) {
    removeFolder("./public/albums/{$albumKey}");
    $sql = 'DELETE FROM album_images WHERE albumKey = :albumKey';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':albumKey', $albumKey, PDO::PARAM_STR); 
    return $stmt->execute();
}

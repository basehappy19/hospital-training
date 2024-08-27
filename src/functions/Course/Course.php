<?php
function getCourseHomePage($conn, $view) {
    $sql = 'SELECT 
    courses.id, 
    courses.courseKey, 
    courses.courseTitle, 
    courses.courseShowHomepage, 
    detail.courseStartDate, 
    detail.courseEndDate, 
    detail.courseStartEnrollDate, 
    detail.courseEndEnrollDate, 
    detail.courseEnrollFee, 
    detail.courseLimit, 
    detail.courseTarget, 
    detail.courseLocation, 
    detail.courseOnline,
    detail.courseOnsite, 
    thumbnail.courseImageName AS courseThumbnail, 
    COALESCE(attendee.count, 0) AS participants FROM courses
    LEFT JOIN course_details AS detail ON courses.courseKey = detail.courseKey 
    LEFT JOIN course_thumbnails AS thumbnail ON courses.courseKey = thumbnail.courseKey 
    LEFT JOIN (SELECT courseKey, COUNT(*) AS count FROM course_attends GROUP BY courseKey) AS attendee ON courses.courseKey = attendee.courseKey
    WHERE courses.courseShowHomepage = 1';

    if ($view == 0) {
        $sql .= ' AND detail.courseOpen = 1';
    }

    $sql .= ' ORDER BY courses.createdAt DESC;';
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}
function getCourse($conn, $view) {
    $sql = 'SELECT 
    courses.id, 
    courses.courseKey, 
    courses.courseTitle, 
    categories.categoryTitle,
    detail.courseStartDate, 
    detail.courseEndDate, 
    detail.courseStartEnrollDate, 
    detail.courseEndEnrollDate, 
    detail.courseEnrollFee, 
    detail.courseLimit, 
    detail.courseTarget, 
    detail.courseLocation, 
    detail.courseOnline,
    detail.courseOnsite, 
    detail.courseOpen, 
    thumbnail.courseImageName AS courseThumbnail, 
    COALESCE(attendee.count, 0) AS participants FROM courses 
    LEFT JOIN course_details AS detail ON courses.courseKey = detail.courseKey 
    LEFT JOIN course_thumbnails AS thumbnail ON courses.courseKey = thumbnail.courseKey 
    LEFT JOIN categories ON courses.courseCategoryId = categories.id 
    LEFT JOIN (SELECT courseKey, COUNT(*) AS count FROM course_attends GROUP BY courseKey) AS attendee ON courses.courseKey = attendee.courseKey';

    if ($view == 0) {
        $sql .= ' WHERE detail.courseOpen = 1';
    }

    $sql .= ' ORDER BY courses.createdAt DESC;';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}
function getCourseFilter($conn, $id, $view) {
    $sql = 'SELECT 
    courses.id, 
    courses.courseKey, 
    courses.courseTitle, 
    categories.categoryTitle, 
    detail.courseStartDate, 
    detail.courseEndDate, 
    detail.courseStartEnrollDate, 
    detail.courseEndEnrollDate, 
    detail.courseEnrollFee, 
    detail.courseLimit, 
    detail.courseTarget, 
    detail.courseLocation, 
    detail.courseOnline,
    detail.courseOnsite, 
    detail.courseOpen, 
    thumbnail.courseImageName AS courseThumbnail, 
    COALESCE(attendee.count, 0) AS participants FROM courses 
    LEFT JOIN course_details AS detail ON courses.courseKey = detail.courseKey 
    LEFT JOIN course_thumbnails AS thumbnail ON courses.courseKey = thumbnail.courseKey 
    LEFT JOIN categories ON courses.courseCategoryId = categories.id 
    LEFT JOIN (SELECT courseKey, COUNT(*) AS count FROM course_attends GROUP BY courseKey) AS attendee ON courses.courseKey = attendee.courseKey
    WHERE courses.courseCategoryId = :id';

    if ($view == 0) {
        $sql .= ' AND detail.courseOpen = 1';
    }

    $sql .= ' ORDER BY courses.createdAt DESC;';

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}
function getCourseById($conn, $id) {
    $sql = 'SELECT 
                courses.id, 
                courses.courseKey, 
                courses.courseTitle, 
                courses.courseCategoryId, 
                courses.courseShowHomepage, 
                categories.categoryTitle, 
                detail.courseStartDate, 
                detail.courseEndDate, 
                detail.courseStartEnrollDate, 
                detail.courseEndEnrollDate, 
                detail.courseEnrollFee, 
                detail.courseLimit, 
                detail.courseTarget, 
                detail.courseLocation, 
                detail.courseFoodQuestion, 
                detail.courseOnline, 
                detail.courseOnsite, 
                detail.courseOpen,  
                thumbnail.courseImageName AS courseThumbnail, 
                COALESCE(attendee.count, 0) AS participants 
            FROM courses 
            LEFT JOIN course_details AS detail ON courses.courseKey = detail.courseKey 
            LEFT JOIN course_thumbnails AS thumbnail ON courses.courseKey = thumbnail.courseKey 
            LEFT JOIN categories ON courses.courseCategoryId = categories.id 
            LEFT JOIN (SELECT courseKey, COUNT(*) AS count FROM course_attends GROUP BY courseKey) AS attendee ON courses.courseKey = attendee.courseKey 
            WHERE courses.id = :id';
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $courseDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($courseDetails) {
        $sqlImages = 'SELECT courseImageName AS courseImage FROM course_images WHERE courseKey = :courseKey';
        $stmtImages = $conn->prepare($sqlImages);
        $stmtImages->bindParam(':courseKey', $courseDetails['courseKey'], PDO::PARAM_STR);
        $stmtImages->execute();
        $courseImages = $stmtImages->fetchAll(PDO::FETCH_COLUMN);

        $courseDetails['courseImages'] = $courseImages;

        $sqlFiles = 'SELECT id, courseFileName, courseFileTitle, courseFileType, courseFileSize FROM course_files WHERE courseKey = :courseKey';
        $stmtFiles = $conn->prepare($sqlFiles);
        $stmtFiles->bindParam(':courseKey', $courseDetails['courseKey'], PDO::PARAM_STR);
        $stmtFiles->execute();
        $courseFiles = $stmtFiles->fetchAll(PDO::FETCH_ASSOC);

        $courseDetails['courseFiles'] = $courseFiles;
    }

    return $courseDetails;
}
function newCourse($conn, $data) {
    newCourseDetail($conn, $data);
    newCourseThumbnail($conn, $data);
    if (!empty($data['file'])) {
        newCourseFile($conn, $data['courseKey'],  $data['files']);
    }
    foreach($data['courseImages'] as $image){
        newCourseImage($conn, $data['courseKey'], $image);
    };
    $sql = "INSERT INTO courses (courseKey, courseTitle, courseCategoryId) VALUES (:courseKey, :courseTitle, :courseCategoryId)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $data['courseKey'], PDO::PARAM_STR);
    $stmt->bindParam(':courseTitle', $data['courseTitle'], PDO::PARAM_STR);
    $stmt->bindParam(':courseCategoryId', $data['courseCategoryId'], PDO::PARAM_STR);
    return $stmt->execute();
}
function newCourseDetail($conn, $data) {
    $sql = "INSERT INTO course_details 
    (courseKey, courseStartDate, courseEndDate, courseStartEnrollDate, courseEndEnrollDate, courseEnrollFee, courseLimit, courseTarget, courseLocation, courseFoodQuestion, courseOnline, courseOnsite)
    VALUES (:courseKey, :courseStartDate, :courseEndDate, :courseStartEnrollDate, :courseEndEnrollDate, :courseEnrollFee, :courseLimit, :courseTarget, :courseLocation, :courseFoodQuestion, :courseOnline, :courseOnsite)
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $data['courseKey'], PDO::PARAM_STR);
    $stmt->bindParam(':courseStartDate', $data['courseDate']['courseStartDate'], PDO::PARAM_STR);
    $stmt->bindParam(':courseEndDate', $data['courseDate']['courseEndDate'], PDO::PARAM_STR);
    $stmt->bindParam(':courseStartEnrollDate', $data['courseDate']['courseStartEnrollDate'], PDO::PARAM_STR);
    $stmt->bindParam(':courseEndEnrollDate', $data['courseDate']['courseEndEnrollDate'], PDO::PARAM_STR);
    $stmt->bindParam(':courseEnrollFee', $data['courseEnrollFee'], PDO::PARAM_INT);
    $stmt->bindParam(':courseLimit', $data['courseLimit'], PDO::PARAM_INT);
    $stmt->bindParam(':courseTarget', $data['courseTarget'], PDO::PARAM_STR);
    $stmt->bindParam(':courseLocation', $data['courseLocation'], PDO::PARAM_STR);
    $stmt->bindParam(':courseFoodQuestion', $data['courseFood'], PDO::PARAM_INT);
    $stmt->bindParam(':courseOnline', $data['courseOnline'], PDO::PARAM_INT);
    $stmt->bindParam(':courseOnsite', $data['courseOnsite'], PDO::PARAM_INT);
    return $stmt->execute();
}
function newCourseThumbnail($conn, $data) {
    $sql = "INSERT INTO course_thumbnails (courseKey, courseImageName) VALUES (:courseKey, :courseImageName)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $data['courseKey'], PDO::PARAM_STR);
    $stmt->bindParam(':courseImageName', $data['courseThumbnail'], PDO::PARAM_STR);
    return $stmt->execute();
}
function newCourseImage($conn, $courseKey ,$image){
    $sql = "INSERT INTO course_images (courseKey, courseImageName) VALUES (:courseKey, :courseImageName)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $courseKey, PDO::PARAM_STR);
    $stmt->bindParam(':courseImageName', $image, PDO::PARAM_STR);
    return $stmt->execute();
}
function newCourseFile($conn, $courseKey, $file){
    $sql = "INSERT INTO course_files (courseKey, courseFileName, courseFileTitle, courseFileType, courseFileSize) VALUES (:courseKey, :courseFileName, :courseFileTitle, :courseFileType, :courseFileSize)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $courseKey, PDO::PARAM_STR);
    $stmt->bindParam(':courseFileName', $file['fileName'], PDO::PARAM_STR);
    $stmt->bindParam(':courseFileTitle', $file['fileTitle'], PDO::PARAM_STR);
    $stmt->bindParam(':courseFileType', $file['fileType'], PDO::PARAM_STR);
    $stmt->bindParam(':courseFileSize', $file['fileSize'], PDO::PARAM_INT);
    return $stmt->execute();
}
function updateCourse($conn, $data) {
    updateCourseDetail($conn, $data);
    updateCourseThumbnail($conn, $data);
    updateCourseImages($conn, $data['courseKey'], $data['courseImages']);
    $sql = "UPDATE courses 
    SET courseTitle = :courseTitle,
    courseCategoryId = :courseCategoryId,
    courseShowHomepage = :courseShowHomepage
    WHERE courseKey = :courseKey;";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $data['courseKey'], PDO::PARAM_STR);
    $stmt->bindParam(':courseTitle', $data['courseTitle'], PDO::PARAM_STR);
    $stmt->bindParam(':courseCategoryId', $data['courseCategoryId'], PDO::PARAM_STR);
    $stmt->bindParam(':courseShowHomepage', $data['courseShowHomepage'], PDO::PARAM_INT);
    return $stmt->execute();
}
function updateCourseDetail($conn, $data) {
    $sql = "UPDATE course_details
    SET courseStartDate = :courseStartDate,
    courseEndDate = :courseEndDate,
    courseStartEnrollDate = :courseStartEnrollDate,
    courseEndEnrollDate = :courseEndEnrollDate,
    courseEnrollFee = :courseEnrollFee,
    courseLimit = :courseLimit,
    courseTarget = :courseTarget,
    courseLocation = :courseLocation,
    courseFoodQuestion = :courseFoodQuestion,
    courseOnline = :courseOnline,
    courseOnsite = :courseOnsite,
    courseOpen = :courseOpen
    WHERE courseKey = :courseKey;";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $data['courseKey'], PDO::PARAM_STR);
    $stmt->bindParam(':courseStartDate', $data['courseDate']['courseStartDate'], PDO::PARAM_STR);
    $stmt->bindParam(':courseEndDate', $data['courseDate']['courseEndDate'], PDO::PARAM_STR);
    $stmt->bindParam(':courseStartEnrollDate', $data['courseDate']['courseStartEnrollDate'], PDO::PARAM_STR);
    $stmt->bindParam(':courseEndEnrollDate', $data['courseDate']['courseEndEnrollDate'], PDO::PARAM_STR);
    $stmt->bindParam(':courseEnrollFee', $data['courseEnrollFee'], PDO::PARAM_INT);
    $stmt->bindParam(':courseLimit', $data['courseLimit'], PDO::PARAM_INT);
    $stmt->bindParam(':courseTarget', $data['courseTarget'], PDO::PARAM_STR);
    $stmt->bindParam(':courseLocation', $data['courseLocation'], PDO::PARAM_STR);
    $stmt->bindParam(':courseFoodQuestion', $data['courseFood'], PDO::PARAM_INT);
    $stmt->bindParam(':courseOnline', $data['courseOnline'], PDO::PARAM_INT);
    $stmt->bindParam(':courseOnsite', $data['courseOnsite'], PDO::PARAM_INT);
    $stmt->bindParam(':courseOpen', $data['courseOpen'], PDO::PARAM_INT);
    return $stmt->execute();
}
function updateCourseThumbnail($conn, $data) {
    $sql = "UPDATE course_thumbnails SET courseImageName = :courseImageName WHERE courseKey = :courseKey;";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $data['courseKey'], PDO::PARAM_STR);
    $stmt->bindParam(':courseImageName', $data['courseThumbnail'], PDO::PARAM_STR);
    return $stmt->execute();
}
function updateCourseImages($conn, $courseKey, $images) {
    $sql = "SELECT courseImageName FROM course_images WHERE courseKey = :courseKey";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $courseKey, PDO::PARAM_STR);
    $stmt->execute();
    $currentImages = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($currentImages as $currentImage) {
        if (!in_array($currentImage, $images)) {
            $sql = "DELETE FROM course_images WHERE courseKey = :courseKey AND courseImageName = :courseImageName";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':courseKey', $courseKey, PDO::PARAM_STR);
            $stmt->bindParam(':courseImageName', $currentImage, PDO::PARAM_STR);
            $stmt->execute();
        }
    }

    foreach ($images as $newImage) {
        if (!in_array($newImage, $currentImages)) {
            $sql = "INSERT INTO course_images (courseKey, courseImageName) VALUES (:courseKey, :courseImageName)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':courseKey', $courseKey, PDO::PARAM_STR);
            $stmt->bindParam(':courseImageName', $newImage, PDO::PARAM_STR);
            $stmt->execute();
        }
    }
}
function getCourseByKeyForRemove($conn, $courseKey) {
    $sql = 'SELECT courses.courseTitle, thumbnail.courseImageName AS courseThumbnail
            FROM courses
            LEFT JOIN course_thumbnails AS thumbnail ON courses.courseKey = thumbnail.courseKey 
            WHERE courses.courseKey = :courseKey';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $courseKey, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getFileByIdForRemove($conn, $fileId) {
    $sql = 'SELECT id, courseKey, courseFileName, courseFileTitle FROM course_files WHERE id = :fileId';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':fileId', $fileId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function removeCourse($conn, $courseKey) {
    removeCourseDetail($conn, $courseKey);
    removeCourseThumbnail($conn, $courseKey);
    removeCourseImages($conn, $courseKey);
    removeCourseFiles($conn, $courseKey);
    removeCourseAttend($conn, $courseKey);
    $sql = 'DELETE FROM courses WHERE courseKey = :courseKey';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $courseKey, PDO::PARAM_STR); 
    return $stmt->execute();
}
function removeCourseDetail($conn, $courseKey) {
    $sql = 'DELETE FROM course_details WHERE courseKey = :courseKey';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $courseKey, PDO::PARAM_STR); 
    return $stmt->execute();
}
function removeCourseThumbnail($conn, $courseKey) {
    removeFolder("./public/courses/thumbnails/{$courseKey}");
    $sql = 'DELETE FROM course_thumbnails WHERE courseKey = :courseKey';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $courseKey, PDO::PARAM_STR); 
    return $stmt->execute();
}
function removeCourseImages($conn, $courseKey) {
    removeFolder("./public/courses/images/{$courseKey}");
    $sql = 'DELETE FROM course_images WHERE courseKey = :courseKey';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $courseKey, PDO::PARAM_STR); 
    return $stmt->execute();
}
function removeCourseFiles($conn, $courseKey) {
    removeFolder("./public/courses/files/{$courseKey}");
    $sql = 'DELETE FROM course_images WHERE courseKey = :courseKey';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $courseKey, PDO::PARAM_STR); 
    return $stmt->execute();
}
function RemoveCourseFile($conn, $courseKey, $fileId, $fileName) {
    removeFile("./public/courses/files/{$courseKey}/{$fileName}");
    $sql = 'DELETE FROM course_files WHERE id = :fileId AND courseKey = :courseKey';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $courseKey, PDO::PARAM_STR); 
    $stmt->bindParam(':fileId', $fileId, PDO::PARAM_INT); 
    return $stmt->execute();

}
function RemoveCourseAttend($conn, $courseKey) {
    removeFolder("./uploads/payment-proof/{$courseKey}");
    $sql = 'DELETE FROM course_attends WHERE courseKey = :key';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':key', $courseKey, PDO::PARAM_STR); 
    return $stmt->execute();
}




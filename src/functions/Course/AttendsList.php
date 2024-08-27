<?php
function getCourseAttendsList($conn, $courseKey, $limit, $offset) {
    $sql = 'SELECT 
                course_attends.id, 
                course_attends.fullname, 
                course_attends.phone, 
                course_attends.email, 
                course_attends.institution, 
                course_attends.enrollType, 
                course_attends.foodType, 
                course_attends.enrollTime
            FROM course_attends
            WHERE course_attends.courseKey = :courseKey
            ORDER BY course_attends.enrollTime DESC
            LIMIT :limit
            OFFSET :offset';

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $courseKey, PDO::PARAM_STR);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

    $limit = (int)$limit;
    $offset = (int)$offset;

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getTotalCourseAttends($conn, $courseKey) {
    $sql = 'SELECT COUNT(*) AS total
            FROM course_attends
            WHERE course_attends.courseKey = :courseKey';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $courseKey, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
}
function getAttendDetail($conn, $courseKey, $attendId) {
    $sql = 'SELECT 
                course_attends.id, 
                course_attends.courseKey, 
                course_attends.enrollCode, 
                course_attends.fullname, 
                course_attends.phone, 
                course_attends.email, 
                course_attends.institution, 
                course_attends.enrollType, 
                course_attends.foodType, 
                p.paymentProof,
                p.statusPayment,
                course_attends.enrollTime
            FROM course_attends
            LEFT JOIN paymentproof AS p ON course_attends.enrollCode = p.enrollCode
            WHERE course_attends.courseKey = :courseKey AND course_attends.id = :attendId';

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $courseKey, PDO::PARAM_STR);
    $stmt->bindParam(':attendId', $attendId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function checkAttendPaymentUpdate($conn, $courseKey, $enrollCode) {
    $sql = 'SELECT 
        course_attends.courseKey, 
        course_attends.enrollCode, 
        course_attends.fullname
    FROM course_attends
    LEFT JOIN paymentproof AS p ON course_attends.enrollCode = p.enrollCode
    WHERE course_attends.courseKey = :courseKey AND course_attends.enrollCode = :enrollCode';

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $courseKey, PDO::PARAM_STR);
    $stmt->bindParam(':enrollCode', $enrollCode, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
}
function updatePayment($conn, $courseKey, $enrollCode) {
    $sql = 'UPDATE paymentproof SET statusPayment = "1" WHERE courseKey = :courseKey AND enrollCode = :enrollCode';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $courseKey, PDO::PARAM_STR);
    $stmt->bindParam(':enrollCode', $enrollCode, PDO::PARAM_STR);
    return $stmt->execute();
}

function updateAttendDetail($conn, $courseKey, $enrollCode, $data) {
    $sql = 'UPDATE course_attends
    SET fullname = :fullname, 
    phone = :phone, 
    email = :email, 
    institution = :institution, 
    enrollType = :enrollType, 
    foodType = :foodType
    WHERE courseKey = :courseKey AND enrollCode = :enrollCode';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $courseKey, PDO::PARAM_STR);
    $stmt->bindParam(':enrollCode', $enrollCode, PDO::PARAM_STR);
    $stmt->bindParam(':fullname', $data['fullname'], PDO::PARAM_STR);
    $stmt->bindParam(':phone', $data['phone'], PDO::PARAM_STR);
    $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
    $stmt->bindParam(':institution', $data['institution'], PDO::PARAM_STR);
    $stmt->bindParam(':enrollType', $data['enrollType'], PDO::PARAM_STR);
    $stmt->bindParam(':foodType', $data['foodType'], PDO::PARAM_STR);
    return $stmt->execute();
}
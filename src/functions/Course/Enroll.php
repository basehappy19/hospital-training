<?php
function checkEnrollAttend($conn, $fullname, $courseKey) {
    $sql = "SELECT courseKey, fullname FROM course_attends WHERE course_attends.fullname = :fullname AND course_attends.courseKey = :courseKey";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
    $stmt->bindParam(':courseKey', $courseKey, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return empty($result);
}


function enrollCourse($conn, $data) {
    paymentProof($conn, $data['courseKey'], $data['enrollCode'], $data['paymentProof']);
    $sql = "INSERT INTO course_attends 
    (courseKey, enrollCode, fullname, phone, email, institution, enrollType, foodType)
    VALUES (:courseKey, :enrollCode, :fullname, :phone, :email, :institution, :enrollType, :foodType)
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $data['courseKey'], PDO::PARAM_STR);
    $stmt->bindParam(':enrollCode', $data['enrollCode'], PDO::PARAM_STR);
    $stmt->bindParam(':fullname', $data['fullname'], PDO::PARAM_STR);
    $stmt->bindParam(':phone', $data['phone'], PDO::PARAM_STR);
    $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
    $stmt->bindParam(':institution', $data['institution'], PDO::PARAM_STR);
    $stmt->bindParam(':enrollType', $data['enrollType'], PDO::PARAM_STR);
    $stmt->bindParam(':foodType', $data['foodType'], PDO::PARAM_STR);
    return $stmt->execute();
}

function paymentProof($conn, $courseKey, $enrollCode, $paymentProof) {
    $sql = "INSERT INTO paymentproof (courseKey, enrollCode, paymentProof) VALUES (:courseKey, :enrollCode, :paymentProof)
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseKey', $courseKey, PDO::PARAM_STR);
    $stmt->bindParam(':enrollCode', $enrollCode, PDO::PARAM_STR);
    $stmt->bindParam(':paymentProof', $paymentProof, PDO::PARAM_STR);
    return $stmt->execute();
}
<?php

function login($conn, $username) {
    $sql = 'SELECT id, username, password FROM users WHERE username = :username';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function updateLoginAt($conn, $id) {
    date_default_timezone_set('Asia/Bangkok');
    $dateNow = (new DateTime())->format('Y-m-d H:i:s');
    $sql = 'UPDATE users SET loginAt = :loginAt WHERE id = :id';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':loginAt', $dateNow, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}




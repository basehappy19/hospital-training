<?php

function getUser($conn, $id) {
    $sql = 'SELECT id, username, password, displayName, canPostCourse, canManageUser, loginAt FROM users WHERE id = :id';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
}
function getAllUser($conn) {
    $sql = 'SELECT id, username, displayName, canPostCourse, canManageUser, loginAt FROM users';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function checkUser($conn, $username) {
    $sql = 'SELECT username FROM users WHERE username = :username;';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return empty($result);
}
function addUser($conn, $data) {
    $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

    $sql = 'INSERT INTO users
    (username, password, displayName, canPostCourse, canManageUser) 
    VALUES (:username, :password, :displayName, :canPostCourse, :canManageUser)';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $data['username'], PDO::PARAM_STR);
    $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
    $stmt->bindParam(':displayName', $data['displayName'], PDO::PARAM_STR);
    $stmt->bindParam(':canPostCourse', $data['canPostCourse'], PDO::PARAM_INT);
    $stmt->bindParam(':canManageUser', $data['canManageUser'], PDO::PARAM_INT);
    return $stmt->execute();
}
function editUser($conn, $data) {
    $sql = 'SELECT password FROM users WHERE username = :username;';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $data['username'], PDO::PARAM_STR);
    $stmt->execute();
    $checkUser = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if($checkUser['password'] != $data['password']) {
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
    } else {
        $hashed_password = $data['password'];
    }

    $sql = 'UPDATE users
                SET username = :username,
                password = :password,
                displayName = :displayName,
                canPostCourse = :canPostCourse, 
                canManageUser = :canManageUser
            WHERE id = :id';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_STR);
    $stmt->bindParam(':username', $data['username'], PDO::PARAM_STR);
    $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
    $stmt->bindParam(':displayName', $data['displayName'], PDO::PARAM_STR);
    $stmt->bindParam(':canPostCourse', $data['canPostCourse'], PDO::PARAM_INT);
    $stmt->bindParam(':canManageUser', $data['canManageUser'], PDO::PARAM_INT);
    return $stmt->execute();
}
function RemoveUser($conn, $id) {
    $sql = 'DELETE FROM users WHERE id = :id';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
    return $stmt->execute();
}

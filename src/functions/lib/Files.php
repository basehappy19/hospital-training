<?php

function upload($files, $uploadDir, $allowedTypes){
    if (in_array($files['type'], $allowedTypes)) {
        $fileExtension = pathinfo($files['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $fileExtension;
        $uploadFile = $uploadDir . $fileName;
        if (move_uploaded_file($files['tmp_name'], $uploadFile)) {
            return $fileName;
        }
    }
}

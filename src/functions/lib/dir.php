<?php

function createFolder($path) {
    if (!file_exists($path)) {
        if (!mkdir($path, 0777, true)) {
            return false;
        }
    }
    return true;
}
function removeFolder($path) {
    if (is_dir($path)) {
        $items = scandir($path);
        foreach ($items as $item) {
            if ($item != '.' && $item != '..') {
                $itemPath = $path . DIRECTORY_SEPARATOR . $item;
                if (is_dir($itemPath)) {
                    removeFolder($itemPath);
                } elseif (file_exists($itemPath)) {
                    @unlink($itemPath);
                }
            }
        }
        @rmdir($path);
    } elseif (file_exists($path)) {
        @unlink($path);
    }
}
function removeFile($path) {
    if (file_exists($path)) {
        unlink($path);
    }      
}
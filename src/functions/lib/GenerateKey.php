<?php
function generateKey() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $key = '';
    for ($i = 0; $i < 25; $i++) {
        $key .= $characters[rand(0, $charactersLength - 1)];
    }
    return $key;
}
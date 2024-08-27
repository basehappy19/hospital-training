<?php 
function convertThaiDateToGregorian($thaiDate) {
    list($day, $month, $year) = explode('-', $thaiDate);
    $gregorianYear = $year - 543;
    return "$gregorianYear-$month-$day";
}

function convertGregorianDateToThai($gregorianDate) {
    list($year, $month, $day) = explode('-', $gregorianDate);
    $thaiYear = $year + 543;
    return "$day-$month-$thaiYear";
}
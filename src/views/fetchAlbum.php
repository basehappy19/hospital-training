<?php
require_once './functions/Album/Album.php';
require_once './functions/lib/ConvertThaiDate.php';

global $conn;

$search = array(
    "filterText" => isset($_GET['s']) ? $_GET['s'] : "",
    "numRows" => isset($_GET['query']) ? $_GET['query'] : 10,
);

$albums = getAlbums($conn, $search['filterText'], $search['numRows']);
require_once './components/Album/AlbumTable.php';
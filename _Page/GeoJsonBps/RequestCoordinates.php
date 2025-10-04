<?php
// koneksi dan session
include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";
date_default_timezone_set("Asia/Jakarta");

// Header agar response berupa JSON
header('Content-Type: application/json');

// Validasi Akses
if(empty($SessionIdAccess)){
    http_response_code(401);
    echo json_encode(["error" => "Sesi akses sudah berakhir"]);
    exit;
}

// Validasi id_geo_region
if(empty($_POST['id_geo_region'])){
    http_response_code(400);
    echo json_encode(["error" => "id_geo_region tidak dikirim"]);
    exit;
}
$id_geo_region = $_POST['id_geo_region'];

// Ambil data coordinates
$coordinates = GetDetailData($Conn, 'geo_region', 'id_geo_region', $id_geo_region, 'coordinates');

// Jika kosong
if(empty($coordinates)){
    http_response_code(404);
    echo json_encode(["error" => "Data koordinat tidak ditemukan"]);
    exit;
}

// Karena kolom bertipe JSON, pastikan keluarannya valid JSON
echo $coordinates;

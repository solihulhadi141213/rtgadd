<?php
//Koneksi
include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";

//Validasi Session Akses
if(empty($SessionIdAccess)){
    echo json_encode(['error' => 'Sesi akses sudah berakhir']);
    exit;
}

//Tangkap id_geo_region
if(empty($_POST['id_geo_region'])){
    echo json_encode(['error' => 'ID wilayah harus diisi']);
    exit;
}

$id_geo_region = $_POST['id_geo_region'];

//Buka Data Dengan Prepared Statement
$Qry = $Conn->prepare("SELECT coordinates FROM geo_region WHERE id_geo_region = ?");
$Qry->bind_param("i", $id_geo_region);

if (!$Qry->execute()) {
    $error = $Conn->error;
    echo json_encode(['error' => 'Terjadi kesalahan: ' . $error]);
    exit;
}

$Result = $Qry->get_result();
if ($Result->num_rows === 0) {
    echo json_encode(['error' => 'Data tidak ditemukan']);
    exit;
}

$Data = $Result->fetch_assoc();
$Qry->close();

//Kembalikan data coordinates sebagai JSON
if (!empty($Data['coordinates'])) {
    // Validasi apakah coordinates adalah JSON yang valid
    $coordinates = $Data['coordinates'];
    
    // Coba decode untuk validasi
    json_decode($coordinates);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo $coordinates;
    } else {
        echo json_encode(['error' => 'Format GeoJSON tidak valid']);
    }
} else {
    echo json_encode(['error' => 'Data koordinat kosong']);
}
?>
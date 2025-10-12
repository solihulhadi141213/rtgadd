<?php
    // Inisiasi Type File
    header('Content-Type: application/json');

    // Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Validasi Sesi akses
    if (empty($SessionIdAccess)) {
        $response = [
            "code" => 201,
            "message" => "Sesi akses sudah berakhir. Silahkan <b>login</b> ulang!",
            "lulusan_ppg" => NULL
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }

    // Validasi province_code Tidak ada
    if(empty($_POST['province_code'])){
        $response = [
            "code" => 201,
            "message" => "Parameter <b>Kode Provinsi</b> tidak boleh kosong!",
            "lulusan_ppg" => NULL
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }

    // Sanitize input
    $province_code = $_POST['province_code'];
    $lulusan_ppg =0;
    //Looping kabupaten 
    $query_region = mysqli_query($Conn, "SELECT id_region FROM region WHERE category='District' AND province_code='$province_code'");
    while ($data_region = mysqli_fetch_array($query_region)) {
        $id_region = $data_region['id_region'];

        //Looping calon_guru
        $query_calon_guru = mysqli_query($Conn, "SELECT id_calon_guru FROM calon_guru WHERE id_region='$id_region' AND ppg_blm_diangkat='Belum'");
        while ($data_calon_guru = mysqli_fetch_array($query_calon_guru)) {
            $id_calon_guru = $data_calon_guru['id_calon_guru'];

            //Looping untuk menghitung baris
            $lulusan_ppg=$lulusan_ppg+1;
        }
    }
    // Buat Response
    $response = [
        "code" => 200,
        "message" => "Success",
        "lulusan_ppg" => (int)$lulusan_ppg
    ];
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
?>
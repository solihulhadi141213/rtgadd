<?php
    //Inisiasi Type File
    header('Content-Type: application/json');

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Validasi Sesi akses
    if (empty($SessionIdAccess)) {
        $response = [
            "code" => 201,
            "message" => "Sesi akses sudah berakhir. Silahkan <b>login</b> ulang!",
            "lulusan_ppg" =>NULL
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }

    //Validasi province_code Tidak ada
    if(empty($_POST['province_code'])){
        $response = [
            "code" => 201,
            "message" => "Parameter <b>Kode Provinsi</b> tidak boleh kosong!",
            "lulusan_ppg" =>NULL
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }

    $province_code = $_POST['province_code'];

    // Inisialisasi akumulasi Agar Tidak Error
    $lulusan_ppg = 0;

    //Melakukan looping tabel region dengan condition category=District AND province_code
    $query_region = mysqli_query($Conn, "SELECT id_region FROM region WHERE category='District' AND province_code='$province_code'");
    while ($data_region = mysqli_fetch_assoc($query_region)) {
        $id_region = $data_region['id_region'];

        // Loop posisi guru di district
        $query_position_region = mysqli_query($Conn, "SELECT asn, jumlah_guru FROM position_region WHERE id_region='$id_region'");
        while ($data_position_region = mysqli_fetch_assoc($query_position_region)) {
            $jumlah_guru    = $data_position_region['jumlah_guru'];
            if(empty($data_position_region['asn'])){
                $lulusan_ppg = $lulusan_ppg+$jumlah_guru;
            }
        }
    }

    //Buat Response
    $response = [
        "code" => 200,
        "message" => "Success",
        "lulusan_ppg" =>$lulusan_ppg
    ];
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
?>
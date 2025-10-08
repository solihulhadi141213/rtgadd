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
            "count" =>NULL
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }

    //Validasi district_code Tidak ada
    if(empty($_POST['district_code'])){
        $response = [
            "code" => 201,
            "message" => "Parameter <b>Kode Kab/Kota</b> tidak boleh kosong!",
            "count" =>NULL
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }

    $district_code = $_POST['district_code'];

    // Inisialisasi akumulasi Agar Tidak Error
    $ppg_belum_diangkat = 0;

    //Melakukan looping tabel region dengan condition category=District AND district_code
    $query_region = mysqli_query($Conn, "SELECT id_region FROM region WHERE category='District' AND district_code='$district_code'");
    while ($data_region = mysqli_fetch_assoc($query_region)) {
        $id_region = $data_region['id_region'];

        // Loop posisi guru di district
        $query_position_region = mysqli_query($Conn, "SELECT asn FROM position_region WHERE id_region='$id_region'");
        while ($data_position_region = mysqli_fetch_assoc($query_position_region)) {
            $asn    = (int)$data_position_region['asn'];
            if($asn==0){
                $ppg_belum_diangkat = $ppg_belum_diangkat+1;
            }
        }
    }

    //Buat Response
    $response = [
        "code" => 200,
        "message" => "Success",
        "count" =>$ppg_belum_diangkat
    ];
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
?>
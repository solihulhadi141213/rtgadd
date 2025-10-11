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

    // Loop calon_guru
    $query_calon_guru  = mysqli_query($Conn, "SELECT ppg_blm_diangkat FROM calon_guru WHERE district_code='$district_code'");
    while ($data_calon_guru = mysqli_fetch_assoc($query_calon_guru)) {
        $ppg_blm_diangkat= $data_calon_guru['ppg_blm_diangkat'];
        if($ppg_blm_diangkat=="Belum"){
            $ppg_belum_diangkat = $ppg_belum_diangkat+1;
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
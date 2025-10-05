<?php
    //Inisiasi File
    header('Content-Type: application/json');

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Validasi Sesi akses
    if (empty($SessionIdAccess)) {
        $response_code = 201;
        $response_msg = "Sesi akses sudah berakhir. Silahkan <b>login</b> ulang!";
        $response = [
            "code" => $response_code,
            "message" => $response_msg
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }

    //Validasi province_code
    if(empty($_POST['province_code'])){
        $response_code = 201;
        $response_msg = "Kode Provinsi Tidak Boleh Kosong!";
        $response = [
            "code" => $response_code,
            "message" => $response_msg
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }

    //Buat Variabel
    $province_code = $_POST['province_code'];

    //Looping Semua Sekolah dengan DISTINCT jenjang
    $query = mysqli_query($Conn, "SELECT DISTINCT school_level FROM school ");
    while ($data = mysqli_fetch_array($query)) {
        $school_level = $data['school_level'];

        //Looping Daftar id_region level kabupaten berdasarkan province_code
        $query_kab = mysqli_query($Conn, "SELECT id_region FROM region WHERE category='District' AND province_code='$province_code'");
        while ($data_kab = mysqli_fetch_array($query_kab)) {
            $id_region = $data_kab['id_region'];

            //Looping Daftar id_region
            
        }

    }

?>
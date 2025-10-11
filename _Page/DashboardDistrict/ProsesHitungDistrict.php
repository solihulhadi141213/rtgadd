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
    $jumlah_abk         = 0;
    $jumlah_asn         = 0;
    $jumlah_pppk2024    = 0;
    $ppg_belum_diangkat = 0;
    $kurang_guru        = 0;

    //Melakukan looping tabel region dengan condition category=District AND district_code
    $query_region = mysqli_query($Conn, "SELECT id_region FROM region WHERE category='District' AND district_code='$district_code'");
    while ($data_region = mysqli_fetch_assoc($query_region)) {
        $id_region = $data_region['id_region'];

        //Looping School
        $query_school = mysqli_query($Conn, "SELECT id_school FROM school WHERE id_region='$id_region'");
        while ($data_school = mysqli_fetch_assoc($query_school)) {
            $id_school = (int)$data_school['id_school'];

            // Loop posisi guru di district
            $query_position_region = mysqli_query($Conn, "SELECT abk, JmlASN, PPPK2024, KurangGuru FROM position_school WHERE id_school='$id_school'");
            while ($data_position_region = mysqli_fetch_assoc($query_position_region)) {
                $abk                = (int)$data_position_region['abk'];
                $asn                = (int)$data_position_region['JmlASN'];
                $pppk2024           = (int)$data_position_region['PPPK2024'];
                $kurang_guru_list   = (int)$data_position_region['KurangGuru'];

                //Menghitung asn, abk dan pppk2024
                $jumlah_abk         = $jumlah_abk+$abk;
                $jumlah_asn         = $jumlah_asn+$asn;
                $jumlah_pppk2024    = $jumlah_pppk2024+$pppk2024;

                //Menghitung Kurang Guru
                $kurang_guru        = $kurang_guru+$kurang_guru_list;
            }
        }

        //Hitung PPG Belum Diangkat
        $query_calon_guru  = mysqli_query($Conn, "SELECT ppg_blm_diangkat FROM calon_guru WHERE id_region='$id_region'");
        while ($data_calon_guru = mysqli_fetch_assoc($query_calon_guru)) {
            $ppg_blm_diangkat_list= $data_calon_guru['ppg_blm_diangkat'];
            if($ppg_blm_diangkat_list=="Belum"){
                $ppg_belum_diangkat = $ppg_belum_diangkat+1;
            }
        }
    }
    
    

    //Buat Response
    $response = [
        "code" => 200,
        "message" => "Success",
        "abk" =>$jumlah_abk,
        "asn" =>$jumlah_asn,
        "pppk2024" =>$jumlah_pppk2024,
        "kurang_guru" =>$kurang_guru,
        "ppg_belum_diangkat" =>$ppg_belum_diangkat,
    ];
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
?>
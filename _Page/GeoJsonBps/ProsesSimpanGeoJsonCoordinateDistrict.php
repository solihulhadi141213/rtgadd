<?php
    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Validasi Session Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="alert alert-danger">
                <small>Sesi akses sudah berakhir. Silahkan <b>Login</b> ulang!</small>
            </div>
        ';
        exit;
    }
    //Validasi Data Tidak Boleh Kosong
    if(empty($_POST['district_code'])){
        echo '
            <div class="alert alert-danger">
                <small>Kode Kab/Kota Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    
    if(empty($_POST['district_name'])){
        echo '
            <div class="alert alert-danger">
                <small>Nama Kab/Kota Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    //Buat Variabel
    $district_name = $_POST['district_name'] ?? [];
    $district_code = $_POST['district_code'] ?? [];
    $coordinates = $_POST['coordinates'] ?? [];

    // Loop data
    $jumlah_data = count($district_code);

    //Jika proses berhasil
    $count_success = 0;
    foreach ($district_code as $index => $code) {
        //Buka coordinates_list
        $coordinates_list = $coordinates[$index] ?? '';

        //Validasi Data Duplikat
        $id_geo_region = GetDetailData($Conn, 'geo_region','district_code', $code,'id_geo_region');
        $coordinates_lama = GetDetailData($Conn, 'geo_region','district_code', $code,'coordinates');

        //Jika Sudah Ada Maka Update
        $QryUpdate = $Conn->prepare("UPDATE geo_region SET coordinates=? WHERE id_geo_region=?");
        $QryUpdate->bind_param("si", $coordinates_list, $id_geo_region);
        if($QryUpdate->execute()){
            $count_success = $count_success+1;
        }else{
            $count_success = $count_success+0;
        }
    }
    if($count_success==$jumlah_data){
        echo '
            <input type="hidden" id="id_geo_region_put" value="'.$id_geo_region.'">
            <div class="alert alert-success">
                <small>Semua Data Provinsi <b id="NotifikasiSimpanGeoJsonCoordinateDistrictBerhasil">Berhasil</b> Disimpan</small>
            </div>
        ';
    }else{
        echo '
            <div class="alert alert-danger">
                <small>Tidak semua data berhasil disimpan <br> Total data : '.$jumlah_data.'<br>Berhasil Disimpan : '.$count_success.'</small>
            </div>
        ';
    }
?>
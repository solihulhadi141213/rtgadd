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
    if(empty($_POST['province_code'])){
        echo '
            <div class="alert alert-danger">
                <small>Tidak ada Kode Provinsi Yang Di Kirim</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['province_name'])){
        echo '
            <div class="alert alert-danger">
                <small>Tidak ada nama provinsi yang dikirim</small>
            </div>
        ';
        exit;
    }
    $province_codes = $_POST['province_code'] ?? [];
    $province_names = $_POST['province_name'] ?? [];

    // Loop data
    $jumlah_data = count($province_codes);

    //Jika proses berhasil
    $count_success = 0;
    foreach ($province_codes as $index => $code) {
        $name = $province_names[$index] ?? '';

        //Validasi Data Duplikat
        $id_geo_region = GetDetailData($Conn, 'geo_region','province_code', $code, 'id_geo_region');

        //Inisiasi Variabel $level_region
        $level_region = "Province";

        //Jika Data Belum ADa Lakukan Insert
        if(empty($id_geo_region)){
            $EntryGeoRegion = "INSERT INTO geo_region (
                level_region,
                province_code,
                province_name
            ) VALUES (
                '$level_region',
                '$code',
                '$name'
            )";
            
            $InputGeoRegion = mysqli_query($Conn, $EntryGeoRegion);
            if($InputGeoRegion) {
                $count_success = $count_success+1;
            }else{
                $count_success = $count_success+0;
            }
        }else{
            //Jika Sudah Ada Maka Update
            $QryUpdate = $Conn->prepare("UPDATE geo_region SET province_code=?, province_name=? WHERE id_geo_region=?");
            $QryUpdate->bind_param("ssi", $code, $name, $id_geo_region);
            if($QryUpdate->execute()){
                $count_success = $count_success+1;
            }else{
                $count_success = $count_success+0;
            }
        }
    }
    if($count_success==$jumlah_data){
        echo '
            <div class="alert alert-success">
                <small>Semua Data Provinsi <b id="NotifikasiTambahBerhasil">Berhasil</b> Disimpan</small>
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
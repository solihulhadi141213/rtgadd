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
    if(empty($_POST['id_geo_region'])){
        echo '
            <div class="alert alert-danger">
                <small>ID Provinsi Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    
    if(empty($_POST['district_code'])){
        echo '
            <div class="alert alert-danger">
                <small>Tidak ada Kode Kab/KKota Yang Di Kirim</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['district_name'])){
        echo '
            <div class="alert alert-danger">
                <small>Tidak ada nama Kab/KKota yang dikirim</small>
            </div>
        ';
        exit;
    }
    //Buat Variabel
    $id_geo_region = $_POST['id_geo_region'];
    $district_code = $_POST['district_code'] ?? [];
    $district_name = $_POST['district_name'] ?? [];

    //Buka kode dan nama provinsi
    $province_code = GetDetailData($Conn, 'geo_region','id_geo_region', $id_geo_region,'province_code');
    $province_name = GetDetailData($Conn, 'geo_region','id_geo_region', $id_geo_region,'province_name');

    // Loop data
    $jumlah_data = count($district_code);

    //Jika proses berhasil
    $count_success = 0;
    foreach ($district_code as $index => $code) {
        $name = $district_name[$index] ?? '';

        //Validasi Data Duplikat
        $id_kabupaten = GetDetailData($Conn, 'geo_region','district_code', $code,'id_geo_region');

        //Inisiasi Variabel $level_region
        $level_region = "District";

        //Jika Data Belum ADa Lakukan Insert
        if(empty($id_kabupaten)){
            $EntryGeoRegion = "INSERT INTO geo_region (
                level_region,
                province_code,
                province_name,
                district_code,
                district_name
            ) VALUES (
                '$level_region',
                '$province_code',
                '$province_name',
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
            $QryUpdate = $Conn->prepare("UPDATE geo_region SET district_code=?, district_name=? WHERE id_geo_region=?");
            $QryUpdate->bind_param("ssi", $code, $name, $id_kabupaten);
            if($QryUpdate->execute()){
                $count_success = $count_success+1;
            }else{
                $count_success = $count_success+0;
            }
        }
    }
    if($count_success==$jumlah_data){
        echo '
            <input type="hidden" id="id_geo_region_put" value="'.$id_geo_region.'">
            <div class="alert alert-success">
                <small>Semua Data Provinsi <b id="NotifikasiGetKabKotBerhasil">Berhasil</b> Disimpan</small>
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
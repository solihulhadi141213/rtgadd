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
    if(empty($_POST['position_code'])){
        echo '
            <div class="alert alert-danger">
                <small>Kode Jabatan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['position_name'])){
        echo '
            <div class="alert alert-danger">
                <small>Nama Jabatan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat Variabel Dan Sanitasi
    $position_code  = validateAndSanitizeInput($_POST['position_code']);
    $position_name  = validateAndSanitizeInput($_POST['position_name']);

    //validasi duplikat data position_code
    $id_position=GetDetailData($Conn, 'position','position_code', $position_code, 'id_position');
    if(!empty($id_position)){
        echo '
            <div class="alert alert-danger">
                <small>Kode Jabatan Tersebut Sudah Ada</small>
            </div>
        ';
        exit;
    }

    //Simpan Data
    $EntryRegion = "INSERT INTO position (
        position_code,
        position_name
    ) VALUES (
        '$position_code',
        '$position_name'
    )";
    
    $InputRegion = mysqli_query($Conn, $EntryRegion);
    if($InputRegion) {
        echo '
            <div class="alert alert-success">
                <small>Input data ke database <b id="NotifikasiTambahBerhasil">Berhasil</b></small>
            </div>
        ';
    }else{
        echo '
            <div class="alert alert-danger">
                <small>Terjadi Kesalahan Pada Saat Insert Data Ke Database!</small>
            </div>
        ';
    }
?>
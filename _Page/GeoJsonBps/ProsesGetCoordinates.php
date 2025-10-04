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

    if(empty($_POST['DataCoordinal'])){
        echo '
            <div class="alert alert-danger">
                <small>Tidak Data Koordinat Tidak Boleh Kosong</small>
            </div>
        ';
        exit;
    }
    $id_geo_region = $_POST['id_geo_region'];
    $DataCoordinal = $_POST['DataCoordinal'];

    //Simpan Data Ke Database
    $QryUpdate = $Conn->prepare("UPDATE geo_region SET coordinates=? WHERE id_geo_region=?");
    $QryUpdate->bind_param("si", $DataCoordinal, $id_geo_region);
    if($QryUpdate->execute()){
        echo '
            <div class="alert alert-success">
                <small>Simpan koordinat ke database <b id="NotifikasiGetCoordinatesBerhasil">Berhasil</b></small>
            </div>
        ';
    }else{
        echo '
            <div class="alert alert-danger">
                <small>Terjadi kesalahan pada saat menyimpan data ke database!</small>
            </div>
        ';
    }
?>
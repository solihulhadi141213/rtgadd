<?php
    // Koneksi & dependensi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    date_default_timezone_set('Asia/Jakarta');
    $now = date('Y-m-d H:i:s');

    // --- Validasi sesi ---
    if (empty($SessionIdAccess)) {
        echo '<div class="alert alert-danger"><small>Sesi akses sudah berakhir! Silahkan login ulang!</small></div>';
        exit;
    }

    // --- Validasi input wajib ---
    if(empty($_POST['id_access'])){
        echo '<div class="alert alert-danger"><small>ID Client Tidak Boleh Kosong</small></div>';
        exit;
    }
    if(empty($_POST['level'])){
        echo '<div class="alert alert-danger"><small>Level Akses Client Tidak Boleh Kosong</small></div>';
        exit;
    }

    // --- Sanitasi input ---
    $id_access  = validateAndSanitizeInput($_POST['id_access']);
    $level      = validateAndSanitizeInput($_POST['level']);
    $keterangan = !empty($_POST['keterangan']) ? validateAndSanitizeInput($_POST['keterangan']) : "";

    //Validasi Province dan District Berdasarkan Level
    $id_region = null;
    if($level=="District"){
        if(empty($_POST['province'])){
            echo '<div class="alert alert-danger"><small>Informasi Provinsi Tidak Boleh Kosong</small></div>';
            exit;
        }
        if(empty($_POST['district'])){
            echo '<div class="alert alert-danger"><small>Informasi Kab/Kota Tidak Boleh Kosong</small></div>';
            exit;
        }
        $id_region=$_POST['district'];
    } elseif($level=="Province"){
        if(empty($_POST['province'])){
            echo '<div class="alert alert-danger"><small>Informasi Provinsi Tidak Boleh Kosong</small></div>';
            exit;
        }
        $id_region=$_POST['province'];
    } elseif($level=="National"){
        $id_region=null;
    }

    //Update Data Akses
    $QryUpdate = $Conn->prepare("UPDATE access_client SET id_region=?, level=?, keterangan=? WHERE id_access=?");
    $QryUpdate->bind_param("issi", $id_region, $level, $keterangan, $id_access);
    if($QryUpdate->execute()){
        echo '
            <div class="alert alert-success">
                <small>Update data ke database <b id="NotifikasiEditLevelBerhasil">Berhasil</b></small>
            </div>
        ';
    }else{
        echo '
            <div class="alert alert-danger">
                <small>Terjadi Kesalahan Pada Saat Update Data Ke Database!</small>
            </div>
        ';
    }

?>
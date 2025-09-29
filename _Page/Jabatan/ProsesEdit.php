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
    if(empty($_POST['id_position'])){
        echo '
            <div class="alert alert-danger">
                <small>ID Jabatan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
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
    $id_position    = validateAndSanitizeInput($_POST['id_position']);
    $position_code  = validateAndSanitizeInput($_POST['position_code']);
    $position_name  = validateAndSanitizeInput($_POST['position_name']);

    //Buka Data Lama
    $position_code_old=GetDetailData($Conn, 'position','id_position', $id_position, 'position_code');

    //Jika Code Di ketika Baru
    if($position_code!==$position_code_old){
        $validasi_duplikat=GetDetailData($Conn, 'position','position_code', $position_code, 'id_position');
        if(!empty($validasi_duplikat)){
            echo '
                <div class="alert alert-danger">
                    <small>Kode Jabatan Tersebut Sudah Terdaftar</small>
                </div>
            ';
            exit;
        }
    }

    //Update Data
    $QryUpdate = $Conn->prepare("UPDATE position SET position_code=?, position_name=? WHERE id_position=?");
    $QryUpdate->bind_param("ssi", $position_code, $position_name, $id_position);
    if($QryUpdate->execute()){
        echo '
            <div class="alert alert-success">
                <small>Update data ke database <b id="NotifikasiEditBerhasil">Berhasil</b></small>
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
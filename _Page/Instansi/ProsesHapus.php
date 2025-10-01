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

    //tangkap id_organization
    if(empty($_POST['id_organization'])){
        echo '
            <div class="alert alert-danger">
                <small>ID Instansi Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    $id_organization=$_POST['id_organization'];

    //Hapus Data Sekolah Berdasarkan ID
    $HapusData = mysqli_query($Conn, "DELETE FROM organization WHERE id_organization='$id_organization'") or die(mysqli_error($Conn));
    if ($HapusData) {
        echo '
            <div class="alert alert-success">
                <small>Hapus data sekolah <b id="NotifikasisHapusBerhasil">Berhasil</b></small>
            </div>
        ';
    }else{
        echo '
            <div class="alert alert-danger">
                <small>Terjadi kesalahan pada saat menghapus data!</small>
            </div>
        ';
    }

?>
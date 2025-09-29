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

    //tangkap id_school
    if(empty($_POST['id_school'])){
        echo '
            <div class="alert alert-danger">
                <small>ID Sekolah Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    $id_school=$_POST['id_school'];

    //Hapus Data Sekolah Berdasarkan ID
    $HapusData = mysqli_query($Conn, "DELETE FROM school WHERE id_school='$id_school'") or die(mysqli_error($Conn));
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
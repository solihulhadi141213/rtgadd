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

    //tangkap id_position
    if(empty($_POST['id_position'])){
        echo '
            <div class="alert alert-danger">
                <small>ID Jabatan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    $id_position=$_POST['id_position'];

    $HapusData = mysqli_query($Conn, "DELETE FROM position WHERE id_position='$id_position'") or die(mysqli_error($Conn));
    if ($HapusData) {
        echo '
            <div class="alert alert-success">
                <small>Hapus data wilayah <b id="NotifikasisHapusBerhasil">Berhasil</b></small>
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
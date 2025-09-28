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

    //tangkap id_region
    if(empty($_POST['id_region'])){
        echo '
            <div class="alert alert-danger">
                <small>ID wilayah harus diisi terlebih dulu!</small>
            </div>
        ';
        exit;
    }

    $id_region=$_POST['id_region'];

    //Buka Data Lama
    $category       = GetDetailData($Conn, 'region','id_region', $id_region, 'category');
    $province_code  = GetDetailData($Conn, 'region','id_region', $id_region, 'province_code');
    $district_code  = GetDetailData($Conn, 'region','id_region', $id_region, 'district_code');

    //Penanganan data untuk categori=='Province'
    if($category=="Province"){
        $HapusData = mysqli_query($Conn, "DELETE FROM region WHERE province_code='$province_code'") or die(mysqli_error($Conn));
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

    }else{
        //Penanganan data untuk categori=='District'
        $HapusData = mysqli_query($Conn, "DELETE FROM region WHERE id_region='$id_region'") or die(mysqli_error($Conn));
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
    }

?>
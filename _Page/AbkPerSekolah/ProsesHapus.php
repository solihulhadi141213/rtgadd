<?php
    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Time Zone
    date_default_timezone_set('Asia/Jakarta');

    //Time Now Tmp
    $now=date('Y-m-d H:i:s');

    //Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        echo '
            <div class="alert alert-danger">
                <small>
                    Sesi akses sudah berakhir. Silahkan <b>login</b> ulang!
                </small>
            </div>
        ';
        exit;
    }
    //Tangkap id_position_school
    if(empty($_POST['id_position_school'])){
         echo '
            <div class="alert alert-danger">
                <small>
                    ID Jabatan Per Sekolah Tidak Boleh Kosong!
                </small>
            </div>
        ';
        exit;
    }

    //Buat variabel
    $id_position_school=validateAndSanitizeInput($_POST['id_position_school']);

    //Proses hapus data
    $HapusData = mysqli_query($Conn, "DELETE FROM position_school WHERE id_position_school='$id_position_school'") or die(mysqli_error($Conn));
    if ($HapusData) {

        //Apabila Proses Hapus Berhasil
        echo '<span class="text-success">Proses Hapus Data <b id="NotifikasisHapusBerhasil">Berhasil</b></span>';
    }else{

        //Jika menghapus gagal
        echo '
            <div class="alert alert-danger">
                <small>
                    Terjadi kesalahan pada saat menghapus data!
                </small>
            </div>
        ';
    }
?>
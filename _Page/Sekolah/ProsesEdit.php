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

    //Validasi Form Wajib Diisi
    if(empty($_POST['id_school'])){
        echo '
            <div class="alert alert-danger">
                <small>ID Sekolah Tidak Boleh Kosong. Silahkan pilih terlebih dulu!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['province_code'])){
        echo '
            <div class="alert alert-danger">
                <small>Provinsi Tidak Boleh Kosong. Silahkan pilih terlebih dulu!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['district_code'])){
        echo '
            <div class="alert alert-danger">
                <small>Informasi Kab/Kota Tidak Boleh Kosong. Silahkan pilih terlebih dulu!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['npsn'])){
        echo '
            <div class="alert alert-danger">
                <small>Kode Sekolah (NPSN) Tidak Boleh Kosong. Silahkan Isi Terlebih Dulu!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['school_name'])){
        echo '
            <div class="alert alert-danger">
                <small>Nama Sekolah Tidak Boleh Kosong. Silahkan Isi Terlebih Dulu!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['school_level'])){
        echo '
            <div class="alert alert-danger">
                <small>Jenjang Pendidikan Tidak Boleh Kosong. Silahkan Isi Terlebih Dulu!</small>
            </div>
        ';
        exit;
    }

    //Buat Variabel Dan Sanitasi
    $id_school      = validateAndSanitizeInput($_POST['id_school']);
    $province_code  = validateAndSanitizeInput($_POST['province_code']);
    $district_code  = validateAndSanitizeInput($_POST['district_code']);
    $npsn           = validateAndSanitizeInput($_POST['npsn']);
    $school_name    = validateAndSanitizeInput($_POST['school_name']);
    $school_level   = validateAndSanitizeInput($_POST['school_level']);

    //Buka Data Lama
    $Qry = $Conn->prepare("SELECT * FROM school WHERE id_school = ?");
    $Qry->bind_param("i", $id_school);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
            </div>
        ';
        exit;
    }
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    //Buat Variabel
    $npsn_lama  = $Data['npsn'];

    //Validasi npsn tidak boleh duplikat
    if($npsn_lama!==$npsn){
        $validasi_duplikat= GetDetailData($Conn, 'school','npsn', $npsn, 'id_school');
        if(!empty($validasi_duplikat)){
            echo '
                <div class="alert alert-danger">
                    <small>Kode Sekolah (NPSN) Yang Anda Masukan Sudah Terdaftar!</small>
                </div>
            ';
            exit;
        }
    }

    //Buka id_region
    $id_region = GetDetailData($Conn, 'region','district_code', $district_code, 'id_region');

    //Update Data
    $QryUpdate = $Conn->prepare("UPDATE school SET id_region=?, npsn=?, school_name=?, school_level=? WHERE id_school=?");
    $QryUpdate->bind_param("isssi", $id_region, $npsn, $school_name, $school_level, $id_school);
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

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

    //Buat Variabel Dan Sanitasi
    $province_code  = validateAndSanitizeInput($_POST['province_code']);
    $district_code  = validateAndSanitizeInput($_POST['district_code']);
    $npsn           = validateAndSanitizeInput($_POST['npsn']);
    $school_name    = validateAndSanitizeInput($_POST['school_name']);

    //Validasi npsn tidak boleh duplikat
    $id_school= GetDetailData($Conn, 'school','npsn', $npsn, 'id_school');
    if(!empty($id_school)){
         echo '
            <div class="alert alert-danger">
                <small>Kode Sekolah (NPSN) Yang Anda Masukan Sudah Terdaftar!</small>
            </div>
        ';
        exit;
    }

    //Buka id_region
    $id_region = GetDetailData($Conn, 'region','district_code', $district_code, 'id_region');

    //Prepared Statement Insert Data
    $stmt = $Conn->prepare("INSERT INTO school (id_region, npsn, school_name) VALUES (?, ?, ?)");
    if($stmt){
        $stmt->bind_param("iss", $id_region, $npsn, $school_name);
        if($stmt->execute()){
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
        $stmt->close();
    }else{
        echo '
            <div class="alert alert-danger">
                <small>Terjadi Kesalahan Pada Persiapan Query!</small>
            </div>
        ';
    }
?>

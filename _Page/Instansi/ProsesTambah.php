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
    if(empty($_POST['organization_code'])){
        echo '
            <div class="alert alert-danger">
                <small>Kode Instansi Tidak Boleh Kosong. Silahkan Isi Terlebih Dulu!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['organization_name'])){
        echo '
            <div class="alert alert-danger">
                <small>Nama Instansi Tidak Boleh Kosong. Silahkan Isi Terlebih Dulu!</small>
            </div>
        ';
        exit;
    }

    //Buat Variabel Dan Sanitasi
    $province_code      = validateAndSanitizeInput($_POST['province_code']);
    $organization_code  = validateAndSanitizeInput($_POST['organization_code']);
    $organization_name  = validateAndSanitizeInput($_POST['organization_name']);
    if(empty($_POST['district_code'])){
        $id_region          = GetDetailData($Conn, 'region','province_code', $province_code, 'id_region');
        $district_code      = "";
        $organization_level ="Province";
    }else{
        $district_code      = validateAndSanitizeInput($_POST['district_code']);
        $id_region          = GetDetailData($Conn, 'region','district_code', $district_code, 'id_region');
        $organization_level ="District";
    }

    //Validasi organization_code tidak boleh duplikat
    $id_organization= GetDetailData($Conn, 'organization','organization_code', $organization_code, 'id_organization');
    if(!empty($id_organization)){
         echo '
            <div class="alert alert-danger">
                <small>Kode Instansi Yang Anda Masukan Sudah Terdaftar!</small>
            </div>
        ';
        exit;
    }

    //Prepared Statement Insert Data
    $stmt = $Conn->prepare("INSERT INTO organization (id_region, organization_level, organization_code, organization_name) VALUES (?, ?, ?, ?)");
    if($stmt){
        $stmt->bind_param("isss", $id_region, $organization_level, $organization_code, $organization_name);
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

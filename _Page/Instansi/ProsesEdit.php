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
    if(empty($_POST['id_organization'])){
        echo '
            <div class="alert alert-danger">
                <small>ID Instansi Tidak Boleh Kosong. Silahkan pilih terlebih dulu!</small>
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
    $id_organization    = validateAndSanitizeInput($_POST['id_organization']);
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

    //Buka Data Lama
    $organization_code_old = GetDetailData($Conn, 'organization','id_organization', $id_organization, 'organization_code');

    //Validasi organization_code tidak boleh duplikat
    if($organization_code_old!==$organization_code){
        $id_organization= GetDetailData($Conn, 'organization','organization_code', $organization_code, 'id_organization');
        if(!empty($id_organization)){
            echo '
                <div class="alert alert-danger">
                    <small>Kode Instansi Yang Anda Masukan Sudah Terdaftar!</small>
                </div>
            ';
            exit;
        }
    }

    //Prepared Statement Insert Data
    $QryUpdate = $Conn->prepare("UPDATE organization SET id_region=?, organization_level=?, organization_code=?, organization_name=? WHERE id_organization=?");
    $QryUpdate->bind_param("isssi", $id_region, $organization_level, $organization_code, $organization_name, $id_organization);
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

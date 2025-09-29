<?php
    //Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Inisiasi Variabel id_region
    $id_region="";

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

    //Tangkap id_region
    if(empty($_POST['id_region'])){
         echo '
            <div class="alert alert-danger">
                <small>
                    ID Wilayah Tidak Boleh Kosong!
                </small>
            </div>
        ';
        exit;
    }

    //Buat variabel
    $id_region=validateAndSanitizeInput($_POST['id_region']);

    //Fungsi untuk mengganti value kosong menjadi strip
    function showOrDash($value){
        return (isset($value) && $value !== "" && $value !== null) ? $value : "-";
    }

    //Buka Data Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM region WHERE id_region = ?");
    $Qry->bind_param("i", $id_region);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
            </div>
        ';
    }else{
        $Result = $Qry->get_result();
        $Data = $Result->fetch_assoc();
        $Qry->close();

        //Buat Variabel dengan fallback "-"
        $category       = showOrDash($Data['category']);
        $province_code  = showOrDash($Data['province_code']);
        $province_code_dapodik  = showOrDash($Data['province_code_dapodik']);
        $province_name  = showOrDash($Data['province_name']);
        $district_code  = showOrDash($Data['district_code']);
        $district_code_dapodik  = showOrDash($Data['district_code_dapodik']);
        $district_name  = showOrDash($Data['district_name']);
        $code_map       = showOrDash($Data['code_map']);

        //Tampilkan Data
        echo '
            <div class="row mb-2">
                <div class="col-4"><small>Kategori</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$category.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kode Provinsi (BPS)</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$province_code.'</small>
                </div>
            </div>
             <div class="row mb-2">
                <div class="col-4"><small>Kode Provinsi (DAPODIK)</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$province_code_dapodik.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Nama Provinsi</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$province_name.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kode Kab/Kota (BPS)</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$district_code.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kode Kab/Kota (DAPODIK)</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$district_code_dapodik.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Nama Kab/Kota</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$district_name.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kode Map</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$code_map.'</small>
                </div>
            </div>
        ';
    }
?>

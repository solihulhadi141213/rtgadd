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

    //Tangkap id_school
    if(empty($_POST['id_school'])){
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
    $id_school = validateAndSanitizeInput($_POST['id_school']);

    //Fungsi untuk mengganti value kosong menjadi strip
    function showOrDash($value){
        return (isset($value) && $value !== "" && $value !== null) ? $value : "-";
    }

    //Buka Data Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM school WHERE id_school = ?");
    $Qry->bind_param("i", $id_school);
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
        $id_region      = showOrDash($Data['id_region']);
        $npsn           = showOrDash($Data['npsn']);
        $school_name    = showOrDash($Data['school_name']);

        //Buka Region
        $province_code      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_code');
        $province_name      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_name');
        $district_code      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_code');
        $district_name      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_name');

        //Tampilkan Data
        echo '
            <div class="row mb-2">
                <div class="col-4"><small>Kode Provinsi</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$province_code.'</small>
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
                <div class="col-4"><small>Kode Kab/Kota</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$district_code.'</small>
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
                <div class="col-4"><small>Kode Sekolah (NPSN)</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$npsn.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Nama Sekolah</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$school_name.'</small>
                </div>
            </div>
        ';
    }
?>

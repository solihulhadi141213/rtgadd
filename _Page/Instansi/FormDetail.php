<?php
    //Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Inisiasi Variabel id_organization
    $id_organization="";

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

    //Tangkap id_organization
    if(empty($_POST['id_organization'])){
         echo '
            <div class="alert alert-danger">
                <small>
                    ID Instansi Tidak Boleh Kosong!
                </small>
            </div>
        ';
        exit;
    }

    //Buat variabel
    $id_organization = validateAndSanitizeInput($_POST['id_organization']);

    //Fungsi untuk mengganti value kosong menjadi strip
    function showOrDash($value){
        return (isset($value) && $value !== "" && $value !== null) ? $value : "-";
    }

    //Buka Data Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM organization WHERE id_organization = ?");
    $Qry->bind_param("i", $id_organization);
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
        $id_region              = showOrDash($Data['id_region']);
        $organization_level     = showOrDash($Data['organization_level']);
        $organization_code      = showOrDash($Data['organization_code']);
        $organization_name      = showOrDash($Data['organization_name']);

        //Buka Region
        $category           = GetDetailData($Conn, 'region', 'id_region', $id_region, 'category');
        $province_code      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_code');
        $province_name      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_name');
        if($category=="District"){
            $district_code      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_code');
            $district_name      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_name');
        }else{
            $district_code      = "-";
            $district_name      = "-";
        }
        
        //Label Level
        if($organization_level=="District"){
            $label_organization_level='<span class="badge badge-success">Kab/Kota</span>';
        }else{
            $label_organization_level='<span class="badge badge-primary">Provinsi</span>';
        }

        //Tampilkan Data
        echo '
            <div class="row mb-2">
                <div class="col-4"><small>Level Instansi</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$label_organization_level.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kode Instansi</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$organization_code.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Nama Instansi</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$organization_name.'</small>
                </div>
            </div>
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
        ';
    }
?>

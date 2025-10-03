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

    //Tangkap id_position_organization
    if(empty($_POST['id_position_organization'])){
         echo '
            <div class="alert alert-danger">
                <small>
                    ID Jabatan Per Instansi Tidak Boleh Kosong!
                </small>
            </div>
        ';
        exit;
    }

    //Buat variabel
    $id_position_organization = validateAndSanitizeInput($_POST['id_position_organization']);

    //Fungsi untuk mengganti value kosong menjadi strip
    function showOrDash($value){
        return (isset($value) && $value !== "" && $value !== null) ? $value : "-";
    }

    //Buka Data Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM position_organization WHERE id_position_organization = ?");
    $Qry->bind_param("i", $id_position_organization);
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
        $id_region                      = $Data['id_region'];
        $id_position                    = $Data['id_position'];
        $id_organization                = $Data['id_organization'];
        $category                       = $Data['category'];
        $formasi_ppg                    = $Data['formasi_ppg'];

        //Buka Nama Provinsi
        $province_code              = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_code');
        $province_code_dapodik      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_code_dapodik');
        $province_name              = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_name');

        //Buka Nama Kab/Kota
        $district_code              = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_code');
        $district_code_dapodik      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_code_dapodik');
        $district_name              = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_name');

        //Buka position
        $position_code      = GetDetailData($Conn, 'position', 'id_position', $id_position, 'position_code');
        $position_name      = GetDetailData($Conn, 'position', 'id_position', $id_position, 'position_name');

        //Buka Organization
        $organization_code  = GetDetailData($Conn, 'organization', 'id_organization', $id_organization, 'organization_code');
        $organization_name  = GetDetailData($Conn, 'organization', 'id_organization', $id_organization, 'organization_name');

        //Label Level
        if($category=="District"){
            $LabelCategory='<span class="badge badge-success">Kab/Kota</span>';
        }else{
            $LabelCategory='<span class="badge badge-primary">Provinsi</span>';
        }

        //Tampilkan Data
        echo '
            <input type="hidden" name="id_position_organization" value="'.$id_position_organization.'">
            <div class="row mb-2">
                <div class="col-12"><small><b>A. Informasi Provinsi</b></small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Kode Prov (BPS)</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6">
                    <small class="text text-grayish">'.$province_code.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Kode Prov (DAPODIK)</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6">
                    <small class="text text-grayish">'.$province_code_dapodik.'</small>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-5"><small>Nama Provinsi</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6">
                    <small class="text text-grayish">'.$province_name.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12"><small><b>B. Informasi Kab/Kota</b></small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Kode Kab/Kota (BPS)</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6">
                    <small class="text text-grayish">'.$district_code.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Kode Kab/Kota (DAPODIK)</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6">
                    <small class="text text-grayish">'.$district_code_dapodik.'</small>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-5"><small>Nama Kab/Kota</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6">
                    <small class="text text-grayish">'.$district_name.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12"><small><b>C. Informasi Instansi</b></small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Kode Instansi</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6">
                    <small class="text text-grayish">'.$organization_code.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Nama Instansi</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6">
                    <small class="text text-grayish">'.$organization_name.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12"><small><b>D. Informasi Jabatan</b></small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Kode Jabatan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6">
                    <small class="text text-grayish">'.$position_code.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Nama Jabatan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6">
                    <small class="text text-grayish">'.$position_name.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12"><small><b>E. Formasi PPG</b></small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Level</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6">
                    <small class="text text-grayish">'.$LabelCategory.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-5">
                    <label for="formasi_ppg_edit">
                        <small>Jumlah Formasi</small>
                    </label>
                </div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6">
                    <input type="number" name="formasi_ppg" id="formasi_ppg_edit" class="form-control" value="'.$formasi_ppg.'">
                </div>
            </div>
        ';
    }
?>

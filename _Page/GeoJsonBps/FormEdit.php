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

    //Tangkap id_geo_region
    if(empty($_POST['id_geo_region'])){
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
    $id_geo_region=validateAndSanitizeInput($_POST['id_geo_region']);

    //Buka Data Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM geo_region WHERE id_geo_region = ?");
    $Qry->bind_param("i", $id_geo_region);
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

    //Form ID
    echo '<input type="hidden" name="id_geo_region" value="'.$id_geo_region.'">';

    //Buat Variabel 
    $level_region   = $Data['level_region']; 
    echo '<input type="hidden" name="level_region" value="'.$level_region.'">';
    //Tampilkan Kategori
    echo '
        <div class="row mb-3">
            <div class="col-5"><small>Level</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small class="text text-grayish">'.$level_region.'</small></div>
        </div>
    ';
    if($level_region=="District"){
        $province_code  = $Data['province_code']; 
        $province_name  = $Data['province_name']; 
        $district_code  = $Data['district_code']; 
        $district_name  = $Data['district_name']; 
        $coordinates    = $Data['coordinates']; 
        //Tampilkan Form
        echo '
            <div class="row mb-3">
                <div class="col-5"><small>Kode Provinsi</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6"><small class="text text-grayish">'.$province_code.'</small></div>
            </div>
            <div class="row mb-3">
                <div class="col-5"><small>Nama Provinsi</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6"><small class="text text-grayish">'.$province_name.'</small></div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <label for="edit_district_code">
                        <small>Kode Kab/Kota <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                    </label>
                    <input type="text" name="district_code" id="edit_district_code" class="form-control" value="'.$district_code.'" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <label for="edit_district_name">
                        <small>Nama Kab/Kota <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                    </label>
                    <input type="text" name="district_name" id="edit_district_name" class="form-control" value="'.$district_name.'" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <label for="coordinates_edit">
                        <small>Koordinat (GeoJson) <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                    </label>
                    <textarea name="coordinates" id="coordinates_edit" class="form-control">'.$coordinates.'</textarea>
                </div>
            </div>
        ';
    }else{
        $province_code  = $Data['province_code']; 
        $province_name  = $Data['province_name']; 
        $coordinates    = $Data['coordinates']; 
        //Tampilkan Form
        echo '
            <div class="row mb-3">
                <div class="col-12">
                    <label for="province_code_edit">
                        <small>Kode Provinsi <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                    </label>
                    <input type="text" name="province_code" id="province_code_edit" class="form-control" value="'.$province_code.'" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <label for="province_name_edit">
                        <small>Nama Provinsi <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                    </label>
                    <input type="text" name="province_name" id="province_name_edit" class="form-control" value="'.$province_name.'" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <label for="coordinates_edit">
                        <small>Koordinat (GeoJson) <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                    </label>
                    <textarea name="coordinates" id="coordinates_edit" class="form-control">'.$coordinates.'</textarea>
                </div>
            </div>
        ';
    }
?>

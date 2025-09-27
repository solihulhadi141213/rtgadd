<?php
    //Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Inisiasi Variabel id_position_region
    $id_position_region="";

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
    //Tangkap id_position_region
    if(empty($_POST['id_position_region'])){
         echo '
            <div class="alert alert-danger">
                <small>
                    ID Jabatan Per Wilayah Tidak Boleh Kosong!
                </small>
            </div>
        ';
        exit;
    }

    //Buat variabel
    $id_position_region=validateAndSanitizeInput($_POST['id_position_region']);

    //Buka Data Jabatan Per Wilayah
    $Qry = $Conn->prepare("SELECT * FROM position_region WHERE id_position_region = ?");
    $Qry->bind_param("s", $id_position_region);
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

        //Buat Variabel
        $province           = $Data['province'];
        $regency            = $Data['regency'];
        $department         = $Data['department'];
        $workload           = $Data['workload'];
        $officials_public   = $Data['officials_public'];
        $officials_private  = $Data['officials_private'];
        $manpower_gap       = $Data['manpower_gap'];

        

        //Tampilkan Data
        echo '
            <div class="row mb-2">
                <div class="col-4"><small>Provinsi</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$province.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kab/Kota</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$regency.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Jabatan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$department.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>AKB</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$workload.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>ASN-Sekolah Negeri</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$officials_public.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>ASN-Sekolah Swasta</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$officials_private.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kekurangan ASN</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$manpower_gap.'</small>
                </div>
            </div>
        ';
    }
?>
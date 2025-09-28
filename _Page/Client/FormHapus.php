<?php
    //Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

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

    //Validasi id_access
    if(empty($_POST['id_access'])){
        echo '
            <div class="alert alert-danger">
                <small>
                    ID Akses Tidak Boleh Kosong!
                </small>
            </div>
        ';
        exit;
    }
    $id_access=$_POST['id_access'];
    $id_access=validateAndSanitizeInput($_POST['id_access']);
    
    //Buka Data access
    $Qry = $Conn->prepare("SELECT * FROM access WHERE id_access = ?");
    $Qry->bind_param("i", $id_access);
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
        $access_name        =$Data['access_name'];
        $access_email       =$Data['access_email'];
        if(empty($Data['access_contact'])){
            $access_contact = "-";
        }else{
            $access_contact = $Data['access_contact'];
        }

        //Buka Akses Client
        $level          = GetDetailData($Conn, 'access_client', 'id_access', $id_access, 'level');
        $id_region      = GetDetailData($Conn, 'access_client', 'id_access', $id_access, 'id_region');

        //Buka region
        if(!empty($id_region)){
            $category       = GetDetailData($Conn, 'region', 'id_region', $id_region, 'category');
            $province_name  = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_name');
            $district_name  = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_name');
            if(empty($district_name)){
                $district_name  = "-";
            }
        }else{
            $category       = "-";
            $province_name  = "-";
            $district_name  = "-";
        }

        //Routing Level Label
        $level_label='<span class="badge bg-danger">None</span>';
        if($level=="National"){
            $level_label='<span class="badge bg-primary">Nasional</span>';
        }
        if($level=="Province"){
            $level_label='<span class="badge bg-info">Provinsi</span>';
        }
        if($level=="District"){
            $level_label='<span class="badge bg-success">Kab/Kota</span>';
        }

        //Tampilkan Data
        echo '
            <input type="hidden" name="id_access" value="'.$id_access.'">
            <div class="row mb-2">
                <div class="col-4"><small>Nama</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$access_name.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Email</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$access_email.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kontak</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$access_contact.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Level</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$level_label.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Provinsi</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$province_name.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kab/Kota</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$district_name.'</small>
                </div>
            </div>
            <div class="row mb-3 mt-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger">
                        <h2><i class="bi bi-exclamation-triangle"></i> Penting!</h2>
                        <small>
                            Dengan menghapus data akses tersebut akan menyebabkan yang bersangkutan tidak dapat masuk / mengakses aplikasi.<br>
                            <b>Apakah anda yakin akan menghapus data tersebut?</b>
                        </small>
                    </div>
                </div>
            </div>
        ';
    }
?>
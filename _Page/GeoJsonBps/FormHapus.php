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

    //tangkap id_geo_region
    if(empty($_POST['id_geo_region'])){
        echo '
            <div class="alert alert-danger">
                <small>ID wilayah harus diisi terlebih dulu!</small>
            </div>
        ';
        exit;
    }

    $id_geo_region=$_POST['id_geo_region'];

    //Fungsi untuk mengganti value kosong menjadi strip
    function showOrDash($value){
        return (isset($value) && $value !== "" && $value !== null) ? $value : "-";
    }

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
    }else{
        $Result = $Qry->get_result();
        $Data = $Result->fetch_assoc();
        $Qry->close();

        //Buat Variabel dengan fallback "-"
        $level_region   = showOrDash($Data['level_region']);
        $province_code  = showOrDash($Data['province_code']);
        $province_name  = showOrDash($Data['province_name']);
        $district_code  = showOrDash($Data['district_code']);
        $district_name  = showOrDash($Data['district_name']);

        //Tampilkan Data
        echo '
            <input type="hidden" name="id_geo_region" value="'.$id_geo_region.'">
            <div class="row mb-2">
                <div class="col-4"><small>Kategori/Level</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$level_region.'</small>
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
            <div class="row mb-2">
                <div class="col-12">
                    <div class="alert alert-danger">
                        <small>Apakah anda yakin akan menghapus data wilayah tersebut?</small>
                    </div>
                </div>
            </div>
        ';
    }

?>
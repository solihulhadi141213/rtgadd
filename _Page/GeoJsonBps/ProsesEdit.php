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
                <small>ID wilayah Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //tangkap level_region
    if(empty($_POST['level_region'])){
        echo '
            <div class="alert alert-danger">
                <small>Kategori wilayah harus diisi terlebih dulu!</small>
            </div>
        ';
        exit;
    }

    $id_geo_region  =$_POST['id_geo_region'];
    $level_region   =$_POST['level_region'];

    //Routing Berdasarkan Level
    if($level_region=="Province"){
        if(empty($_POST['province_code'])){
            echo '
                <div class="alert alert-danger">
                    <small>Kode provinsi tidak boleh kosong!</small>
                </div>
            ';
            exit;
        }
        if(empty($_POST['province_name'])){
            echo '
                <div class="alert alert-danger">
                    <small>Kode provinsi tidak boleh kosong!</small>
                </div>
            ';
            exit;
        }
        $province_code=$_POST['province_code'];
        $province_name=$_POST['province_name'];
        if(empty($_POST['coordinates'])){
            $coordinates=NULL;
        }else{
            $coordinates=$_POST['coordinates'];
        }
        //Validas Duplikat
        $province_code_old  = GetDetailData($Conn, 'geo_region','id_geo_region', $id_geo_region, 'province_code');
        $province_name_old  = GetDetailData($Conn, 'geo_region','id_geo_region', $id_geo_region, 'province_name');
        if($province_code_old!==$province_code){
            $id_geo_region  = GetDetailData($Conn, 'geo_region','province_code', $province_code, 'id_geo_region');
            if(!empty($id_geo_region)){
                echo '
                    <div class="alert alert-danger">
                        <small>Kode provinsi tersebut sudah terdaftar!</small>
                    </div>
                ';
                exit;
            }
        }

        //Update provinsi
        $QryUpdate = $Conn->prepare("UPDATE geo_region SET province_code=?, province_name=?, coordinates=? WHERE province_code=?");
        $QryUpdate->bind_param("ssss", $province_code, $province_name, $coordinates, $province_code_old);
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

    }
    if($level_region=="District"){
        if(empty($_POST['district_code'])){
            echo '
                <div class="alert alert-danger">
                    <small>Kode Kab/Kota tidak boleh kosong!</small>
                </div>
            ';
            exit;
        }
        if(empty($_POST['district_name'])){
            echo '
                <div class="alert alert-danger">
                    <small>Kode Kab/Kota tidak boleh kosong!</small>
                </div>
            ';
            exit;
        }
        $district_code=$_POST['district_code'];
        $district_name=$_POST['district_name'];
        if(empty($_POST['coordinates'])){
            $coordinates=NULL;
        }else{
            $coordinates=$_POST['coordinates'];
        }

        //Validasi Kode District
        $district_code_old  = GetDetailData($Conn, 'geo_region','id_geo_region', $id_geo_region, 'district_code');
        if($district_code_old!==$district_code){
            $id_geo_region  = GetDetailData($Conn, 'geo_region','district_code', $district_code, 'id_geo_region');
            if(!empty($id_geo_region)){
                echo '
                    <div class="alert alert-danger">
                        <small>Kode Kab/Kota tersebut sudah terdaftar!</small>
                    </div>
                ';
                exit;
            }
        }

        //Update Kab/Kota
        $QryUpdate = $Conn->prepare("UPDATE geo_region SET district_code=?, district_name=?, coordinates=? WHERE id_geo_region=?");
        $QryUpdate->bind_param("ssss", $district_code, $district_name, $coordinates, $id_geo_region);
        if($QryUpdate->execute()){
            echo '
                <div class="alert alert-success">
                    <small>Update data ke database <b id="NotifikasiEditBerhasil">Berhasil</b></small>
                </div>
                <input type="hidden" id="id_geo_region_put" value="'.$id_geo_region.'">
            ';
        }else{
            echo '
                <div class="alert alert-danger">
                    <small>Terjadi Kesalahan Pada Saat Update Data Ke Database!</small>
                </div>
            ';
        }

    }
?>
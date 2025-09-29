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

    //tangkap id_region
    if(empty($_POST['id_region'])){
        echo '
            <div class="alert alert-danger">
                <small>ID wilayah harus diisi terlebih dulu!</small>
            </div>
        ';
        exit;
    }

    //tangkap kategori wilayah
    if(empty($_POST['category'])){
        echo '
            <div class="alert alert-danger">
                <small>Kategori wilayah harus diisi terlebih dulu!</small>
            </div>
        ';
        exit;
    }

    $id_region=$_POST['id_region'];
    $category=$_POST['category'];

    //Buka Data Lama
    $province_code_old          = GetDetailData($Conn, 'region','id_region', $id_region, 'province_code');
    $province_code_dapodik_old  = GetDetailData($Conn, 'region','id_region', $id_region, 'province_code_dapodik');
    $district_code_old          = GetDetailData($Conn, 'region','id_region', $id_region, 'district_code');
    $district_code_dapodik_old  = GetDetailData($Conn, 'region','id_region', $id_region, 'district_code_dapodik');

    //Penanganan data untuk categori=='Province'
    if($category=="Province"){

        //Validasi Kode Provinsi Tidak Boleh kosong
        if(empty($_POST['province_code'])){
            echo '
                <div class="alert alert-danger">
                    <small>Kode provinsi tidak boleh kosong!</small>
                </div>
            ';
            exit;
        }
        if(empty($_POST['province_code_dapodik'])){
            echo '
                <div class="alert alert-danger">
                    <small>Kode provinsi tidak boleh kosong!</small>
                </div>
            ';
            exit;
        }

        //Validasi Nama Provinsi Tidak Boleh Kosong
        if(empty($_POST['province_name'])){
            echo '
                <div class="alert alert-danger">
                    <small>Nama provinsi tidak boleh kosong!</small>
                </div>
            ';
            exit;
        }

        //Buat Variabel
        $province_code=validateAndSanitizeInput($_POST['province_code']);
        $province_code_dapodik=validateAndSanitizeInput($_POST['province_code_dapodik']);
        $province_name=validateAndSanitizeInput($_POST['province_name']);
        $district_code="";
        $district_code_dapodik="";
        $district_name="";

        //validasi duplikasi $province_code
        if($province_code==$province_code_old){
            $validasi_duplikat="";
        }else{
            $validasi_duplikat=GetDetailData($Conn, 'region','province_code', $province_code, 'id_region');
        }
        
        if(!empty($validasi_duplikat)){
            echo '
                <div class="alert alert-danger">
                    <small>Kode Provinsi (BPS) Tersebut Sudah Ada</small>
                </div>
            ';
            exit;
        }

        //validasi duplikasi $province_code_dapodik
        if($province_code_dapodik==$province_code_dapodik_old){
            $validasi_duplikat="";
        }else{
            $validasi_duplikat=GetDetailData($Conn, 'region','province_code_dapodik', $province_code_dapodik, 'id_region');
        }
        
        if(!empty($validasi_duplikat)){
            echo '
                <div class="alert alert-danger">
                    <small>Kode Provinsi (DAPODIK) Tersebut Sudah Ada</small>
                </div>
            ';
            exit;
        }

    }else{
        //Penanganan data untuk categori=='District'

        //Validasi Kode Provinsi Tidak Boleh kosong
        if(empty($_POST['province_code'])){
            echo '
                <div class="alert alert-danger">
                    <small>Kode provinsi tidak boleh kosong!</small>
                </div>
            ';
            exit;
        }

        //Validasi Kode District Tidak Boleh kosong
        if(empty($_POST['district_code'])){
            echo '
                <div class="alert alert-danger">
                    <small>Kode Kab/Kota (BPS) tidak boleh kosong!</small>
                </div>
            ';
            exit;
        }
         if(empty($_POST['district_code_dapodik'])){
            echo '
                <div class="alert alert-danger">
                    <small>Kode Kab/Kota (DAPODIK) tidak boleh kosong!</small>
                </div>
            ';
            exit;
        }

        //Validasi Nama District Tidak Boleh kosong
        if(empty($_POST['district_name'])){
            echo '
                <div class="alert alert-danger">
                    <small>Nama Kab/Kota tidak boleh kosong!</small>
                </div>
            ';
            exit;
        }

        //Buat Variabel
        $province_code          = validateAndSanitizeInput($_POST['province_code']);
        $province_name          = GetDetailData($Conn, 'region','province_code', $province_code, 'province_name');
        $district_code          = validateAndSanitizeInput($_POST['district_code']);
        $district_code_dapodik  = validateAndSanitizeInput($_POST['district_code_dapodik']);
        $district_name          = validateAndSanitizeInput($_POST['district_name']);

        //validasi jika $province_name tidak ada
        if(empty($province_name)){
             echo '
                <div class="alert alert-danger">
                    <small>Kode Provinsi Yang Dipilih Tidak Valid!</small>
                </div>
            ';
            exit;
        }

        //validasi duplikasi $district_code
        if($district_code==$district_code_old){
            $validasi_duplikat = "";
        }else{
            $validasi_duplikat = GetDetailData($Conn, 'region','district_code', $district_code, 'id_region');
        }
        
        if(!empty($validasi_duplikat)){
            echo '
                <div class="alert alert-danger">
                    <small>Kode Kab/Kota (BPS) Tersebut Sudah Ada</small>
                </div>
            ';
            exit;
        }

        //validasi duplikasi $district_code_dapodik
        if($district_code_dapodik==$district_code_dapodik_old){
            $validasi_duplikat = "";
        }else{
            $validasi_duplikat = GetDetailData($Conn, 'region','district_code_dapodik', $district_code_dapodik, 'id_region');
        }
        
        if(!empty($validasi_duplikat)){
            echo '
                <div class="alert alert-danger">
                    <small>Kode Kab/Kota (DAPODIK) Tersebut Sudah Ada</small>
                </div>
            ';
            exit;
        }
    }

    //Variabel kode map
    if(empty($_POST['code_map'])){
        $code_map="";
    }else{
        $code_map=validateAndSanitizeInput($_POST['code_map']);
    }

    if($category=="Province"){
        
        //Update provinsi
        $QryUpdate = $Conn->prepare("UPDATE region SET province_code=?, province_code_dapodik=?, province_name=? WHERE province_code=?");
        $QryUpdate->bind_param("ssss", $province_code, $province_code_dapodik, $province_name, $province_code_old);
        if($QryUpdate->execute()){
            echo '
                <div class="alert alert-success">
                    <small>Input data ke database <b id="NotifikasiEditBerhasil">Berhasil</b></small>
                </div>
            ';
        }else{
            echo '
                <div class="alert alert-danger">
                    <small>Terjadi Kesalahan Pada Saat Update Data Ke Database!</small>
                </div>
            ';
        }
    }else{
        
        //Update District
        $QryUpdate = $Conn->prepare("UPDATE region SET province_code=?, province_name=?, district_code=?, district_code_dapodik=?, district_name=?, code_map=? WHERE id_region=?");
        $QryUpdate->bind_param("ssssssi", $province_code, $province_name, $district_code, $district_code_dapodik, $district_name, $code_map, $id_region);
        if($QryUpdate->execute()){
            echo '
                <div class="alert alert-success">
                    <small>Input data ke database <b id="NotifikasiEditBerhasil">Berhasil</b></small>
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
?>
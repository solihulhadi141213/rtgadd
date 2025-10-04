<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    date_default_timezone_set("Asia/Jakarta");
    $JmlHalaman=0;
    $page=0;
    //Validasi Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="alert alert-danger">
                <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
            </div>
        ';
        exit;
    }
    //id_geo_region
    if(empty($_POST['id_geo_region'])){
        echo '
            <div class="alert alert-danger">
                <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
            </div>
        ';
        exit;
    }
    $id_geo_region=$_POST['id_geo_region'];

    //Validasi Data
    $province_code = GetDetailData($Conn, 'geo_region','id_geo_region', $id_geo_region, 'province_code');
    $province_name = GetDetailData($Conn, 'geo_region','id_geo_region', $id_geo_region, 'province_name');
    if(empty($province_code)){
         echo '
            <div class="alert alert-danger">
                <small class="text-danger">ID Wilayah Tidak Valid!</small>
            </div>
        ';
        exit;
    }
    
    $url_endpoint = "https://whatsproject.my.id/geo/v1/prov/$province_code/map";

    // CURL
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url_endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ));

    $response = curl_exec($curl);

    // Cek error
    if ($response === false) {
        echo '
            <div class="alert alert-danger">
                <small class="text-danger">cURL Error: '.curl_error($curl).'</small>
            </div>
        ';
        exit;
    }
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    if ($httpcode >= 200 && $httpcode < 300) {
        $data = json_decode($response, true);
        $coordinates = $data['provFeature'];
        $data_rapih= json_encode($coordinates, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        echo '
            <input type="hidden" name="id_geo_region" value="'.$id_geo_region.'">
            <textarea class="form-control" name="DataCoordinal" id="DataCoordinal" style="height:200px;">'.$data_rapih.'</textarea>
        ';
    }else{
        echo '
            <div class="alert alert-danger">
                <small class="text-danger">HTTP Status '.$httpcode.'<br>'.$response.'</small>
            </div>
        ';
    }
?>
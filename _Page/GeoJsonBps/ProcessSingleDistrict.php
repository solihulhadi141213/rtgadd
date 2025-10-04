<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    date_default_timezone_set("Asia/Jakarta");

    //Validasi Sesi Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="row mb-3">
                <div class="col-12">
                    <div class="alert alert-danger">
                        <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                    </div>
                </div>
            </div>
        ';
        exit;
    }

    //Tangkap data
    if(empty($_POST['district_code'])){
        echo '
            <div class="row mb-3">
                <div class="col-12">
                    <div class="alert alert-danger">
                        <small class="text-danger">Tidak Ada Data Kab/Kota Yang Dikirim</small>
                    </div>
                </div>
            </div>
        ';
        exit;
    }

    $district_code = $_POST['district_code'];
    $current_index = $_POST['current_index'] ?? 0;
    $total = $_POST['total'] ?? 1;

    // Dapatkan nama district dari database
    $district_name = GetDetailData($Conn, 'geo_region', 'district_code', $district_code, 'district_name');

    // Proses CURL untuk satu district
    $url_endpoint = "https://whatsproject.my.id/geo/v1/city/$district_code/map";

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url_endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ));

    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($httpcode >= 200 && $httpcode < 300) {
        $data = json_decode($response, true);
        
        if(isset($data['cityFeature']['features'][0]['geometry']['coordinates'])) {
            $coordinates = $data['cityFeature'];
            $data_rapih = json_encode($coordinates, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            
            echo '
                <div class="row mb-3 border-bottom pb-2">
                    <div class="col-2 mb-2">
                        <label for="district_code_list'.($current_index + 1).'">
                            <small>Kode Kab/Kota</small>
                        </label>
                        <input type="text" class="form-control form-control-sm" 
                            name="district_code[]" 
                            id="district_code_list'.($current_index + 1).'" 
                            value="'.$district_code.'" readonly>
                    </div>
                    <div class="col-3 mb-2">
                        <label for="district_name_list'.($current_index + 1).'">
                            <small>Nama Kab/Kota</small>
                        </label>
                        <input type="text" class="form-control form-control-sm" 
                            name="district_name[]" 
                            id="district_name_list'.($current_index + 1).'" 
                            value="'.$district_name.'" readonly>
                    </div>
                    <div class="col-7 mb-2">
                        <label for="coordinates_list'.($current_index + 1).'">
                            <small>Koordinat GeoJSON</small>
                        </label>
                        <textarea class="form-control form-control-sm" 
                                name="coordinates[]" 
                                id="coordinates_list'.($current_index + 1).'" 
                                rows="3" readonly>'.htmlspecialchars($data_rapih).'</textarea>
                        <small class="text-success">
                            <i class="bi bi-check-circle"></i> Berhasil diambil
                        </small>
                    </div>
                </div>
            ';
        } else {
            echo '
                <div class="row mb-3 border-bottom pb-2">
                    <div class="col-2 mb-2">
                        <input type="text" class="form-control form-control-sm" 
                            name="district_code[]" 
                            value="'.$district_code.'" readonly>
                    </div>
                    <div class="col-3 mb-2">
                        <input type="text" class="form-control form-control-sm" 
                            name="district_name[]" 
                            value="'.$district_name.'" readonly>
                    </div>
                    <div class="col-7 mb-2">
                        <small class="text-warning">
                            <i class="bi bi-exclamation-triangle"></i> Struktur data tidak sesuai
                        </small>
                    </div>
                </div>
            ';
        }
    } else {
        echo '
            <div class="row mb-3 border-bottom pb-2">
                <div class="col-2 mb-2">
                    <input type="text" class="form-control form-control-sm" 
                        name="district_code[]" 
                        value="'.$district_code.'" readonly>
                </div>
                <div class="col-3 mb-2">
                    <input type="text" class="form-control form-control-sm" 
                        name="district_name[]" 
                        value="'.$district_name.'" readonly>
                </div>
                <div class="col-7 mb-2">
                    <small class="text-danger">
                        <i class="bi bi-x-circle"></i> HTTP Status '.$httpcode.' - Gagal mengambil data
                    </small>
                </div>
            </div>
        ';
    }
?>
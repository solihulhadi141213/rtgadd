<?php
    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    date_default_timezone_set("Asia/Jakarta");

    // Header agar response berupa JSON
    header('Content-Type: application/json');

    // Validasi Akses
    if(empty($SessionIdAccess)){
        http_response_code(401);
        echo json_encode(["error" => "Sesi akses sudah berakhir"]);
        exit;
    }

    // Validasi id_geo_region
    if(empty($_POST['id_geo_region'])){
        http_response_code(400);
        echo json_encode(["error" => "id_geo_region tidak dikirim"]);
        exit;
    }
    $id_geo_region = $_POST['id_geo_region'];

    // Ambil data coordinates dan info district
    $query = mysqli_query($Conn, "
        SELECT 
            gr.coordinates, 
            gr.district_name,
            gr.province_name,
            gr.district_code
        FROM geo_region gr 
        WHERE gr.id_geo_region = '$id_geo_region'
    ");
    $data = mysqli_fetch_array($query);

    if(!$data || empty($data['coordinates'])){
        http_response_code(404);
        echo json_encode(["error" => "Data koordinat tidak ditemukan"]);
        exit;
    }

    $coordinates_json = $data['coordinates'];
    $district_name = $data['district_name'];
    $province_name = $data['province_name'];

    // Karena coordinates sudah berupa array langsung, kita kembalikan sebagai JSON
    $coordinates_array = json_decode($coordinates_json, true);

    if(json_last_error() !== JSON_ERROR_NONE){
        http_response_code(500);
        echo json_encode(["error" => "Format koordinat tidak valid: " . json_last_error_msg()]);
        exit;
    }

    // Kembalikan response dengan struktur yang jelas
    echo json_encode([
        'success' => true,
        'coordinates' => $coordinates_array,
        'district_name' => $district_name,
        'province_name' => $province_name
    ]);
?>
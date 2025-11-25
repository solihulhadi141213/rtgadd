<?php
    // Tetapkan Tipe Data 
    header("Content-Type: application/json");

    // Koneksi Database
    include "../../_Config/Connection.php";

    // Ambil province_code dari request
    $province_code = $_GET['province_code'] ?? '';

    // Validasi keberadaan province_code
    if(empty($province_code)){
        echo json_encode([
            "status" => 201,
            "message" => 'Kode Provinsi Tidak Boleh Kosong',
            "metadata" => [],
        ]);
        exit;
    }

    try {
        // Validasi koneksi database
        if (!$Conn) {
            throw new Exception("Koneksi database gagal");
        }

        // Array district_code yang menjadi sample (garis merah)
        // $sample_district = [3374, 6403, 1473, 1505, 1211];
        $sample_district = [];

        // Inisialisasi features untuk GeoJSON
        $features = [];

        // Query data region
        $query_region = mysqli_query($Conn, "SELECT * FROM region WHERE category='District' AND province_code='$province_code'");
        
        if (!$query_region) {
            throw new Exception("Query region gagal: " . mysqli_error($Conn));
        }

        while ($data_region = mysqli_fetch_assoc($query_region)) {
            $id_region = $data_region['id_region'];
            $province_code = $data_region['province_code'];
            $province_name = $data_region['province_name'];
            $district_code = $data_region['district_code'];
            $district_name = $data_region['district_name'];

            // Ambil coordinates dari tabel geo_region
            $query_geo = mysqli_query($Conn, "SELECT coordinates FROM geo_region WHERE district_code='$district_code'");
            
            if (!$query_geo) {
                continue; // Skip jika tidak ada data geografis
            }
            
            $geo_data = mysqli_fetch_assoc($query_geo);
            
            if (!$geo_data || empty($geo_data['coordinates'])) {
                continue; // Skip jika tidak ada coordinates
            }

            // Decode coordinates JSON
            $coordinates_json = $geo_data['coordinates'];
            $geo_data_decoded = json_decode($coordinates_json, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || !isset($geo_data_decoded['features'])) {
                continue; // Skip jika format JSON tidak valid
            }

            // Hitung Kurang Guru

            #inisiasi kuarang guru
            $KurangGuru = 0;
            $nilaiAbk   = 0;

            # Loop 'school'
            $query_school = mysqli_query($Conn, "SELECT id_school FROM school WHERE id_region='$id_region'");
            if ($query_school) {
                while ($data_school = mysqli_fetch_assoc($query_school)) {
                    $id_school = $data_school['id_school'];

                    # Loop 'KurangGuru' from 'position_school'
                    $query_position = mysqli_query($Conn, "SELECT KurangGuru FROM position_school WHERE id_school='$id_school'");
                    if ($query_position) {
                        while ($data_position = mysqli_fetch_assoc($query_position)) {
                            $KurangGuru += intval($data_position['KurangGuru'] ?? 0);
                        }
                    }

                    # Loop 'KurangGuru' from 'position_school'
                    $query_position2 = mysqli_query($Conn, "SELECT abk FROM position_school WHERE id_school='$id_school'");
                    if ($query_position2) {
                        while ($data_position2 = mysqli_fetch_assoc($query_position2)) {
                            $nilaiAbk += intval($data_position2['abk'] ?? 0);
                        }
                    }
                }
            }
            if(empty($nilaiAbk)){
                $persentase = 0;
            }else{
                $persentase = ($KurangGuru/$nilaiAbk)*100;
            }
            $persentase_fix = round($persentase);

            // Cek apakah district termasuk sample
            $is_sample = in_array(intval($district_code), $sample_district);

            // Proses setiap feature dalam GeoJSON
            foreach ($geo_data_decoded['features'] as $feature) {
                if (isset($feature['geometry']) && isset($feature['geometry']['coordinates'])) {
                    $features[] = [
                        "type" => "Feature",
                        "properties" => [
                            "id_region" => $id_region,
                            "province_code" => $province_code,
                            "province_name" => $province_name,
                            "district_code" => $district_code,
                            "district_name" => $district_name,
                            "kurang_guru" => $persentase_fix,
                            "is_sample" => $is_sample
                        ],
                        "geometry" => $feature['geometry']
                    ];
                }
            }
        }

        // Buat GeoJSON structure
        $geojson = [
            "type" => "FeatureCollection",
            "features" => $features
        ];

        if (empty($features)) {
            echo json_encode([
                "status" => 201,
                "message" => 'Tidak Ada Data Geografis Untuk Provinsi Ini',
                "metadata" => [],
            ]);
            exit;
        }

        echo json_encode($geojson);

    } catch (Exception $e) {
        echo json_encode([
            "status" => 500,
            "message" => 'Error: ' . $e->getMessage(),
            "metadata" => [],
        ]);
        exit;
    }
?>
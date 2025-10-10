<?php
    header('Content-Type: application/json');
    include_once "../../_Config/Connection.php";
    include_once "../../_Config/GlobalFunction.php";
    include_once "../../_Config/Session.php";

    // Validasi Akses
    if (empty($SessionIdAccess)) {
        echo json_encode([
            "empty_province_code" => 0,
            "empty_province_code_dapodik" => 0,
            "empty_province_name" => 0,
            "empty_district_code" => 0,
            "empty_district_code_dapodik" => 0,
            "empty_district_name" => 0,
            "insert_province_success" => 0,
            "insert_province_failed" => 0,
            "update_province_success" => 0,
            "update_province_failed" => 0,
            "insert_district_success" => 0,
            "insert_district_failed" => 0,
            "update_district_success" => 0,
            "update_district_failed" => 0,
            "error_details" => ["Sesi Akses Sudah Berakhir! Silahkan Login Ulang."]
        ]);
        exit;
    }

    // Cek jika batch tidak ada
    if (!isset($_POST['batch']) || empty($_POST['batch'])) {
        echo json_encode([
            "empty_province_code" => 0,
            "empty_province_code_dapodik" => 0,
            "empty_province_name" => 0,
            "empty_district_code" => 0,
            "empty_district_code_dapodik" => 0,
            "empty_district_name" => 0,
            "insert_province_success" => 0,
            "insert_province_failed" => 0,
            "update_province_success" => 0,
            "update_province_failed" => 0,
            "insert_district_success" => 0,
            "insert_district_failed" => 0,
            "update_district_success" => 0,
            "update_district_failed" => 0,
            "error_details" => []
        ]);
        exit;
    }

    // Ambil batch data
    $batch = json_decode($_POST['batch'], true);

    // Validasi jika batch bukan array
    if (!is_array($batch)) {
        echo json_encode([
            "empty_province_code" => 0,
            "empty_province_code_dapodik" => 0,
            "empty_province_name" => 0,
            "empty_district_code" => 0,
            "empty_district_code_dapodik" => 0,
            "empty_district_name" => 0,
            "insert_province_success" => 0,
            "insert_province_failed" => 0,
            "update_province_success" => 0,
            "update_province_failed" => 0,
            "insert_district_success" => 0,
            "insert_district_failed" => 0,
            "update_district_success" => 0,
            "update_district_failed" => 0,
            "error_details" => ["Data batch tidak valid"]
        ]);
        exit;
    }

    // Inisialisasi semua counter
    $response = [
        // Validasi
        'empty_province_code' => 0,
        'empty_province_code_dapodik' => 0,
        'empty_province_name' => 0,
        'empty_district_code' => 0,
        'empty_district_code_dapodik' => 0,
        'empty_district_name' => 0,
        
        // Operasi Database Province
        'insert_province_success' => 0,
        'insert_province_failed' => 0,
        'update_province_success' => 0,
        'update_province_failed' => 0,
        
        // Operasi Database District
        'insert_district_success' => 0,
        'insert_district_failed' => 0,
        'update_district_success' => 0,
        'update_district_failed' => 0,
        
        // Error details
        'error_details' => []
    ];

    foreach ($batch as $rowIndex => $row) {
        // Pastikan row tidak kosong
        if (empty($row) || !is_array($row)) {
            $response['error_details'][] = "Data row kosong";
            continue;
        }

        // Extract data dari row (sesuai header CSV)
        $province_code          = $row['province_code'] ?? '';
        $province_code_dapodik  = $row['province_code_dapodik'] ?? '';
        $province_name          = $row['province_name'] ?? '';
        $district_code          = $row['district_code'] ?? '';
        $district_code_dapodik  = $row['district_code_dapodik'] ?? '';
        $district_name          = $row['district_name'] ?? '';
        $code_map               = $row['code_map'] ?? '';

        // Validasi baris kosong
        if(empty($province_code) && empty($province_code_dapodik) && empty($province_name) && 
        empty($district_code) && empty($district_code_dapodik) && empty($district_name)) {
            continue;
        }

        // Validasi data wajib untuk Province
        if(empty($province_code)) {
            $response['empty_province_code']++;
            $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Kode provinsi (BPS) tidak boleh kosong";
            continue;
        }
        
        if(empty($province_code_dapodik)) {
            $response['empty_province_code_dapodik']++;
            $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Kode provinsi (Dapodik) tidak boleh kosong";
            continue;
        }

        if(empty($province_name)) {
            $response['empty_province_name']++;
            $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Nama provinsi tidak boleh kosong";
            continue;
        }
        
        // Escape data untuk keamanan
        $province_code          = mysqli_real_escape_string($Conn, $province_code);
        $province_code_dapodik  = mysqli_real_escape_string($Conn, $province_code_dapodik);
        $province_name          = mysqli_real_escape_string($Conn, $province_name);
        $district_code          = mysqli_real_escape_string($Conn, $district_code);
        $district_code_dapodik  = mysqli_real_escape_string($Conn, $district_code_dapodik);
        $district_name          = mysqli_real_escape_string($Conn, $district_name);
        $code_map               = mysqli_real_escape_string($Conn, $code_map);
        
        // PROSES PROVINCE (Category = 'Province')
        // Cek apakah province_code sudah ada sebagai Province
        $id_region_province = GetDetailData($Conn, 'region', 'province_code', $province_code, 'id_region');
        
        if(empty($id_region_province)) {
            // INSERT PROVINCE BARU
            $category = "Province";
            $EntryProvince = "INSERT INTO region (
                category,
                province_code,
                province_code_dapodik,
                province_name,
                district_code,
                district_code_dapodik,
                district_name,
                code_map
            ) VALUES (
                '$category',
                '$province_code',
                '$province_code_dapodik',
                '$province_name',
                '',
                '',
                '',
                '$code_map'
            )";
            
            $InputProvince = mysqli_query($Conn, $EntryProvince);
            if($InputProvince) {
                $response['insert_province_success']++;
            } else {
                $response['insert_province_failed']++;
                $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Gagal insert provinsi - " . mysqli_error($Conn);
            }
        } else {
            // UPDATE PROVINCE YANG SUDAH ADA
            $sql = "UPDATE region SET 
                province_name = ?,
                province_code_dapodik = ?,
                code_map = ?
                WHERE province_code = ? AND category = 'Province'";
            $stmt = $Conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param(
                    "ssss",
                    $province_name,
                    $province_code_dapodik,
                    $code_map,
                    $province_code
                );

                $Input = $stmt->execute();
                if ($Input) {
                    $response['update_province_success']++;
                } else {
                    $response['update_province_failed']++;
                    $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Gagal update provinsi - " . $stmt->error;
                }
                $stmt->close();
            } else {
                $response['update_province_failed']++;
                $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Prepare update provinsi gagal - " . $Conn->error;
            }
        }
        
        // PROSES DISTRICT (Category = 'District') - Hanya jika ada data district
        if(!empty($district_code) && !empty($district_code_dapodik) && !empty($district_name)) {
            
            // Validasi data district
            if(empty($district_code)) {
                $response['empty_district_code']++;
                $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Kode Kab/Kota (BPS) tidak boleh kosong";
                // Lanjutkan ke row berikutnya, jangan stop proses
            }
            
            if(empty($district_code_dapodik)) {
                $response['empty_district_code_dapodik']++;
                $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Kode Kab/Kota (Dapodik) tidak boleh kosong";
                // Lanjutkan ke row berikutnya, jangan stop proses
            }
            
            if(empty($district_name)) {
                $response['empty_district_name']++;
                $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Nama Kab/Kota tidak boleh kosong";
                // Lanjutkan ke row berikutnya, jangan stop proses
            }
            
            // Cek apakah district_code sudah ada sebagai District
            $id_region_district = GetDetailData($Conn, 'region', 'district_code', $district_code, 'id_region');
            
            if(empty($id_region_district)) {
                // INSERT DISTRICT BARU
                $category = "District";
                $EntryDistrict = "INSERT INTO region (
                    category,
                    province_code,
                    province_code_dapodik,
                    province_name,
                    district_code,
                    district_code_dapodik,
                    district_name,
                    code_map
                ) VALUES (
                    '$category',
                    '$province_code',
                    '$province_code_dapodik',
                    '$province_name',
                    '$district_code',
                    '$district_code_dapodik',
                    '$district_name',
                    '$code_map'
                )";
                
                $InputDistrict = mysqli_query($Conn, $EntryDistrict);
                if($InputDistrict) {
                    $response['insert_district_success']++;
                } else {
                    $response['insert_district_failed']++;
                    $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Gagal insert kab/kota - " . mysqli_error($Conn);
                }
            } else {
                // UPDATE DISTRICT YANG SUDAH ADA
                $sql = "UPDATE region SET 
                    district_name = ?,
                    district_code_dapodik = ?,
                    province_name = ?,
                    province_code_dapodik = ?,
                    code_map = ?
                    WHERE district_code = ? AND category = 'District'";
                $stmt = $Conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param(
                        "ssssss",
                        $district_name,
                        $district_code_dapodik,
                        $province_name,
                        $province_code_dapodik,
                        $code_map,
                        $district_code
                    );

                    $Input = $stmt->execute();
                    if ($Input) {
                        $response['update_district_success']++;
                    } else {
                        $response['update_district_failed']++;
                        $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Gagal update kab/kota - " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $response['update_district_failed']++;
                    $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Prepare update kab/kota gagal - " . $Conn->error;
                }
            }
        }
    }

    echo json_encode($response);
?>
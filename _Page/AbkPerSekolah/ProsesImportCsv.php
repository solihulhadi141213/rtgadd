<?php
    header('Content-Type: application/json');
    include_once "../../_Config/Connection.php";
    include_once "../../_Config/GlobalFunction.php";

    // Cek jika batch tidak ada
    if (!isset($_POST['batch']) || empty($_POST['batch'])) {
        echo json_encode(getEmptyResponse());
        exit;
    }

    // Ambil batch data
    $batch = json_decode($_POST['batch'], true);

    // Validasi jika batch bukan array
    if (!is_array($batch)) {
        echo json_encode(getEmptyResponse());
        exit;
    }

    // Inisialisasi semua counter
    $response = [
        // Data validation
        'empty_province_data' => 0,
        'empty_school_data' => 0,
        'empty_position_data' => 0,
        
        // Insert operations
        'insert_province_success' => 0,
        'insert_province_failed' => 0,
        'insert_district_success' => 0,
        'insert_district_failed' => 0,
        'insert_school_success' => 0,
        'insert_school_failed' => 0,
        'insert_position_success' => 0,
        'insert_position_failed' => 0,
        
        // Main data operations
        'registered_data' => 0,
        'update_success' => 0,
        'update_failed' => 0,
        'insert_success' => 0,
        'insert_failed' => 0,
        
        // Error details
        'error_details' => []
    ];

    foreach ($batch as $row) {
        // Pastikan row tidak kosong
        if (empty($row) || !is_array($row)) {
            $response['insert_failed']++;
            continue;
        }

        // Extract data dari row
        $province_code          = $row['province_code'] ?? '';
        $province_code_dapodik  = $row['province_code_dapodik'] ?? '';
        $province_name          = $row['province_name'] ?? '';
        $district_code          = $row['district_code'] ?? '';
        $district_code_dapodik  = $row['district_code_dapodik'] ?? '';
        $district_name          = $row['district_name'] ?? '';
        $npsn                   = $row['npsn'] ?? '';
        $school_name            = $row['school_name'] ?? '';
        $school_level           = $row['school_level'] ?? '';
        $position_code          = $row['position_code'] ?? '';
        $position_name          = $row['position_name'] ?? '';
        $abk                    = $row['abk'] ?? 0;
        $asn                    = $row['asn'] ?? 0;
        $PPPK2024               = $row['PPPK2024'] ?? 0;
        $NonASN_sblmOkt2022     = $row['NonASN_sblmOkt2022'] ?? 0;
        $NonASN_stlhOkt2022     = $row['NonASN_stlhOkt2022'] ?? 0;
        $JmlGuru                = $row['JmlGuru'] ?? 0;
        $KurangGuru             = $row['KurangGuru'] ?? 0;
        $JmlASN                 = $row['JmlASN'] ?? 0;
        $KrngASN                = $row['KrngASN'] ?? 0;

        // Validasi Data
        if(empty($province_code) || empty($province_code_dapodik) || empty($province_name)){
            $response['empty_province_data']++;
            continue;
        }

        if(empty($npsn) || empty($school_name) || empty($school_level)){
            $response['empty_school_data']++;
            continue;
        }

        if(empty($position_code) || empty($position_name)){
            $response['empty_position_data']++;
            continue;
        }

        // PROSES PROVINCE
        $id_region_province = GetDetailData($Conn, 'region', 'province_code', $province_code, 'id_region');
        if (empty($id_region_province)) {
            $category = "Province";
            $code_map = "";
            $EntryProvince = "INSERT INTO region (category, province_code, province_code_dapodik, province_name, district_code, district_code_dapodik, district_name, code_map) VALUES ('$category', '$province_code', '$province_code_dapodik', '$province_name', '', '', '', '$code_map')";
            $InputProvince = mysqli_query($Conn, $EntryProvince);
            if ($InputProvince) {
                $response['insert_province_success']++;
            } else {
                $response['insert_province_failed']++;
                $response['error_details'][] = "Gagal insert provinsi: " . mysqli_error($Conn);
                continue;
            }
        }

        // PROSES KABUPATEN/KOTA
        $id_region = null;
        if (!empty($district_code)) {
            $id_region_district = GetDetailData($Conn, 'region', 'district_code', $district_code, 'id_region');
            
            if(empty($id_region_district)){
                $category = "District";
                $code_map = "";
                $EntryDistrict = "INSERT INTO region (category, province_code, province_code_dapodik, province_name, district_code, district_code_dapodik, district_name, code_map) VALUES ('$category', '$province_code', '$province_code_dapodik', '$province_name', '$district_code', '$district_code_dapodik', '$district_name', '$code_map')";
                $InputDistrict = mysqli_query($Conn, $EntryDistrict);
                if ($InputDistrict) {
                    $response['insert_district_success']++;
                    $id_region = $Conn->insert_id;
                } else {
                    $response['insert_district_failed']++;
                    $response['error_details'][] = "Gagal insert kab/kota: " . mysqli_error($Conn);
                    // Fallback ke province
                    $id_region = GetDetailData($Conn, 'region', 'province_code', $province_code, 'id_region');
                }
            } else {
                $id_region = $id_region_district;
            }
        } else {
            $id_region = GetDetailData($Conn, 'region', 'province_code', $province_code, 'id_region');
        }

        // PROSES SEKOLAH
        $id_school = GetDetailData($Conn, 'school', 'npsn', $npsn, 'id_school');
        if (empty($id_school)) {
            $EntrySchool = "INSERT INTO school (id_region, npsn, school_name, school_level) VALUES ('$id_region', '$npsn', '$school_name', '$school_level')";
            $InputSchool = mysqli_query($Conn, $EntrySchool);
            if ($InputSchool) {
                $response['insert_school_success']++;
            } else {
                $response['insert_school_failed']++;
                $response['error_details'][] = "Gagal insert sekolah {$npsn}: " . mysqli_error($Conn);
                continue;
            }
        }
        
        $id_school = GetDetailData($Conn, 'school', 'npsn', $npsn, 'id_school');

        // PROSES JABATAN
        $id_position = GetDetailData($Conn, 'position', 'position_code', $position_code, 'id_position');
        if (empty($id_position)) {
            $EntryPosition = "INSERT INTO position (position_code, position_name) VALUES ('$position_code', '$position_name')";
            $InputPosition = mysqli_query($Conn, $EntryPosition);
            if ($InputPosition) {
                $response['insert_position_success']++;
            } else {
                $response['insert_position_failed']++;
                $response['error_details'][] = "Gagal insert jabatan {$position_code}: " . mysqli_error($Conn);
                continue;
            }
        }
        
        $id_position = GetDetailData($Conn, 'position', 'position_code', $position_code, 'id_position');

        // PROSES DATA UTAMA
        $Qry = $Conn->prepare("SELECT id_position_school FROM position_school WHERE id_school = ? AND id_position = ?");
        if ($Qry === false) {
            $response['insert_failed']++;
            $response['error_details'][] = "Error prepare query: " . $Conn->error;
            continue;
        }
        
        $Qry->bind_param("ii", $id_school, $id_position);
        if (!$Qry->execute()) {
            $response['insert_failed']++;
            $response['error_details'][] = "Error execute query: " . $Qry->error;
            continue;
        }
        
        $Result = $Qry->get_result();
        $Data = $Result ? $Result->fetch_assoc() : null;
        $Qry->close();
        
        if (!empty($Data['id_position_school'])) {
            // UPDATE DATA UTAMA
            $id_position_school = $Data['id_position_school'];
            $sql = "UPDATE position_school SET 
                abk=?, 
                asn=?, 
                PPPK2024=?, 
                NonASN_sblmOkt2022=?, 
                NonASN_stlhOkt2022=?, 
                JmlGuru=?, 
                KurangGuru=?,
                JmlASN=?,
                KrngASN=?
                WHERE id_position_school=?";
            $stmt = $Conn->prepare($sql);
            if ($stmt === false) {
                $response['update_failed']++;
                $response['error_details'][] = "Error prepare update: " . $Conn->error;
                continue;
            }
            
            $stmt->bind_param("iiiiiiiiii", $abk, $asn, $PPPK2024, $NonASN_sblmOkt2022, $NonASN_stlhOkt2022, $JmlGuru, $KurangGuru, $JmlASN, $KrngASN, $id_position_school);
            $execute = $stmt->execute();
            if ($execute) {
                $response['update_success']++;
            } else {
                $response['update_failed']++;
                $response['error_details'][] = "Error execute update: " . $stmt->error;
            }
            $stmt->close();
        } else {
            // INSERT DATA UTAMA
            $insert = $Conn->prepare("INSERT INTO position_school (
                id_school, 
                id_position, 
                abk, 
                asn, 
                PPPK2024, 
                NonASN_sblmOkt2022, 
                NonASN_stlhOkt2022, 
                JmlGuru, 
                KurangGuru,
                JmlASN,
                KrngASN
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            if ($insert === false) {
                $response['insert_failed']++;
                $response['error_details'][] = "Error prepare insert: " . $Conn->error;
                continue;
            }
            
            $insert->bind_param(
                "iiiiiiiiiii", 
                $id_school, 
                $id_position, 
                $abk, 
                $asn, 
                $PPPK2024, 
                $NonASN_sblmOkt2022, 
                $NonASN_stlhOkt2022, 
                $JmlGuru, 
                $KurangGuru,
                $JmlASN,
                $KrngASN
            );
            
            $execInsert = $insert->execute();
            if ($execInsert) {
                $response['insert_success']++;
            } else {
                $response['insert_failed']++;
                $response['error_details'][] = "Error execute insert: " . $insert->error;
            }
            $insert->close();
        }
    }

    echo json_encode($response);

    function getEmptyResponse() {
        return [
            'empty_province_data' => 0,
            'empty_school_data' => 0,
            'empty_position_data' => 0,
            'insert_province_success' => 0,
            'insert_province_failed' => 0,
            'insert_district_success' => 0,
            'insert_district_failed' => 0,
            'insert_school_success' => 0,
            'insert_school_failed' => 0,
            'insert_position_success' => 0,
            'insert_position_failed' => 0,
            'registered_data' => 0,
            'update_success' => 0,
            'update_failed' => 0,
            'insert_success' => 0,
            'insert_failed' => 0,
            'error_details' => []
        ];
    }
?>
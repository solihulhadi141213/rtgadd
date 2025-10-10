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
        
        // Operasi Data Tidak Ditemukan
        'kode_province_tidak_ada' => 0,
        'kode_district_tidak_ada' => 0,
        
        // Main data operations
        'update_success' => 0,
        'update_failed' => 0,
        'insert_success' => 0,
        'insert_failed' => 0,
        
        // Error details
        'error_details' => []
    ];

    foreach ($batch as $rowIndex => $row) {
        // Pastikan row tidak kosong
        if (empty($row) || !is_array($row)) {
            $response['insert_failed']++;
            $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Data row kosong";
            continue;
        }

        // Extract data dari row
        $province_code_dapodik  = $row['province_code_dapodik'] ?? '';
        $province_name          = $row['province_name'] ?? '';
        $district_code_dapodik  = $row['district_code_dapodik'] ?? '';
        $district_name          = $row['district_name'] ?? '';
        $ptkid                  = $row['ptkid'] ?? '';
        $status_asn             = $row['status_asn'] ?? '';
        $instansi_kelulusan     = $row['instansi_kelulusan'] ?? '';
        $jabatan_kelulusan      = $row['jabatan_kelulusan'] ?? '';
        $umur                   = $row['umur'] ?? 0;
        $pulau                  = $row['pulau'] ?? '';
        $perguruan_tinggi_s1    = $row['perguruan_tinggi_s1'] ?? '';
        $program_studi_s1       = $row['program_studi_s1'] ?? '';
        $bidang_studi_ppg       = $row['bidang_studi_ppg'] ?? '';
        $lptk                   = $row['lptk'] ?? '';
        $ppg_blm_diangkat       = $row['status'] ?? '';

        // Debug khusus untuk ppg_blm_diangkat
        if (empty($ppg_blm_diangkat)) {
            $response['insert_failed']++;
            $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Data Status ASN kosong";
            continue;
        }

        // Validasi Data Provinsi
        if(empty($province_code_dapodik) || empty($province_name)){
            $response['empty_province_data']++;
            $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Data provinsi kosong";
            continue;
        }

        // Validasi PTKID
        if(empty($ptkid)){
            $response['insert_failed']++;
            $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": PTKID kosong";
            continue;
        }

        // PROSES PROVINCE
        $id_region_province = GetDetailData($Conn, 'region', 'province_code_dapodik', $province_code_dapodik, 'id_region');
        if (empty($id_region_province)) {
            $response['kode_province_tidak_ada']++;
            $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Kode provinsi tidak ditemukan - " . $province_code_dapodik;
            continue;
        }

        // PROSES KABUPATEN/KOTA
        $id_region = null;
        if (!empty($district_code_dapodik)) {
            $id_region_district = GetDetailData($Conn, 'region', 'district_code_dapodik', $district_code_dapodik, 'id_region');
            
            if(empty($id_region_district)){
                $response['kode_district_tidak_ada']++;
                $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Kode kab/kota tidak ditemukan - " . $district_code_dapodik;
                continue;
            } else {
                $id_region = $id_region_district;
            }
        } else {
            $id_region = $id_region_province;
        }

        // PROSES DATA UTAMA
        $Qry = $Conn->prepare("SELECT id_calon_guru FROM calon_guru WHERE ptkid = ?");
        if ($Qry === false) {
            $response['insert_failed']++;
            $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Error prepare query - " . $Conn->error;
            continue;
        }
        
        $Qry->bind_param("s", $ptkid);
        if (!$Qry->execute()) {
            $response['insert_failed']++;
            $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Error execute query - " . $Qry->error;
            continue;
        }
        
        $Result = $Qry->get_result();
        $Data = $Result ? $Result->fetch_assoc() : null;
        $Qry->close();
        
        if (!empty($Data['id_calon_guru'])) {
            // UPDATE DATA UTAMA
            $id_calon_guru = $Data['id_calon_guru'];
            $sql = "UPDATE calon_guru SET 
                id_region=?, 
                status_asn=?, 
                instansi_kelulusan=?, 
                jabatan_kelulusan=?, 
                umur=?, 
                pulau=?, 
                perguruan_tinggi_s1=?,
                program_studi_s1=?,
                bidang_studi_ppg=?,
                ppg_blm_diangkat=?,
                lptk=?
                WHERE id_calon_guru=?";
            $stmt = $Conn->prepare($sql);
            if ($stmt === false) {
                $response['update_failed']++;
                $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Error prepare update - " . $Conn->error;
                continue;
            }
            
            $stmt->bind_param("isssissssssi", 
                $id_region, 
                $status_asn, 
                $instansi_kelulusan, 
                $jabatan_kelulusan, 
                $umur, 
                $pulau, 
                $perguruan_tinggi_s1, 
                $program_studi_s1, 
                $bidang_studi_ppg, 
                $ppg_blm_diangkat,
                $lptk,
                $id_calon_guru
            );
            $execute = $stmt->execute();
            if ($execute) {
                $response['update_success']++;
            } else {
                $response['update_failed']++;
                $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Error execute update - " . $stmt->error;
            }
            $stmt->close();
        } else {
            // INSERT DATA UTAMA
            $insert = $Conn->prepare("INSERT INTO calon_guru (
                ptkid,
                id_region, 
                status_asn, 
                instansi_kelulusan, 
                jabatan_kelulusan, 
                umur, 
                pulau, 
                perguruan_tinggi_s1, 
                program_studi_s1, 
                bidang_studi_ppg,
                ppg_blm_diangkat,
                lptk
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            if ($insert === false) {
                $response['insert_failed']++;
                $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Error prepare insert - " . $Conn->error;
                continue;
            }
            
            $insert->bind_param(
                "sisssissssss", 
                $ptkid,
                $id_region, 
                $status_asn, 
                $instansi_kelulusan, 
                $jabatan_kelulusan, 
                $umur, 
                $pulau, 
                $perguruan_tinggi_s1, 
                $program_studi_s1, 
                $bidang_studi_ppg,
                $ppg_blm_diangkat,
                $lptk
            );
            
            $execInsert = $insert->execute();
            if ($execInsert) {
                $response['insert_success']++;
            } else {
                $response['insert_failed']++;
                $response['error_details'][] = "Baris " . ($rowIndex + 1) . ": Error execute insert - " . $insert->error;
            }
            $insert->close();
        }
    }

    echo json_encode($response);

    function getEmptyResponse() {
        return [
            'empty_province_data' => 0,
            'kode_province_tidak_ada' => 0,
            'kode_district_tidak_ada' => 0,
            'update_success' => 0,
            'update_failed' => 0,
            'insert_success' => 0,
            'insert_failed' => 0,
            'error_details' => []
        ];
    }
?>
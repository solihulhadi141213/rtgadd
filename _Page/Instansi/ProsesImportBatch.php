<?php
    include_once "../../_Config/Connection.php";
    include_once "../../_Config/GlobalFunction.php";
    include_once "../../_Config/Session.php";

    // Time Zone
    date_default_timezone_set('Asia/Jakarta');
    $now = date('Y-m-d H:i:s');

    //kode map
    $code_map="";

    // Validasi Akses
    if (empty($SessionIdAccess)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sesi Akses Sudah Berakhir! Silahkan Login Ulang.'
        ]);
        exit;
    }

    // Validasi parameter
    if(empty($_POST['file_token']) || empty($_POST['batch']) || empty($_POST['total_batches'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Parameter tidak valid'
        ]);
        exit;
    }

    $file_token = $_POST['file_token'];
    $current_batch = intval($_POST['batch']);
    $total_batches = intval($_POST['total_batches']);
    $batch_size = 100;

    // Ambil data dari session
    if(!isset($_SESSION['import_data_' . $file_token])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Data import tidak ditemukan. Silahkan upload ulang file.'
        ]);
        exit;
    }

    $sheetData = $_SESSION['import_data_' . $file_token];
    $JumlahBaris = count($sheetData);

    // Hitung range data untuk batch ini
    $start_row = 1 + (($current_batch - 1) * $batch_size);
    $end_row = min($start_row + $batch_size - 1, $JumlahBaris - 1);

    $html_output = '';
    $JumlahKodeValid = 0;

    for($i = $start_row; $i <= $end_row; $i++) {
        // Validasi baris kosong
        if(empty($sheetData[$i][0]) && empty($sheetData[$i][1]) && empty($sheetData[$i][2]) && empty($sheetData[$i][3]) && empty($sheetData[$i][4]) && empty($sheetData[$i][5]) && empty($sheetData[$i][6]) && empty($sheetData[$i][7])) {
            continue; // Lewati baris kosong
        }

        // Validasi kolom wajib
        if(empty($sheetData[$i][0])) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Kode provinsi (BPS) tidak boleh kosong</small>
                </td>
            </tr>';
            continue;
        }

        if(empty($sheetData[$i][1])) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Kode provinsi (DAPODIK) tidak boleh kosong</small>
                </td>
            </tr>';
            continue;
        }

        if(empty($sheetData[$i][2])) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Nama provinsi tidak boleh kosong</small>
                </td>
            </tr>';
            continue;
        }

        if(empty($sheetData[$i][3])||empty($sheetData[$i][4])||empty($sheetData[$i][5])) {
            $organization_level     ="Province";
            $district_code          = "";
            $district_code_dapodik  = "";
            $district_name          = "";
        }else{
            $organization_level     ="District";
            $district_code          = mysqli_real_escape_string($Conn, $sheetData[$i][3]);
            $district_code_dapodik  = mysqli_real_escape_string($Conn, $sheetData[$i][4]);
            $district_name          = mysqli_real_escape_string($Conn, $sheetData[$i][5]);
        }

        if(empty($sheetData[$i][6])) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td>'.$organization_level.'</td>
                <td>'.$sheetData[$i][2].'</td>
                <td>'.$sheetData[$i][5].'</td>
                <td colspan="3" class="text-center">
                    <small class="text-danger">Kode Instansi tidak boleh kosong</small>
                </td>
            </tr>';
            continue;
        }

        if(empty($sheetData[$i][7])) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td>'.$organization_level.'</td>
                <td>'.$sheetData[$i][2].'</td>
                <td>'.$sheetData[$i][5].'</td>
                <td>'.$sheetData[$i][6].'</td>
                <td colspan="2" class="text-center">
                    <small class="text-danger">Nama Instansi tidak boleh kosong</small>
                </td>
            </tr>';
            continue;
        }

        $province_code          = mysqli_real_escape_string($Conn, $sheetData[$i][0]);
        $province_code_dapodik  = mysqli_real_escape_string($Conn, $sheetData[$i][1]);
        $province_name          = mysqli_real_escape_string($Conn, $sheetData[$i][2]);
        $organization_code      = mysqli_real_escape_string($Conn, $sheetData[$i][6]);
        $organization_name      = mysqli_real_escape_string($Conn, $sheetData[$i][7]);

        // PROSES PROVINSI
        // Cek apakah kode provinsi sudah ada (cek kedua kode: BPS dan DAPODIK)
        $id_region_province = null;
        
        // Cek berdasarkan kode BPS
        if(!empty($province_code)) {
            $id_region_province = GetDetailData($Conn, 'region', 'province_code', $province_code, 'id_region');
        }
        
        // Jika tidak ditemukan berdasarkan BPS, cek berdasarkan DAPODIK
        if(empty($id_region_province) && !empty($province_code_dapodik)) {
            $id_region_province = GetDetailData($Conn, 'region', 'province_code_dapodik', $province_code_dapodik, 'id_region');
        }
        
        // Jika provinsi belum ada, insert sebagai Province
        if(empty($id_region_province)) {
            $category = "Province";
            $EntryProvince = "INSERT INTO region 
                (category, province_code, province_code_dapodik, province_name, district_code, district_code_dapodik, district_name, code_map) 
                VALUES 
                ('$category', '$province_code', '$province_code_dapodik', '$province_name', '', '', '', '$code_map')";
            $InputProvince = mysqli_query($Conn, $EntryProvince);
            
            if($InputProvince) {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$organization_level.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td colspan="4" class="text-center">
                        <small class="text-success">Data Provinsi Baru Berhasil Disimpan</small>
                    </td>
                </tr>';
            } else {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$organization_level.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td colspan="4" class="text-center">
                        <small class="text-danger">Data Provinsi Baru Gagal Disimpan: '.mysqli_error($Conn).'</small>
                    </td>
                </tr>';
            }
        }

        // PROSES KABUPATEN/KOTA
        if($organization_level=="District"){
            // Cek apakah kode kabupaten sudah ada (cek kedua kode: BPS dan DAPODIK)
            $id_region_district = null;
            
            // Cek berdasarkan kode BPS
            if(!empty($district_code)) {
                $id_region_district = GetDetailData($Conn, 'region', 'district_code', $district_code, 'id_region');
            }
            
            // Jika tidak ditemukan berdasarkan BPS, cek berdasarkan DAPODIK
            if(empty($id_region_district) && !empty($district_code_dapodik)) {
                $id_region_district = GetDetailData($Conn, 'region', 'district_code_dapodik', $district_code_dapodik, 'id_region');
            }
            
            // Jika kabupaten belum ada, insert sebagai District
            if(empty($id_region_district)) {
                $category = "District";
                $EntryDistrict = "INSERT INTO region 
                    (category, province_code, province_code_dapodik, province_name, district_code, district_code_dapodik, district_name, code_map) 
                    VALUES 
                    ('$category', '$province_code', '$province_code_dapodik', '$province_name', '$district_code', '$district_code_dapodik', '$district_name', '$code_map')";
                $InputDistrict = mysqli_query($Conn, $EntryDistrict);
                
                if($InputDistrict) {
                    $html_output .= '
                    <tr>
                        <td>'.$i.'</td>
                        <td>'.$organization_level.'</td>
                        <td>'.$sheetData[$i][2].'</td>
                        <td>'.$sheetData[$i][5].'</td>
                        <td colspan="3" class="text-center">
                            <small class="text-success">Data Kab/Kota Baru Berhasil Disimpan</small>
                        </td>
                    </tr>';
                } else {
                    $html_output .= '
                    <tr>
                        <td>'.$i.'</td>
                        <td>'.$organization_level.'</td>
                        <td>'.$sheetData[$i][2].'</td>
                        <td>'.$sheetData[$i][5].'</td>
                        <td colspan="3" class="text-center">
                            <small class="text-danger">Data Kab/Kota Baru Gagal Disimpan: '.mysqli_error($Conn).'</small>
                        </td>
                    </tr>';
                }
            }
        }

        // PROSES SEKOLAH
        
        if($organization_level=="District"){
            // Cari id_region berdasarkan district_code (prioritas BPS, lalu DAPODIK)
            $id_region = null;
            if(!empty($district_code)) {
                $id_region = GetDetailData($Conn, 'region', 'district_code', $district_code, 'id_region');
            }
            if(empty($id_region) && !empty($district_code_dapodik)) {
                $id_region = GetDetailData($Conn, 'region', 'district_code_dapodik', $district_code_dapodik, 'id_region');
            }
            
            // Jika tidak ditemukan id_region, cari berdasarkan kombinasi provinsi+kabupaten
            if(empty($id_region)) {
                $query_region = "SELECT id_region FROM region WHERE province_code = '$province_code' AND district_name = '$district_name' LIMIT 1";
                $result_region = mysqli_query($Conn, $query_region);
                if($result_region && mysqli_num_rows($result_region) > 0) {
                    $row_region = mysqli_fetch_assoc($result_region);
                    $id_region = $row_region['id_region'];
                }
            }
        }else{
            $id_region = null;
            if(!empty($province_code)) {
                $id_region = GetDetailData($Conn, 'region', 'province_code', $province_code, 'id_region');
            }
            if(empty($id_region) && !empty($province_code_dapodik)) {
                $id_region = GetDetailData($Conn, 'region', 'province_code_dapodik', $province_code_dapodik, 'id_region');
            }
        }
        
        if(empty($id_region)) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td>'.$organization_level.'</td>
                <td>'.$sheetData[$i][2].'</td>
                <td>'.$sheetData[$i][5].'</td>
                <td>'.$sheetData[$i][6].'</td>
                <td>'.$sheetData[$i][7].'</td>
                <td class="text-center">
                    <small class="text-danger">Data wilayah tidak ditemukan</small>
                </td>
            </tr>';
            continue;
        }

        // Validasi Instansi Duplikat berdasarkan organization_code
        $id_organization = GetDetailData($Conn, 'organization', 'organization_code', $organization_code, 'id_organization');
        
        if(!empty($id_organization)){
            // Jika Sudah Ada Update sekolah
            $sql = "UPDATE organization SET id_region = ?, organization_level = ?, organization_code = ?, organization_name = ? WHERE id_organization = ?";
            $stmt = $Conn->prepare($sql);
            
            if (!$stmt) {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$organization_level.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td>'.$sheetData[$i][5].'</td>
                    <td>'.$sheetData[$i][6].'</td>
                    <td>'.$sheetData[$i][7].'</td>
                    <td class="text-center">
                        <small class="text-danger">Prepare gagal: '.htmlspecialchars($Conn->error).'</small>
                    </td>
                </tr>';
                continue;
            }
            
            $stmt->bind_param("isssi", $id_region, $organization_level, $organization_code, $organization_name, $id_organization);
            $Input = $stmt->execute();
            $stmt->close();
            
            if ($Input) {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$organization_level.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td>'.$sheetData[$i][5].'</td>
                    <td>'.$sheetData[$i][6].'</td>
                    <td>'.$sheetData[$i][7].'</td>
                    <td class="text-center">
                        <small class="text-success">Update Berhasil</small>
                    </td>
                </tr>';
                $JumlahKodeValid++;
            } else {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$organization_level.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td>'.$sheetData[$i][5].'</td>
                    <td>'.$sheetData[$i][6].'</td>
                    <td>'.$sheetData[$i][7].'</td>
                    <td class="text-center">
                        <small class="text-danger">Update Gagal: '.htmlspecialchars($Conn->error).'</small>
                    </td>
                </tr>';
            }
        } else {
            // Jika Belum Ada Lakukan Insert
            $EntryOrganization = "INSERT INTO organization (id_region, organization_level, organization_code, organization_name) VALUES ('$id_region', '$organization_level', '$organization_code', '$organization_name')";
            $InputOrganization = mysqli_query($Conn, $EntryOrganization);
            
            if($InputOrganization) {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$organization_level.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td>'.$sheetData[$i][5].'</td>
                    <td>'.$sheetData[$i][6].'</td>
                    <td>'.$sheetData[$i][7].'</td>
                    <td class="text-center">
                        <small class="text-primary">Insert Berhasil</small>
                    </td>
                </tr>';
                $JumlahKodeValid++;
            } else {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$organization_level.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td>'.$sheetData[$i][5].'</td>
                    <td>'.$sheetData[$i][6].'</td>
                    <td>'.$sheetData[$i][7].'</td>
                    <td class="text-center">
                        <small class="text-danger">Insert Gagal: '.mysqli_error($Conn).'</small>
                    </td>
                </tr>';
            }
        }
    }

    // Hapus data session jika sudah selesai semua batch
    if($current_batch == $total_batches) {
        unset($_SESSION['import_data_' . $file_token]);
        unset($_SESSION['import_total_rows_' . $file_token]);
    }

    // Tambahkan info batch
    $html_output .= '
    <tr>
        <td colspan="6" class="text-center">
            <small class="text-info">Batch ' . $current_batch . ' dari ' . $total_batches . ' selesai. ' . $JumlahKodeValid . ' data sekolah berhasil diproses.</small>
        </td>
    </tr>';

    echo json_encode([
        'status' => 'success',
        'html' => $html_output,
        'batch' => $current_batch,
        'total_batches' => $total_batches
    ]);
?>
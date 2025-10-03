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
                <td colspan="6" class="text-end">
                    <small class="text-danger">Kode provinsi (BPS) tidak boleh kosong</small>
                </td>
            </tr>';
            continue;
        }

        if(empty($sheetData[$i][1])) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td colspan="6" class="text-end">
                    <small class="text-danger">Kode provinsi (DAPODIK) tidak boleh kosong</small>
                </td>
            </tr>';
            continue;
        }

        if(empty($sheetData[$i][2])) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td colspan="6" class="text-end">
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
                <td>'.$sheetData[$i][2].'</td>
                <td>'.$sheetData[$i][5].'</td>
                <td colspan="4" class="text-end">
                    <small class="text-danger">Kode Instansi tidak boleh kosong</small>
                </td>
            </tr>';
            continue;
        }

        if(empty($sheetData[$i][7])) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td>'.$sheetData[$i][2].'</td>
                <td>'.$sheetData[$i][5].'</td>
                <td colspan="4" class="text-end">
                    <small class="text-danger">Nama Instansi tidak boleh kosong</small>
                </td>
            </tr>';
            continue;
        }
        if(empty($sheetData[$i][8])) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td>'.$sheetData[$i][2].'</td>
                <td>'.$sheetData[$i][5].'</td>
                <td>'.$sheetData[$i][7].'</td>
                <td colspan="3" class="text-end">
                    <small class="text-danger">Kode Jabatan tidak boleh kosong</small>
                </td>
            </tr>';
            continue;
        }

        if(empty($sheetData[$i][9])) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td>'.$sheetData[$i][2].'</td>
                <td>'.$sheetData[$i][5].'</td>
                <td>'.$sheetData[$i][7].'</td>
                <td colspan="3" class="text-end">
                    <small class="text-danger">Nama Jabatan tidak boleh kosong</small>
                </td>
            </tr>';
            continue;
        }

        $province_code          = mysqli_real_escape_string($Conn, $sheetData[$i][0]);
        $province_code_dapodik  = mysqli_real_escape_string($Conn, $sheetData[$i][1]);
        $province_name          = mysqli_real_escape_string($Conn, $sheetData[$i][2]);
        $organization_code      = mysqli_real_escape_string($Conn, $sheetData[$i][6]);
        $organization_name      = mysqli_real_escape_string($Conn, $sheetData[$i][7]);
        $position_code          = mysqli_real_escape_string($Conn, $sheetData[$i][8]);
        $position_name          = mysqli_real_escape_string($Conn, $sheetData[$i][9]);
        $formasi_ppg            = !empty($sheetData[$i][10]) ? (int)$sheetData[$i][10] : 0;

        // PROSES PROVINSI
        // Cek apakah kode provinsi sudah ada (cek kedua kode: BPS dan DAPODIK)
        $id_region = null;
        
        // Cek berdasarkan kode BPS
        if(!empty($province_code)) {
            $id_region = GetDetailData($Conn, 'region', 'province_code', $province_code, 'id_region');
        }
        
        // Jika tidak ditemukan berdasarkan BPS, cek berdasarkan DAPODIK
        if(empty($id_region) && !empty($province_code_dapodik)) {
            $id_region = GetDetailData($Conn, 'region', 'province_code_dapodik', $province_code_dapodik, 'id_region');
        }
        
        // Jika provinsi belum ada, insert sebagai Province
        if(empty($id_region)) {
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
                    <td>'.$sheetData[$i][2].'</td>
                    <td colspan="5" class="text-end">
                        <small class="text-success">Data Provinsi Baru Berhasil Disimpan</small>
                    </td>
                </tr>';
                $id_region = GetDetailData($Conn, 'region', 'province_code', $province_code, 'id_region');
            } else {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td colspan="5" class="text-end">
                        <small class="text-danger">Data Provinsi Baru Gagal Disimpan: '.mysqli_error($Conn).'</small>
                    </td>
                </tr>';
            }
        }

        // PROSES KABUPATEN/KOTA
        if($organization_level=="District"){
            // Cek apakah kode kabupaten sudah ada (cek kedua kode: BPS dan DAPODIK)
            $id_region = null;
            
            // Cek berdasarkan kode BPS
            if(!empty($district_code)) {
                $id_region = GetDetailData($Conn, 'region', 'district_code', $district_code, 'id_region');
            }
            
            // Jika tidak ditemukan berdasarkan BPS, cek berdasarkan DAPODIK
            if(empty($id_region) && !empty($district_code_dapodik)) {
                $id_region = GetDetailData($Conn, 'region', 'district_code_dapodik', $district_code_dapodik, 'id_region');
            }
            
            // Jika kabupaten belum ada, insert sebagai District
            if(empty($id_region)) {
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
                        <td>'.$sheetData[$i][2].'</td>
                        <td>'.$sheetData[$i][5].'</td>
                        <td colspan="4" class="text-end">
                            <small class="text-success">Data Kab/Kota Baru Berhasil Disimpan</small>
                        </td>
                    </tr>';
                    $id_region = GetDetailData($Conn, 'region', 'district_code', $district_code, 'id_region');
                } else {
                    $html_output .= '
                    <tr>
                        <td>'.$i.'</td>
                        <td>'.$sheetData[$i][2].'</td>
                        <td>'.$sheetData[$i][5].'</td>
                        <td colspan="4" class="text-end">
                            <small class="text-danger">Data Kab/Kota Baru Gagal Disimpan: '.mysqli_error($Conn).'</small>
                        </td>
                    </tr>';
                    $id_region =0;
                }
            }
        }

        //PROSES ORGANIZATION
        //Cek Kode Instansi
        $id_organization = GetDetailData($Conn, 'organization', 'organization_code', $organization_code, 'id_organization');
        if(empty($id_organization)){
            //Jika Belum ADa Maka Insert
            $EntryOrganization = "INSERT INTO organization (id_region, organization_level, organization_code, organization_name) 
                VALUES 
            ('$id_region', '$organization_level', '$organization_code', '$organization_name')";
            $InputOrganization = mysqli_query($Conn, $EntryOrganization);
            
            if($InputOrganization) {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td>'.$sheetData[$i][5].'</td>
                    <td>'.$sheetData[$i][7].'</td>
                    <td colspan="3" class="text-end">
                        <small class="text-success">Data Instansi Baru Berhasil Disimpan</small>
                    </td>
                </tr>';
                $id_organization = GetDetailData($Conn, 'organization', 'organization_code', $organization_code, 'id_organization');
            } else {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td>'.$sheetData[$i][5].'</td>
                    <td>'.$sheetData[$i][7].'</td>
                    <td colspan="3" class="text-end">
                        <small class="text-danger">Data Instansi Baru Gagal Disimpan: '.mysqli_error($Conn).'</small>
                    </td>
                </tr>';
                 $id_organization =0;
            }
        }

        //PROSES POSITION
        //Cek Kode jabatan
        $id_position = GetDetailData($Conn, 'position', 'position_code', $position_code, 'id_position');
        if(empty($id_position)){
            //Jika Belum ADa Maka Insert
            $EntryPosition = "INSERT INTO position (position_code, position_name) VALUES ('$position_code', '$position_name')";
            $InputPosition = mysqli_query($Conn, $EntryPosition);
            
            if($InputPosition) {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td>'.$sheetData[$i][5].'</td>
                    <td>'.$sheetData[$i][7].'</td>
                    <td>'.$sheetData[$i][9].'</td>
                    <td colspan="2" class="text-end">
                        <small class="text-success">Data Jabatan Baru Berhasil Disimpan</small>
                    </td>
                </tr>';
                $id_position = GetDetailData($Conn, 'position', 'position_code', $position_code, 'id_position');
            } else {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td>'.$sheetData[$i][5].'</td>
                    <td>'.$sheetData[$i][7].'</td>
                    <td>'.$sheetData[$i][9].'</td>
                    <td colspan="2" class="text-end">
                        <small class="text-danger">Data Jabatan Baru Gagal Disimpan: '.mysqli_error($Conn).'</small>
                    </td>
                </tr>';
                 $id_position =0;
            }
        }
        
        //Jika id_region tidak ada
        if(empty($id_region)) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td>'.$sheetData[$i][2].'</td>
                <td>'.$sheetData[$i][5].'</td>
                <td colspan="4" class="text-end">
                    <small class="text-danger">Data wilayah tidak ditemukan</small>
                </td>
            </tr>';
            continue;
        }

        // Validasi Instansi Duplikat berdasarkan id_region, id_position, id_organization
        $QryCekDuplikat = $Conn->prepare("SELECT id_position_organization FROM position_organization  WHERE id_region = ? AND id_position = ? AND id_organization = ?");
        $QryCekDuplikat->bind_param("iii", $id_region, $id_position, $id_organization);
        if (!$QryCekDuplikat->execute()) {
            $error=$Conn->error;
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td>'.$sheetData[$i][2].'</td>
                <td>'.$sheetData[$i][5].'</td>
                <td>'.$sheetData[$i][7].'</td>
                <td>'.$sheetData[$i][9].'</td>
                <td colspan="2" class="text-end">
                    <small class="text-danger">'.$error.'</small>
                </td>
            </tr>';
            continue;
        }
        $ResultCekDuplikat = $QryCekDuplikat->get_result();
        $DataCekDuplikat = $ResultCekDuplikat->fetch_assoc();
        $QryCekDuplikat->close();
        if(!empty($DataCekDuplikat['id_position_organization'])){
            $id_position_organization   = $DataCekDuplikat['id_position_organization'];
        }else{
            $id_position_organization   = "";
        }
        
        if(!empty($id_position_organization)){
            
            // Jika Sudah Ada Update
            $sql = "UPDATE position_organization SET id_region = ?, id_position = ?, id_organization = ?, category = ?, formasi_ppg = ? WHERE id_position_organization = ?";
            $stmt = $Conn->prepare($sql);
            
            if (!$stmt) {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td>'.$sheetData[$i][5].'</td>
                    <td>'.$sheetData[$i][7].'</td>
                    <td>'.$sheetData[$i][9].'</td>
                    <td>'.$sheetData[$i][10].'</td>
                    <td class="text-end">
                        <small class="text-danger">Prepare gagal: '.htmlspecialchars($Conn->error).'</small>
                    </td>
                </tr>';
                continue;
            }
            
            $stmt->bind_param("iiisii", $id_region, $id_position, $id_organization, $organization_level, $formasi_ppg, $id_position_organization);
            $Input = $stmt->execute();
            $stmt->close();
            
            if ($Input) {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td>'.$sheetData[$i][5].'</td>
                    <td>'.$sheetData[$i][7].'</td>
                    <td>'.$sheetData[$i][9].'</td>
                    <td>'.$sheetData[$i][10].'</td>
                    <td class="text-end">
                        <small class="text-success">Update Berhasil</small>
                    </td>
                </tr>';
                $JumlahKodeValid++;
            } else {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td>'.$sheetData[$i][5].'</td>
                    <td>'.$sheetData[$i][7].'</td>
                    <td>'.$sheetData[$i][9].'</td>
                    <td>'.$sheetData[$i][10].'</td>
                    <td class="text-end">
                        <small class="text-danger">Update Gagal: '.htmlspecialchars($Conn->error).'</small>
                    </td>
                </tr>';
            }
        } else {

            // Jika Belum Ada Lakukan Insert
            $EntryPositionByOrganization = "INSERT INTO position_organization (id_region, id_position, id_organization, category, formasi_ppg) VALUES ('$id_region', '$id_position', '$id_organization', '$organization_level', '$formasi_ppg')";
            $InputPositionByOrganization = mysqli_query($Conn, $EntryPositionByOrganization);
            
            if($InputPositionByOrganization) {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td>'.$sheetData[$i][5].'</td>
                    <td>'.$sheetData[$i][7].'</td>
                    <td>'.$sheetData[$i][9].'</td>
                    <td>'.$sheetData[$i][10].'</td>
                    <td class="text-end">
                        <small class="text-primary">Insert Berhasil</small>
                    </td>
                </tr>';
                $JumlahKodeValid++;
            } else {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td>'.$sheetData[$i][5].'</td>
                    <td>'.$sheetData[$i][7].'</td>
                    <td>'.$sheetData[$i][9].'</td>
                    <td>'.$sheetData[$i][10].'</td>
                    <td class="text-end">
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
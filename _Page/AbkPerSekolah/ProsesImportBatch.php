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
        if(empty($sheetData[$i][0]) && empty($sheetData[$i][1]) && empty($sheetData[$i][2]) && empty($sheetData[$i][3]) && empty($sheetData[$i][4]) && empty($sheetData[$i][5]) && empty($sheetData[$i][6]) && empty($sheetData[$i][7]) && empty($sheetData[$i][8]) && empty($sheetData[$i][9])) {
            continue; // Lewati baris kosong
        }

        // Validasi semua kolom wajib ada
        if(empty($sheetData[$i][0])) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td colspan="6" class="text-right">
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

        if(empty($sheetData[$i][3])) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Kode Kab/Kota (BPS) tidak boleh kosong</small>
                </td>
            </tr>';
            continue;
        }
        if(empty($sheetData[$i][4])) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Kode Kab/Kota (DAPODIK) tidak boleh kosong</small>
                </td>
            </tr>';
            continue;
        }
        if(empty($sheetData[$i][5])) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Nama Kab/Kota tidak boleh kosong</small>
                </td>
            </tr>';
            continue;
        }
        if(empty($sheetData[$i][6])) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Kode Sekolah tidak boleh kosong</small>
                </td>
            </tr>';
            continue;
        }
        if(empty($sheetData[$i][7])) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Nama Sekolah tidak boleh kosong</small>
                </td>
            </tr>';
            continue;
        }
        if(empty($sheetData[$i][8])) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Kode Jabatan tidak boleh kosong</small>
                </td>
            </tr>';
            continue;
        }
        if(empty($sheetData[$i][9])) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Nama Jabatan tidak boleh kosong</small>
                </td>
            </tr>';
            continue;
        }

        //Buat Variabelnya
        $province_code          = mysqli_real_escape_string($Conn, $sheetData[$i][0]);
        $province_code_dapodik  = mysqli_real_escape_string($Conn, $sheetData[$i][1]);
        $province_name          = mysqli_real_escape_string($Conn, $sheetData[$i][2]);
        $district_code          = mysqli_real_escape_string($Conn, $sheetData[$i][3]);
        $district_code_dapodik  = mysqli_real_escape_string($Conn, $sheetData[$i][4]);
        $district_name          = mysqli_real_escape_string($Conn, $sheetData[$i][5]);
        $school_code            = mysqli_real_escape_string($Conn, $sheetData[$i][6]);
        $school_name            = mysqli_real_escape_string($Conn, $sheetData[$i][7]);
        $position_code          = mysqli_real_escape_string($Conn, $sheetData[$i][8]);
        $position_name          = mysqli_real_escape_string($Conn, $sheetData[$i][9]);

        //Buat variabel data yang tidak wajib
        $abk                    = !empty($sheetData[$i][10]) ? (int)$sheetData[$i][10] : 0;
        $asn                    = !empty($sheetData[$i][11]) ? (int)$sheetData[$i][11] : 0;
        $PPPK2024               = !empty($sheetData[$i][12]) ? (int)$sheetData[$i][12] : 0;
        $NonASN_sblmOkt2022     = !empty($sheetData[$i][13]) ? (int)$sheetData[$i][13] : 0;
        $NonASN_stlhOkt2022     = !empty($sheetData[$i][14]) ? (int)$sheetData[$i][14] : 0;
        $JmlGuru                = !empty($sheetData[$i][15]) ? (int)$sheetData[$i][15] : 0;
        $KurangGuru             = !empty($sheetData[$i][16]) ? (int)$sheetData[$i][16] : 0;
        $JmlASN                 = !empty($sheetData[$i][17]) ? (int)$sheetData[$i][17] : 0;
        $KrngASN                = !empty($sheetData[$i][18]) ? (int)$sheetData[$i][18] : 0;

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
                    <td>'.$sheetData[$i][2].'</td>
                    <td colspan="4" class="text-right">
                        <small class="text-success">Data Provinsi Baru Berhasil Disimpan</small>
                    </td>
                </tr>';
            } else {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td colspan="4" class="text-right">
                        <small class="text-danger">Data Provinsi Baru Gagal Disimpan: '.mysqli_error($Conn).'</small>
                    </td>
                </tr>';
            }
        }

        // PROSES KABUPATEN/KOTA
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
                    <td>'.$sheetData[$i][2].'</td>
                    <td>'.$sheetData[$i][5].'</td>
                    <td colspan="3" class="text-right">
                        <small class="text-success">Data Kab/Kota Baru Berhasil Disimpan</small>
                    </td>
                </tr>';
            } else {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td>'.$sheetData[$i][5].'</td>
                    <td colspan="3" class="text-right">
                        <small class="text-danger">Data Kab/Kota Baru Gagal Disimpan: '.mysqli_error($Conn).'</small>
                    </td>
                </tr>';
            }
        }

        // PROSES SEKOLAH
        //Cek Apakah school_code sudah terdaftar
        $id_school = GetDetailData($Conn, 'school', 'npsn', $school_code, 'id_school');

        //Jika Belum Terdaftar Insert school
        if(empty($id_school)) {

            //id_region dari district_code
            $id_region = GetDetailData($Conn, 'region', 'district_code', $district_code, 'id_region');

            //school_level dari school_name
            $parts          = explode(" ", trim($school_name));
            $school_level   = strtoupper($parts[0]);

            //Insert School
            $EntrySchool = "INSERT INTO school (id_region, npsn, school_name, school_level) 
                VALUES 
            ('$id_region', '$school_code', '$school_name', '$school_level')";
            $InputSchool = mysqli_query($Conn, $EntrySchool);
            
            if($InputSchool) {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td>'.$sheetData[$i][5].'</td>
                    <td>'.$school_name.'</td>
                    <td colspan="2" class="text-right">
                        <small class="text-success">Data Sekolah Baru Berhasil Disimpan</small>
                    </td>
                </tr>';
            } else {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td>'.$sheetData[$i][5].'</td>
                    <td>'.$school_name.'</td>
                    <td colspan="2" class="text-right">
                        <small class="text-danger">Data Sekolah Baru Gagal Disimpan: '.mysqli_error($Conn).'</small>
                    </td>
                </tr>';
            }
        }
        //Jika Berhasil Buka Ulang id_school
        $id_school = GetDetailData($Conn, 'school', 'npsn', $school_code, 'id_school');

        //PROSES JABATAN
        //Cek Apakah position_code sudah terdaftar
        $id_position = GetDetailData($Conn, 'position', 'position_code', $position_code, 'id_position');

        //Jika Belum Terdaftar Insert position
        if(empty($id_position)) {

            //Insert position
            $EntryPosition = "INSERT INTO position (position_code, position_name) VALUES ('$position_code', '$position_name')";
            $InputPosition = mysqli_query($Conn, $EntryPosition);
            
            if($InputPosition) {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td>'.$sheetData[$i][5].'</td>
                    <td>'.$school_name.'</td>
                    <td>'.$position_name.'</td>
                    <td class="text-right">
                        <small class="text-success">Data Jabatan Baru Berhasil Disimpan</small>
                    </td>
                </tr>';
            } else {
                $html_output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$sheetData[$i][2].'</td>
                    <td>'.$sheetData[$i][5].'</td>
                    <td>'.$school_name.'</td>
                    <td>'.$position_name.'</td>
                    <td class="text-right">
                        <small class="text-danger">Data Jabatan Baru Gagal Disimpan: '.mysqli_error($Conn).'</small>
                    </td>
                </tr>';
            }
        }
        //Jika Berhasil Buka Ulang id_position
        $id_position = GetDetailData($Conn, 'position', 'position_code', $position_code, 'id_position');
        
        
        // Validasi Duplikasi Data Berdasarkan id_school dan id_position
        $Qry = $Conn->prepare("SELECT * FROM position_school WHERE id_school = ? AND id_position = ?");
        $Qry->bind_param("ii", $id_school, $id_position);

        //Jika Terjadi kesalahan pada saat cek duplikasi
        if (!$Qry->execute()) {
            $error=$Conn->error;
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td>'.$sheetData[$i][2].'</td>
                <td>'.$sheetData[$i][5].'</td>
                <td>'.$school_name.'</td>
                <td>'.$position_name.'</td>
                <td class="text-right">
                    <small class="text-danger">Terjadi kesalahan : '.$error.'</small>
                </td>
            </tr>';
        }else{
            $Result = $Qry->get_result();
            $Data = $Result->fetch_assoc();
            $Qry->close();

            if(!empty($Data['id_position_school'])){

                //Buat Variabel
                $id_position_school     = $Data['id_position_school'];

                // Jika Sudah Ada Update position_school
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
                
                if (!$stmt) {
                    $html_output .= '
                    <tr>
                        <td>'.$i.'</td>
                        <td>'.$sheetData[$i][2].'</td>
                        <td>'.$sheetData[$i][5].'</td>
                        <td>'.$school_name.'</td>
                        <td>'.$position_name.'</td>
                        <td class="text-right">
                            <small class="text-danger">Prepare gagal: '.htmlspecialchars($Conn->error).'</small>
                        </td>
                    </tr>';
                    continue;
                }
                
                $stmt->bind_param("iiiiiiiiii", $abk, $asn, $PPPK2024, $NonASN_sblmOkt2022, $NonASN_stlhOkt2022, $JmlGuru, $KurangGuru, $JmlASN, $KrngASN, $id_position_school);
                $Input = $stmt->execute();
                $stmt->close();
                
                if ($Input) {
                    $html_output .= '
                    <tr>
                        <td>'.$i.'</td>
                        <td>'.$sheetData[$i][2].'</td>
                        <td>'.$sheetData[$i][5].'</td>
                        <td>'.$school_name.'</td>
                        <td>'.$position_name.'</td>
                        <td class="text-center">
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
                        <td>'.$school_name.'</td>
                        <td>'.$position_name.'</td>
                        <td class="text-center">
                            <small class="text-danger">Update Gagal: '.htmlspecialchars($Conn->error).'</small>
                        </td>
                    </tr>';
                }
            } else {
                // Jika Belum Ada Lakukan Insert
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
                if($insert->execute()){
                    $html_output .= '
                    <tr>
                        <td>'.$i.'</td>
                        <td>'.$sheetData[$i][2].'</td>
                        <td>'.$sheetData[$i][5].'</td>
                        <td>'.$school_name.'</td>
                        <td>'.$position_name.'</td>
                        <td class="text-center">
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
                        <td>'.$school_name.'</td>
                        <td>'.$position_name.'</td>
                        <td class="text-center">
                            <small class="text-danger">Insert Gagal</small>
                        </td>
                    </tr>';
                }
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
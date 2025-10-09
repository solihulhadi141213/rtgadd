<?php
    // ProsesImportBatch.php (Diperbaiki penuh)
    // Memproses satu batch dari CSV yang sudah disimpan di _Temp

    include_once "../../_Config/Connection.php";
    include_once "../../_Config/GlobalFunction.php";
    include_once "../../_Config/Session.php"; // pastikan ini mengatur $SessionIdAccess

    date_default_timezone_set('Asia/Jakarta');

    // Supaya error tidak merusak JSON output — log saja
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', '../../php_errors.log');

    header('Content-Type: application/json; charset=utf-8');

    // Pastikan session aktif
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Batasi resources untuk tiap batch
    @ini_set('memory_limit', '256M');
    @ini_set('max_execution_time', 120);

    // validasi akses
    if (empty($SessionIdAccess)) {
        echo json_encode(['status' => 'error', 'message' => 'Sesi Akses Sudah Berakhir! Silahkan Login Ulang.']);
        exit;
    }

    // validasi parameter input
    $file_token = $_POST['file_token'] ?? '';
    $current_batch = isset($_POST['batch']) ? intval($_POST['batch']) : 0;
    $total_batches = isset($_POST['total_batches']) ? intval($_POST['total_batches']) : 0;

    if (empty($file_token) || $current_batch < 1 || $total_batches < 1) {
        echo json_encode(['status' => 'error', 'message' => 'Parameter tidak valid']);
        exit;
    }

    // ambil metadata dari session
    $session_key = 'import_metadata_' . $file_token;
    if (!isset($_SESSION[$session_key])) {
        echo json_encode(['status' => 'error', 'message' => 'Data import tidak ditemukan. Silahkan upload ulang file.']);
        exit;
    }
    $metadata = $_SESSION[$session_key];
    $csv_file_path = $metadata['file_path'] ?? '';
    $total_rows = intval($metadata['total_rows'] ?? 0);
    $original_name = $metadata['original_name'] ?? 'file.csv';

    // validasi file temporary
    if (!file_exists($csv_file_path)) {
        echo json_encode(['status' => 'error', 'message' => 'File temporary tidak ditemukan']);
        exit;
    }

    // ambil batch_size dari metadata jika ada, fallback 100
    $batch_size = intval($metadata['batch_size'] ?? 100);
    if ($batch_size <= 0) $batch_size = 100;

    // hitung start/end (data rows only, zero-based)
    $start_row = ($current_batch - 1) * $batch_size;       // zero-based index for first data row in this batch
    $end_row_exclusive = min($start_row + $batch_size, $total_rows); // exclusive upper bound (zero-based count of data rows)

    // helper: detect delimiter by sampling few lines
    function detect_csv_delimiter(string $filePath, int $sampleLines = 5): string {
        $candidates = [",", ";", "\t", "|"];
        $counts = array_fill_keys($candidates, 0);

        $handle = fopen($filePath, "r");
        if (!$handle) return ","; // fallback

        $i = 0;
        while (($line = fgets($handle)) !== false && $i < $sampleLines) {
            // skip empty lines
            if (trim($line) === '') { $i++; continue; }
            foreach ($candidates as $d) {
                $fields = str_getcsv($line, $d);
                // prefer delimiter that gives more fields (simple heuristic)
                $counts[$d] = max($counts[$d], count($fields));
            }
            $i++;
        }
        fclose($handle);

        // choose delimiter with max fields
        arsort($counts);
        $best = key($counts);
        return $best ?: ",";
    }

    $html_output = '';
    $JumlahKodeValid = 0;
    $processed_in_batch = 0;

    try {
        // detect delimiter first
        $delimiter = detect_csv_delimiter($csv_file_path, 8);

        // gunakan SplFileObject untuk seek ke baris yang diperlukan
        $file = new SplFileObject($csv_file_path);
        // jangan set SKIP_EMPTY karena kita butuh index baris konsisten
        $file->setFlags(SplFileObject::READ_CSV);
        $file->setCsvControl($delimiter);

        // Pastikan header ada di index 0, baca header dan hapus BOM jika ada
        $file->rewind();
        $header = $file->current();
        if (is_array($header) && isset($header[0])) {
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
        }

        // Seek ke baris data pertama untuk batch ini.
        // Data rows start at index 1 (header at 0). So we want file index = start_row + 1
        $seekIndex = $start_row + 1;
        $file->seek($seekIndex);

        // jumlah baris yang harus diproses pada batch ini
        $toProcess = $end_row_exclusive - $start_row;
        if ($toProcess < 0) $toProcess = 0;

        $processed_in_batch = 0;

        while (!$file->eof() && $processed_in_batch < $toProcess) {
            $row = $file->current(); // array atau null
            $lineIndex = $file->key(); // actual file index (0-based)
            $file->next(); // pindah pointer ke baris berikutnya (penting untuk SplFileObject)

            // jika row bukan array (misal empty line), normalisasi jadi array kosong
            if ($row === null || $row === false) {
                $processed_in_batch++;
                // tampilkan baris kosong dilewati (opsional)
                $html_output .= '
                <tr>
                    <td>'.($lineIndex + 1).'</td>
                    <td colspan="6" class="text-center">
                        <small class="text-warning">Baris kosong - dilewati</small>
                    </td>
                </tr>';
                continue;
            }

            // Normalisasi row => pastikan array dan padding supaya index aman
            if (!is_array($row)) $row = [$row];
            // convert null -> ''
            $row = array_map(function($v){ return ($v === null ? '' : $v); }, $row);
            // pad minimal 20 kolom (sesuaikan bila CSV punya lebih sedikit)
            $row = array_pad($row, 20, '');

            // sanitize BOM for first cell
            if (isset($row[0])) $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]);

            $row_number = $lineIndex + 1; // human readable line number (1-based)

            // Cek baris kosong di semua kolom utama (0..9)
            $allEmpty = true;
            for ($k = 0; $k <= 9; $k++) {
                if (isset($row[$k]) && trim($row[$k]) !== '') { $allEmpty = false; break; }
            }
            if ($allEmpty) {
                $html_output .= '
                <tr>
                    <td>'.$row_number.'</td>
                    <td colspan="6" class="text-center">
                        <small class="text-warning">Baris kosong - dilewati</small>
                    </td>
                </tr>';
                $processed_in_batch++;
                continue;
            }

            // Validasi kolom wajib (0..9)
            $errors = [];
            if (trim($row[0]) === '') $errors[] = 'Kode provinsi (BPS) tidak boleh kosong';
            if (trim($row[1]) === '') $errors[] = 'Kode provinsi (DAPODIK) tidak boleh kosong';
            if (trim($row[2]) === '') $errors[] = 'Nama provinsi tidak boleh kosong';
            if (trim($row[3]) === '') $errors[] = 'Kode Kab/Kota (BPS) tidak boleh kosong';
            if (trim($row[4]) === '') $errors[] = 'Kode Kab/Kota (DAPODIK) tidak boleh kosong';
            if (trim($row[5]) === '') $errors[] = 'Nama Kab/Kota tidak boleh kosong';
            if (trim($row[6]) === '') $errors[] = 'Kode Sekolah tidak boleh kosong';
            if (trim($row[7]) === '') $errors[] = 'Nama Sekolah tidak boleh kosong';
            if (trim($row[8]) === '') $errors[] = 'Kode Jabatan tidak boleh kosong';
            if (trim($row[9]) === '') $errors[] = 'Nama Jabatan tidak boleh kosong';

            if (!empty($errors)) {
                $html_output .= '
                <tr>
                    <td>'.$row_number.'</td>
                    <td>'.(!empty($row[2]) ? htmlspecialchars($row[2]) : '-').'</td>
                    <td>'.(!empty($row[5]) ? htmlspecialchars($row[5]) : '-').'</td>
                    <td>'.(!empty($row[7]) ? htmlspecialchars($row[7]) : '-').'</td>
                    <td>'.(!empty($row[9]) ? htmlspecialchars($row[9]) : '-').'</td>
                    <td class="text-center">
                        <small class="text-danger">'.htmlspecialchars(implode(', ', $errors)).'</small>
                    </td>
                </tr>';
                $processed_in_batch++;
                continue;
            }

            // Ambil data dan sanitize untuk DB (gunakan mysqli_real_escape_string)
            $province_code          = mysqli_real_escape_string($Conn, trim($row[0]));
            $province_code_dapodik  = mysqli_real_escape_string($Conn, trim($row[1]));
            $province_name          = mysqli_real_escape_string($Conn, trim($row[2]));
            $district_code          = mysqli_real_escape_string($Conn, trim($row[3]));
            $district_code_dapodik  = mysqli_real_escape_string($Conn, trim($row[4]));
            $district_name          = mysqli_real_escape_string($Conn, trim($row[5]));
            $school_code            = mysqli_real_escape_string($Conn, trim($row[6]));
            $school_name            = mysqli_real_escape_string($Conn, trim($row[7]));
            $position_code          = mysqli_real_escape_string($Conn, trim($row[8]));
            $position_name          = mysqli_real_escape_string($Conn, trim($row[9]));

            // kolom optional (pastikan integer)
            $abk                    = isset($row[10]) ? (int)trim($row[10]) : 0;
            $asn                    = isset($row[11]) ? (int)trim($row[11]) : 0;
            $PPPK2024               = isset($row[12]) ? (int)trim($row[12]) : 0;
            $NonASN_sblmOkt2022     = isset($row[13]) ? (int)trim($row[13]) : 0;
            $NonASN_stlhOkt2022     = isset($row[14]) ? (int)trim($row[14]) : 0;
            $JmlGuru                = isset($row[15]) ? (int)trim($row[15]) : 0;
            $KurangGuru             = isset($row[16]) ? (int)trim($row[16]) : 0;
            $JmlASN                 = isset($row[17]) ? (int)trim($row[17]) : 0;
            $KrngASN                = isset($row[18]) ? (int)trim($row[18]) : 0;

            // ------- PROSES LOGIKA INSERT/UPDATE --------
            $code_map = '';

            // PROSES PROVINSI
            $id_region_province = null;
            if (!empty($province_code)) {
                $id_region_province = GetDetailData($Conn, 'region', 'province_code', $province_code, 'id_region');
            }
            if (empty($id_region_province) && !empty($province_code_dapodik)) {
                $id_region_province = GetDetailData($Conn, 'region', 'province_code_dapodik', $province_code_dapodik, 'id_region');
            }
            if (empty($id_region_province)) {
                // insert province
                $category = "Province";
                $EntryProvince = "INSERT INTO region 
                    (category, province_code, province_code_dapodik, province_name, district_code, district_code_dapodik, district_name, code_map) 
                    VALUES 
                    ('$category', '$province_code', '$province_code_dapodik', '$province_name', '', '', '', '$code_map')";
                $InputProvince = mysqli_query($Conn, $EntryProvince);
                if ($InputProvince) {
                    $html_output .= '
                    <tr>
                        <td>'.$row_number.'</td>
                        <td>'.htmlspecialchars($province_name).'</td>
                        <td colspan="4" class="text-right">
                            <small class="text-success">Data Provinsi Baru Berhasil Disimpan</small>
                        </td>
                    </tr>';
                } else {
                    // log error, tampilkan ringkasan
                    error_log("Insert Province failed: " . mysqli_error($Conn));
                    $html_output .= '
                    <tr>
                        <td>'.$row_number.'</td>
                        <td>'.htmlspecialchars($province_name).'</td>
                        <td colspan="4" class="text-right">
                            <small class="text-danger">Data Provinsi Baru Gagal Disimpan</small>
                        </td>
                    </tr>';
                }
            }

            // PROSES KABUPATEN/KOTA
            $id_region_district = null;
            if (!empty($district_code)) {
                $id_region_district = GetDetailData($Conn, 'region', 'district_code', $district_code, 'id_region');
            }
            if (empty($id_region_district) && !empty($district_code_dapodik)) {
                $id_region_district = GetDetailData($Conn, 'region', 'district_code_dapodik', $district_code_dapodik, 'id_region');
            }
            if (empty($id_region_district)) {
                $category = "District";
                $EntryDistrict = "INSERT INTO region 
                    (category, province_code, province_code_dapodik, province_name, district_code, district_code_dapodik, district_name, code_map) 
                    VALUES 
                    ('$category', '$province_code', '$province_code_dapodik', '$province_name', '$district_code', '$district_code_dapodik', '$district_name', '$code_map')";
                $InputDistrict = mysqli_query($Conn, $EntryDistrict);
                if ($InputDistrict) {
                    $html_output .= '
                    <tr>
                        <td>'.$row_number.'</td>
                        <td>'.htmlspecialchars($province_name).'</td>
                        <td>'.htmlspecialchars($district_name).'</td>
                        <td colspan="3" class="text-right">
                            <small class="text-success">Data Kab/Kota Baru Berhasil Disimpan</small>
                        </td>
                    </tr>';
                } else {
                    error_log("Insert District failed: " . mysqli_error($Conn));
                    $html_output .= '
                    <tr>
                        <td>'.$row_number.'</td>
                        <td>'.htmlspecialchars($province_name).'</td>
                        <td>'.htmlspecialchars($district_name).'</td>
                        <td colspan="3" class="text-right">
                            <small class="text-danger">Data Kab/Kota Baru Gagal Disimpan</small>
                        </td>
                    </tr>';
                }
            }

            // PROSES SEKOLAH
            $id_school = GetDetailData($Conn, 'school', 'npsn', $school_code, 'id_school');
            if (empty($id_school)) {
                // dapatkan id_region dari district_code
                $id_region = GetDetailData($Conn, 'region', 'district_code', $district_code, 'id_region');
                // school_level dari kata pertama nama sekolah
                $parts = preg_split('/\s+/', trim($school_name));
                $school_level = strtoupper($parts[0] ?? '');
                $EntrySchool = "INSERT INTO school (id_region, npsn, school_name, school_level) 
                    VALUES 
                    ('$id_region', '$school_code', '$school_name', '$school_level')";
                $InputSchool = mysqli_query($Conn, $EntrySchool);
                if ($InputSchool) {
                    $html_output .= '
                    <tr>
                        <td>'.$row_number.'</td>
                        <td>'.htmlspecialchars($province_name).'</td>
                        <td>'.htmlspecialchars($district_name).'</td>
                        <td>'.htmlspecialchars($school_name).'</td>
                        <td colspan="2" class="text-right">
                            <small class="text-success">Data Sekolah Baru Berhasil Disimpan</small>
                        </td>
                    </tr>';
                } else {
                    error_log("Insert School failed: " . mysqli_error($Conn));
                    $html_output .= '
                    <tr>
                        <td>'.$row_number.'</td>
                        <td>'.htmlspecialchars($province_name).'</td>
                        <td>'.htmlspecialchars($district_name).'</td>
                        <td>'.htmlspecialchars($school_name).'</td>
                        <td colspan="2" class="text-right">
                            <small class="text-danger">Data Sekolah Baru Gagal Disimpan</small>
                        </td>
                    </tr>';
                }
            }
            // reload id_school
            $id_school = GetDetailData($Conn, 'school', 'npsn', $school_code, 'id_school');

            // PROSES JABATAN
            $id_position = GetDetailData($Conn, 'position', 'position_code', $position_code, 'id_position');
            if (empty($id_position)) {
                $EntryPosition = "INSERT INTO position (position_code, position_name) VALUES ('$position_code', '$position_name')";
                $InputPosition = mysqli_query($Conn, $EntryPosition);
                if ($InputPosition) {
                    $html_output .= '
                    <tr>
                        <td>'.$row_number.'</td>
                        <td>'.htmlspecialchars($province_name).'</td>
                        <td>'.htmlspecialchars($district_name).'</td>
                        <td>'.htmlspecialchars($school_name).'</td>
                        <td>'.htmlspecialchars($position_name).'</td>
                        <td class="text-right">
                            <small class="text-success">Data Jabatan Baru Berhasil Disimpan</small>
                        </td>
                    </tr>';
                } else {
                    error_log("Insert Position failed: " . mysqli_error($Conn));
                    $html_output .= '
                    <tr>
                        <td>'.$row_number.'</td>
                        <td>'.htmlspecialchars($province_name).'</td>
                        <td>'.htmlspecialchars($district_name).'</td>
                        <td>'.htmlspecialchars($school_name).'</td>
                        <td>'.htmlspecialchars($position_name).'</td>
                        <td class="text-right">
                            <small class="text-danger">Data Jabatan Baru Gagal Disimpan</small>
                        </td>
                    </tr>';
                }
            }
            $id_position = GetDetailData($Conn, 'position', 'position_code', $position_code, 'id_position');

            // Validasi / Upsert position_school
            $Qry = $Conn->prepare("SELECT id_position_school FROM position_school WHERE id_school = ? AND id_position = ?");
            if ($Qry === false) {
                error_log("Prepare select position_school failed: " . $Conn->error);
                $html_output .= '
                <tr>
                    <td>'.$row_number.'</td>
                    <td>'.htmlspecialchars($province_name).'</td>
                    <td>'.htmlspecialchars($district_name).'</td>
                    <td>'.htmlspecialchars($school_name).'</td>
                    <td>'.htmlspecialchars($position_name).'</td>
                    <td class="text-right">
                        <small class="text-danger">Prepare gagal</small>
                    </td>
                </tr>';
                $processed_in_batch++;
                continue;
            }
            $Qry->bind_param("ii", $id_school, $id_position);
            if (!$Qry->execute()) {
                error_log("Execute select position_school failed: " . $Conn->error);
                $html_output .= '
                <tr>
                    <td>'.$row_number.'</td>
                    <td>'.htmlspecialchars($province_name).'</td>
                    <td>'.htmlspecialchars($district_name).'</td>
                    <td>'.htmlspecialchars($school_name).'</td>
                    <td>'.htmlspecialchars($position_name).'</td>
                    <td class="text-right">
                        <small class="text-danger">Terjadi kesalahan saat cek duplikasi</small>
                    </td>
                </tr>';
                $Qry->close();
                $processed_in_batch++;
                continue;
            }
            $Result = $Qry->get_result();
            $Data = $Result ? $Result->fetch_assoc() : null;
            $Qry->close();

            if (!empty($Data['id_position_school'])) {
                // update existing
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
                    error_log("Prepare update failed: " . $Conn->error);
                    $html_output .= '
                    <tr>
                        <td>'.$row_number.'</td>
                        <td>'.htmlspecialchars($province_name).'</td>
                        <td>'.htmlspecialchars($district_name).'</td>
                        <td>'.htmlspecialchars($school_name).'</td>
                        <td>'.htmlspecialchars($position_name).'</td>
                        <td class="text-right">
                            <small class="text-danger">Prepare gagal update</small>
                        </td>
                    </tr>';
                    $processed_in_batch++;
                    continue;
                }
                $stmt->bind_param("iiiiiiiiii", $abk, $asn, $PPPK2024, $NonASN_sblmOkt2022, $NonASN_stlhOkt2022, $JmlGuru, $KurangGuru, $JmlASN, $KrngASN, $id_position_school);
                $ok = $stmt->execute();
                if ($ok) {
                    $JumlahKodeValid++;
                    $html_output .= '
                    <tr>
                        <td>'.$row_number.'</td>
                        <td>'.htmlspecialchars($province_name).'</td>
                        <td>'.htmlspecialchars($district_name).'</td>
                        <td>'.htmlspecialchars($school_name).'</td>
                        <td>'.htmlspecialchars($position_name).'</td>
                        <td class="text-center">
                            <small class="text-success">Update Berhasil</small>
                        </td>
                    </tr>';
                } else {
                    error_log("Execute update failed: " . $stmt->error);
                    $html_output .= '
                    <tr>
                        <td>'.$row_number.'</td>
                        <td>'.htmlspecialchars($province_name).'</td>
                        <td>'.htmlspecialchars($district_name).'</td>
                        <td>'.htmlspecialchars($school_name).'</td>
                        <td>'.htmlspecialchars($position_name).'</td>
                        <td class="text-center">
                            <small class="text-danger">Update Gagal</small>
                        </td>
                    </tr>';
                }
                $stmt->close();
            } else {
                // insert new
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
                    error_log("Prepare insert failed: " . $Conn->error);
                    $html_output .= '
                    <tr>
                        <td>'.$row_number.'</td>
                        <td>'.htmlspecialchars($province_name).'</td>
                        <td>'.htmlspecialchars($district_name).'</td>
                        <td>'.htmlspecialchars($school_name).'</td>
                        <td>'.htmlspecialchars($position_name).'</td>
                        <td class="text-center">
                            <small class="text-danger">Prepare Insert gagal</small>
                        </td>
                    </tr>';
                    $processed_in_batch++;
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
                    $JumlahKodeValid++;
                    $html_output .= '
                    <tr>
                        <td>'.$row_number.'</td>
                        <td>'.htmlspecialchars($province_name).'</td>
                        <td>'.htmlspecialchars($district_name).'</td>
                        <td>'.htmlspecialchars($school_name).'</td>
                        <td>'.htmlspecialchars($position_name).'</td>
                        <td class="text-center">
                            <small class="text-primary">Insert Berhasil</small>
                        </td>
                    </tr>';
                } else {
                    error_log("Execute insert failed: " . $insert->error);
                    $html_output .= '
                    <tr>
                        <td>'.$row_number.'</td>
                        <td>'.htmlspecialchars($province_name).'</td>
                        <td>'.htmlspecialchars($district_name).'</td>
                        <td>'.htmlspecialchars($school_name).'</td>
                        <td>'.htmlspecialchars($position_name).'</td>
                        <td class="text-center">
                            <small class="text-danger">Insert Gagal</small>
                        </td>
                    </tr>';
                }
                $insert->close();
            }

            $processed_in_batch++;
        }

        // unset file object
        unset($file);

    } catch (Exception $e) {
        error_log("Exception in ProsesImportBatch: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Exception: ' . $e->getMessage()]);
        exit;
    }

    // Jika ini batch terakhir bersihkan file & session
    if ($current_batch >= $total_batches) {
        if (file_exists($csv_file_path)) {
            @unlink($csv_file_path);
        }
        unset($_SESSION[$session_key]);
    }

    // Tambahkan info ringkasan batch
    $html_output .= '
    <tr>
        <td colspan="6" class="text-center">
            <small class="text-info">Batch ' . $current_batch . ' dari ' . $total_batches . ' selesai. ' . $JumlahKodeValid . ' data berhasil diproses dari ' . $processed_in_batch . ' baris.</small>
        </td>
    </tr>';

    // Response JSON
    echo json_encode([
        'status' => 'success',
        'html' => $html_output,
        'batch' => $current_batch,
        'total_batches' => $total_batches,
        'processed' => $processed_in_batch,
        'successful' => $JumlahKodeValid,
        'total_rows' => $total_rows
    ], JSON_UNESCAPED_UNICODE);
    exit;

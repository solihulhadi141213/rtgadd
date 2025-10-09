<?php
    // ProsesImport.php
    // Menerima upload CSV, hitung baris, simpan file ke _Temp, simpan metadata ke session

    // Inklusi konfigurasi
    include_once "../../_Config/Connection.php";
    include_once "../../_Config/GlobalFunction.php";
    include_once "../../_Config/Session.php"; // pastikan ini mengatur $SessionIdAccess

    // Time Zone
    date_default_timezone_set('Asia/Jakarta');

    // Supaya error tidak merusak JSON output — log saja
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', '../../php_errors.log');

    // Header
    header('Content-Type: application/json; charset=utf-8');

    // Pastikan session aktif
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Tingkatkan resource sementara (sesuaikan server)
    @ini_set('memory_limit', '1024M');
    @ini_set('max_execution_time', 300);

    // Validasi akses
    if (empty($SessionIdAccess)) {
        echo json_encode(['status' => 'error', 'message' => 'Sesi Akses Sudah Berakhir! Silahkan Login Ulang.']);
        exit;
    }

    // Validasi file
    if (empty($_FILES['data_akb_per_sekolah']['name'])) {
        echo json_encode(['status' => 'error', 'message' => 'Silahkan pilih file untuk di upload']);
        exit;
    }

    $nama_file = $_FILES['data_akb_per_sekolah']['name'];
    $file_extension = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

    // hanya csv
    if ($file_extension !== 'csv') {
        echo json_encode(['status' => 'error', 'message' => 'Hanya file CSV yang diizinkan']);
        exit;
    }

    // tmp file
    $tmp_file = $_FILES['data_akb_per_sekolah']['tmp_name'];
    if (!file_exists($tmp_file)) {
        echo json_encode(['status' => 'error', 'message' => 'File tidak ditemukan (temporary)']);
        exit;
    }

    // Hitung total baris menggunakan SplFileObject (fast)
    try {
        $spl = new SplFileObject($tmp_file);
        $spl->setFlags(SplFileObject::READ_CSV);
        // pindah ke akhir dan ambil index baris terakhir
        $spl->seek(PHP_INT_MAX);
        $lastIndex = $spl->key(); // ini index baris terakhir (0-based)
        // Jika file hanya header, lastIndex mungkin 0 -> berarti 1 baris (header) -> tanpa data
        $totalRows = 0;
        if ($lastIndex > 0) {
            // total data = lastIndex (karena index 0 header)
            $totalRows = $lastIndex;
        } else {
            // check manual fallback
            $totalRows = 0;
        }
    } catch (Exception $e) {
        // fallback ke counting manual (lebih lambat)
        $totalRows = 0;
        if (($handle = fopen($tmp_file, "r")) !== false) {
            // skip header
            fgetcsv($handle);
            while (($row = fgetcsv($handle)) !== false) {
                $totalRows++;
            }
            fclose($handle);
        }
    }

    // validasi ada data
    if ($totalRows < 1) {
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada data pada file CSV yang anda upload']);
        exit;
    }

    // generate token & simpan file ke _Temp
    $file_token = uniqid('import_', true);
    $temp_dir = "../../_Temp";
    if (!is_dir($temp_dir)) {
        if (!mkdir($temp_dir, 0755, true)) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal membuat folder temporary']);
            exit;
        }
    }

    $temp_csv_path = $temp_dir . "/" . $file_token . ".csv";

    // gunakan move_uploaded_file untuk keamanan
    if (!move_uploaded_file($tmp_file, $temp_csv_path)) {
        // fallback copy jika move gagal
        if (!copy($tmp_file, $temp_csv_path)) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file temporary']);
            exit;
        }
    }

    // simpan metadata ke session (jangan simpan seluruh isi file)
    $_SESSION['import_metadata_' . $file_token] = [
        'file_path' => $temp_csv_path,
        'total_rows' => $totalRows,
        'original_name' => $nama_file,
        'created_at' => date('Y-m-d H:i:s')
    ];

    // Tuliskan session ke disk agar tidak terkunci
    session_write_close();

    // tentukan batch size adaptif
    $batch_size_default = 100; // bawaan
    // jika sangat besar, naikkan sedikit (sesuaikan memory server)
    if ($totalRows > 200000) {
        $batch_size = 1000;
    } elseif ($totalRows > 50000) {
        $batch_size = 500;
    } elseif ($totalRows > 10000) {
        $batch_size = 200;
    } else {
        $batch_size = $batch_size_default;
    }

    $total_batches = (int)ceil($totalRows / $batch_size);

    // kirimkan response
    echo json_encode([
        'status' => 'success',
        'file_token' => $file_token,
        'total_batches' => $total_batches,
        'total_rows' => $totalRows,
        'batch_size' => $batch_size,
        'message' => 'Data terdeteksi: ' . number_format($totalRows) . ' baris. Akan diproses dalam ' . $total_batches . ' batch.'
    ], JSON_UNESCAPED_UNICODE);
    exit;

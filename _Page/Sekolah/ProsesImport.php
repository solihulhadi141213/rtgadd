<?php
require '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";

// Time Zone
date_default_timezone_set('Asia/Jakarta');

// Validasi Akses
if (empty($SessionIdAccess)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Sesi Akses Sudah Berakhir! Silahkan Login Ulang.'
    ]);
    exit;
}

// Validasi File
if(empty($_FILES['data_sekolah']['name'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Silahkan pilih file untuk di upload'
    ]);
    exit;
}

$nama_file = $_FILES['data_sekolah']['name'];
$file_mimes = array(
    'application/octet-stream',
    'application/vnd.ms-excel',
    'application/x-csv',
    'text/x-csv',
    'text/csv',
    'application/csv',
    'application/excel',
    'application/vnd.msexcel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.oasis.opendocument.spreadsheet'
);

if(isset($_FILES['data_sekolah']['name']) && in_array($_FILES['data_sekolah']['type'], $file_mimes)) {
    $arr_file = explode('.', $_FILES['data_sekolah']['name']);
    $extension = end($arr_file);
    
    if('csv' == $extension) {
        $reader = new Csv();
    } else {
        $reader = new Xlsx();
    }

    // Mengatasi deprecated function dengan menonaktifkan entity loader secara kondisional
    if (PHP_VERSION_ID < 80000) {
        $entityLoaderDisabled = libxml_disable_entity_loader(true);
    }

    try {
        $spreadsheet = $reader->load($_FILES['data_sekolah']['tmp_name']);
        
        // Mengembalikan entity loader ke keadaan semula untuk PHP < 8.0
        if (PHP_VERSION_ID < 80000) {
            libxml_disable_entity_loader($entityLoaderDisabled);
        }

        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        $JumlahBaris = count($sheetData);
        $JumlahValidator = $JumlahBaris - 1;

        if(empty($JumlahValidator)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Tidak ada data pada file excel yang anda upload'
            ]);
            exit;
        }

        // Generate unique token untuk file
        $file_token = uniqid('import_', true);
        
        // Simpan data ke session atau temporary file
        $_SESSION['import_data_' . $file_token] = $sheetData;
        $_SESSION['import_total_rows_' . $file_token] = $JumlahValidator;
        
        // Hitung jumlah batch yang diperlukan
        $batch_size = 100;
        $total_batches = ceil($JumlahValidator / $batch_size);

        if ($total_batches == 1) {
            // Jika data kecil, proses langsung
            require 'ProsesImportBatch.php';
        } else {
            // Jika data besar, return info untuk proses batch
            echo json_encode([
                'status' => 'success',
                'file_token' => $file_token,
                'total_batches' => $total_batches,
                'total_rows' => $JumlahValidator,
                'message' => 'Data besar terdeteksi. Akan diproses dalam ' . $total_batches . ' batch.'
            ]);
        }

    } catch (Exception $e) {
        // Mengembalikan entity loader ke keadaan semula untuk PHP < 8.0 jika terjadi error
        if (PHP_VERSION_ID < 80000) {
            libxml_disable_entity_loader($entityLoaderDisabled);
        }
        
        echo json_encode([
            'status' => 'error',
            'message' => 'Error membaca file: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'File tidak valid. Silahkan upload file Excel atau CSV.'
    ]);
}
?>
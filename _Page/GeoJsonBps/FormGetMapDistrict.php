<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    date_default_timezone_set("Asia/Jakarta");

    //Validasi Sesi Akses
    if(empty($SessionIdAccess)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Sesi Akses Sudah Berakhir! Silahkan Login Ulang!'
        ]);
        exit;
    }

    //Tangkap data
    if(empty($_POST['district_code'])){
        echo json_encode([
            'status' => 'error',
            'message' => 'Tidak Ada Data Kab/Kota Yang Dikirim'
        ]);
        exit;
    }

    $district_codes = $_POST['district_code'];
    $total = count($district_codes);

    // Kembalikan response JSON dengan daftar district codes
    echo json_encode([
        'status' => 'success',
        'district_codes' => $district_codes,
        'total' => $total,
        'message' => 'Proses akan dimulai...'
    ]);
    exit;
?>
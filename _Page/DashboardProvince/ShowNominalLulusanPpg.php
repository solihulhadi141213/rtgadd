<?php
    // Inisiasi Type File
    header('Content-Type: application/json');

    // Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Validasi Sesi akses
    if (empty($SessionIdAccess)) {
        $response = [
            "code" => 201,
            "message" => "Sesi akses sudah berakhir. Silahkan <b>login</b> ulang!",
            "lulusan_ppg" => NULL
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }

    // Validasi province_code Tidak ada
    if(empty($_POST['province_code'])){
        $response = [
            "code" => 201,
            "message" => "Parameter <b>Kode Provinsi</b> tidak boleh kosong!",
            "lulusan_ppg" => NULL
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }

    // Sanitize input
    $province_code = validateAndSanitizeInput($_POST['province_code']);

    // Query tunggal untuk menghitung lulusan PPG
    $query = "SELECT COUNT(cg.id_calon_guru) as total_lulusan_ppg
              FROM calon_guru cg
              INNER JOIN region r ON cg.id_region = r.id_region
              WHERE r.category = 'District' 
                AND r.province_code = ?
                AND cg.ppg_blm_diangkat = 'Belum'";
    
    $stmt = mysqli_prepare($Conn, $query);
    
    if (!$stmt) {
        $response = [
            "code" => 500,
            "message" => "Error preparing query: " . mysqli_error($Conn),
            "lulusan_ppg" => NULL
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }
    
    mysqli_stmt_bind_param($stmt, "s", $province_code);
    
    if (!mysqli_stmt_execute($stmt)) {
        $response = [
            "code" => 500,
            "message" => "Error executing query: " . mysqli_stmt_error($stmt),
            "lulusan_ppg" => NULL
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
        mysqli_stmt_close($stmt);
        exit;
    }
    
    mysqli_stmt_bind_result($stmt, $total_lulusan_ppg);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Handle result
    $lulusan_ppg = $total_lulusan_ppg ?? 0;

    // Buat Response
    $response = [
        "code" => 200,
        "message" => "Success",
        "lulusan_ppg" => (int)$lulusan_ppg
    ];
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
?>
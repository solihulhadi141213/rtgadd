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
            "kebutuhan_guru" => NULL
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }

    // Validasi province_code Tidak ada
    if(empty($_POST['province_code'])){
        $response = [
            "code" => 201,
            "message" => "Parameter <b>Kode Provinsi</b> tidak boleh kosong!",
            "kebutuhan_guru" => NULL
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }

    // Sanitize input
    $province_code = validateAndSanitizeInput($_POST['province_code']);

    // Query tunggal untuk menghitung kebutuhan guru
    $query = "SELECT SUM(ps.KurangGuru) as total_kebutuhan_guru
              FROM position_school ps
              INNER JOIN school s ON ps.id_school = s.id_school
              INNER JOIN region r ON s.id_region = r.id_region
              WHERE r.category = 'District' AND r.province_code = ?";
    
    $stmt = mysqli_prepare($Conn, $query);
    
    if (!$stmt) {
        $response = [
            "code" => 500,
            "message" => "Error preparing query: " . mysqli_error($Conn),
            "kebutuhan_guru" => NULL
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }
    
    mysqli_stmt_bind_param($stmt, "s", $province_code);
    
    if (!mysqli_stmt_execute($stmt)) {
        $response = [
            "code" => 500,
            "message" => "Error executing query: " . mysqli_stmt_error($stmt),
            "kebutuhan_guru" => NULL
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
        mysqli_stmt_close($stmt);
        exit;
    }
    
    mysqli_stmt_bind_result($stmt, $total_kebutuhan_guru);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Handle NULL result
    $kurang_guru = $total_kebutuhan_guru ?? 0;

    // Buat Response
    $response = [
        "code" => 200,
        "message" => "Success",
        "kebutuhan_guru" => (int)$kurang_guru
    ];
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
?>
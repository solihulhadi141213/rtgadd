<?php
    header('Content-Type: application/json');

    // Koneksi & Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Validasi Sesi
    if (empty($SessionIdAccess)) {
        echo json_encode([
            "code" => 201,
            "message" => "Sesi akses sudah berakhir. Silahkan <b>login</b> ulang!",
            "metadata" => []
        ], JSON_PRETTY_PRINT);
        exit;
    }

    $metadata = [];

    // Query tunggal dengan JOIN untuk menghindari nested loops
    $query = "
        SELECT 
            gr.province_code,
            gr.province_name,
            COALESCE(SUM(ps.abk), 0) as total_abk,
            COALESCE(SUM(ps.JmlGuru), 0) as total_jumlah_guru,
            COALESCE(SUM(ps.KurangGuru), 0) as total_kurang_guru,
            COALESCE(SUM(ps.KrngASN), 0) as total_kurang_asn
        FROM geo_region gr
        LEFT JOIN region r ON gr.province_code = r.province_code AND r.category = 'District'
        LEFT JOIN school s ON r.id_region = s.id_region
        LEFT JOIN position_school ps ON s.id_school = ps.id_school
        WHERE gr.level_region = 'Province'
        GROUP BY gr.province_code, gr.province_name
        ORDER BY gr.province_name
    ";

    $result = mysqli_query($Conn, $query);
    
    if (!$result) {
        echo json_encode([
            "code" => 500,
            "message" => "Error executing query: " . mysqli_error($Conn),
            "metadata" => []
        ], JSON_PRETTY_PRINT);
        exit;
    }

    while ($data = mysqli_fetch_assoc($result)) {
        $metadata[] = [
            "KODE_PROV"         => $data['province_code'],
            "PROVINSI"          => $data['province_name'],
            "ABK"               => (int)$data['total_abk'],
            "jumlah_guru"       => (int)$data['total_jumlah_guru'],
            "kurang_guru"       => (int)$data['total_kurang_guru'],
            "kurang_asn"        => (int)$data['total_kurang_asn']
        ];
    }

    // Output JSON
    echo json_encode([
        "code" => 200,
        "message" => "success",
        "metadata" => $metadata
    ], JSON_PRETTY_PRINT);
?>
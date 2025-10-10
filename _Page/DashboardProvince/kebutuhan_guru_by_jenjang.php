<?php
    header('Content-Type: application/json');
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    if (empty($SessionIdAccess)) {
        echo json_encode([
            "code" => 201,
            "message" => "Sesi akses sudah berakhir. Silahkan <b>login</b> ulang!"
        ]);
        exit;
    }

    if(empty($_POST['province_code'])){
        echo json_encode([
            "code" => 201,
            "message" => "Kode Provinsi Tidak Boleh Kosong!"
        ]);
        exit;
    }

    $province_code = mysqli_real_escape_string($Conn, $_POST['province_code']);
    $metadata = [];

    /**
     * Query optimasi:
     * - Ambil langsung kebutuhan guru per jenjang pendidikan
     * - JOIN region → school → position_school
     * - Filter province_code & category=District
     * - Group by jenjang (school_level)
     */
    $sql = "
        SELECT 
            s.school_level,
            COALESCE(SUM(p.KurangGuru), 0) AS kebutuhan_guru
        FROM school s
        INNER JOIN region r ON s.id_region = r.id_region
        LEFT JOIN position_school p ON s.id_school = p.id_school
        WHERE r.category = 'District' 
        AND r.province_code = '$province_code'
        GROUP BY s.school_level
        ORDER BY s.school_level ASC
    ";

    $result = mysqli_query($Conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $metadata[] = [
            "province_code"  => $province_code,
            "school_level"   => $row['school_level'],
            "kebutuhan_guru" => (int)$row['kebutuhan_guru']
        ];
    }

    echo json_encode([
        "code" => 200,
        "data" => $metadata
    ], JSON_PRETTY_PRINT);
?>

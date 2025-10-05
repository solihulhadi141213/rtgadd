<?php
header('Content-Type: application/json; charset=utf-8');

include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";

date_default_timezone_set('Asia/Jakarta');

$debug = [];

// Validasi session
if (empty($SessionIdAccess)) {
    $debug['session_valid'] = false;
    echo json_encode([
        "code" => 401,
        "message" => "Sesi Akses Sudah Berakhir! Silahkan Login Ulang!",
        "metadata" => [],
        "debug" => $debug
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
$debug['session_valid'] = true;

// Validasi district_code
if (empty($_POST['district_code'])) {
    $debug['received_post'] = $_POST;
    echo json_encode([
        "code" => 400,
        "message" => "ID Kab/Kota Tidak Boleh Kosong!",
        "metadata" => [],
        "debug" => $debug
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
$district_code = $_POST['district_code'];
$debug['district_code'] = $district_code;

// Ambil id_region dari tabel region
$id_region = GetDetailData($Conn, 'region', 'district_code', $district_code, 'id_region');
$debug['id_region_raw'] = $id_region;
$id_region = (int)$id_region;
if ($id_region <= 0) {
    $debug['note'] = 'id_region tidak ditemukan atau bernilai 0';
    echo json_encode([
        "code" => 404,
        "message" => "Region tidak ditemukan untuk district_code ini",
        "metadata" => [],
        "debug" => $debug
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
$debug['id_region'] = $id_region;

// Query agregasi per school_level (SUM dari position_school)
$sql = "
    SELECT 
        s.school_level,
        IFNULL(SUM(ps.abk),0) AS abk,
        IFNULL(SUM(ps.asn),0) AS asn,
        IFNULL(SUM(ps.PPPK2024),0) AS PPPK2024,
        IFNULL(SUM(ps.KurangGuru),0) AS KurangGuru,
        COUNT(DISTINCT s.id_school) AS total_schools
    FROM school s
    LEFT JOIN position_school ps ON ps.id_school = s.id_school
    WHERE s.id_region = ?
    GROUP BY s.school_level
    ORDER BY s.school_level
";

$metadata = [];
$totals = ['abk'=>0,'asn'=>0,'PPPK2024'=>0,'KurangGuru'=>0,'schools'=>0];

if ($stmt = $Conn->prepare($sql)) {
    $stmt->bind_param("i", $id_region);
    $stmt->execute();
    $res = $stmt->get_result();

    $debug['levels_found'] = $res->num_rows;

    while ($row = $res->fetch_assoc()) {
        $rowdata = [
            "school_level" => $row['school_level'],
            "abk" => (int)$row['abk'],
            "asn" => (int)$row['asn'],
            "PPPK2024" => (int)$row['PPPK2024'],
            "KurangGuru" => (int)$row['KurangGuru'],
            "total_schools" => (int)$row['total_schools']
        ];
        $metadata[] = $rowdata;

        $totals['abk'] += $rowdata['abk'];
        $totals['asn'] += $rowdata['asn'];
        $totals['PPPK2024'] += $rowdata['PPPK2024'];
        $totals['KurangGuru'] += $rowdata['KurangGuru'];
        $totals['schools'] += $rowdata['total_schools'];
    }

    $debug['totals_from_levels'] = $totals;

    if (count($metadata) === 0) {
        echo json_encode([
            "code" => 200,
            "message" => "Success - tidak ada data position pada region ini",
            "metadata" => [],
            "debug" => $debug
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode([
        "code" => 200,
        "message" => "Success",
        "metadata" => $metadata,
        "debug" => $debug
    ], JSON_UNESCAPED_UNICODE);
    exit;

} else {
    $debug['sql_error'] = $Conn->error;
    echo json_encode([
        "code" => 500,
        "message" => "Gagal menyiapkan query",
        "metadata" => [],
        "debug" => $debug
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

<?php
// Zona Waktu
date_default_timezone_set('Asia/Jakarta');

// Koneksi dan dependensi
include "../../_Config/Connection.php";
include "../../_Config/SettingGeneral.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";

// Validasi sesi akses
if (empty($SessionIdAccess)) {
    echo '
        <div class="alert alert-danger">
            <small>Sesi akses sudah berakhir. Silahkan <b>login</b> ulang!</small>
        </div>
    ';
    exit;
}

// Validasi district_code
if (empty($_POST['district_code'])) {
    echo '
        <div class="alert alert-danger">
            <small>Kode Kab/Kota tidak boleh kosong!</small>
        </div>
    ';
    exit;
}

// Sanitasi input
$district_code = validateAndSanitizeInput($_POST['district_code']);

// Query detail region dengan JOIN untuk menghindari multiple queries
$sql = "SELECT 
            r.*,
            COUNT(DISTINCT s.id_school) as total_sekolah,
            COALESCE(SUM(ps.abk), 0) as total_abk,
            COALESCE(SUM(ps.asn), 0) as total_asn,
            COALESCE(SUM(ps.PPPK2024), 0) as total_pppk2024,
            COALESCE(SUM(ps.NonASN_sblmOkt2022), 0) as total_nonasn_sebelum,
            COALESCE(SUM(ps.NonASN_stlhOkt2022), 0) as total_nonasn_setelah,
            COALESCE(SUM(ps.JmlGuru), 0) as total_jml_guru,
            COALESCE(SUM(ps.KurangGuru), 0) as total_kurang_guru,
            COALESCE(SUM(ps.JmlASN), 0) as total_jml_asn,
            COALESCE(SUM(ps.KrngASN), 0) as total_kurang_asn
        FROM region r
        LEFT JOIN region rd ON rd.province_code = r.province_code AND rd.category = 'District'
        LEFT JOIN school s ON s.id_region = rd.id_region
        LEFT JOIN position_school ps ON ps.id_school = s.id_school
        WHERE r.district_code = ?
        GROUP BY r.id_region";

$stmt = $Conn->prepare($sql);
$stmt->bind_param("s", $district_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Data region
    $id_region             = htmlspecialchars($row['id_region']);
    $province_code         = htmlspecialchars($row['province_code']);
    $province_code_dapodik = htmlspecialchars($row['province_code_dapodik']);
    $province_name         = htmlspecialchars($row['province_name']);
    $district_code         = htmlspecialchars($row['district_code']);
    $district_code_dapodik = htmlspecialchars($row['district_code_dapodik']);
    $district_name         = htmlspecialchars($row['district_name']);
    
    // Data statistik dari query yang sudah di-aggregate
    $abk                = (int)$row['total_abk'];
    $asn                = (int)$row['total_asn'];
    $PPPK2024           = (int)$row['total_pppk2024'];
    $NonASN_sblmOkt2022 = (int)$row['total_nonasn_sebelum'];
    $NonASN_stlhOkt2022 = (int)$row['total_nonasn_setelah'];
    $JmlGuru            = (int)$row['total_jml_guru'];
    $KurangGuru         = (int)$row['total_kurang_guru'];
    $JmlASN             = (int)$row['total_jml_asn'];
    $KrngASN            = (int)$row['total_kurang_asn'];

    // Format angka ribuan
    $abk_formatted                = number_format($abk, 0, ',', '.');
    $asn_formatted                = number_format($asn, 0, ',', '.');
    $PPPK2024_formatted           = number_format($PPPK2024, 0, ',', '.');
    $NonASN_sblmOkt2022_formatted = number_format($NonASN_sblmOkt2022, 0, ',', '.');
    $NonASN_stlhOkt2022_formatted = number_format($NonASN_stlhOkt2022, 0, ',', '.');
    $JmlGuru_formatted            = number_format($JmlGuru, 0, ',', '.');
    $KurangGuru_formatted         = number_format($KurangGuru, 0, ',', '.');
    $JmlASN_formatted             = number_format($JmlASN, 0, ',', '.');
    $KrngASN_formatted            = number_format($KrngASN, 0, ',', '.');

    // Tampilkan data
    echo '
        <input type="hidden" name="Page" value="DashboardDistrict">
        <input type="hidden" name="district_code" value="'.$district_code.'">
        <div class="row mb-2"><div class="col-5"><small>Kode Provinsi (BPS)</small></div><div class="col-1">:</div><div class="col-6 text-left"><small>'.$province_code.'</small></div></div>
        <div class="row mb-2"><div class="col-5"><small>Kode Provinsi (DAPODIK)</small></div><div class="col-1">:</div><div class="col-6 text-left"><small>'.$province_code_dapodik.'</small></div></div>
        <div class="row mb-2"><div class="col-5"><small>Nama Provinsi</small></div><div class="col-1">:</div><div class="col-6 text-left"><small>'.$province_name.'</small></div></div>
        <div class="row mb-2"><div class="col-5"><small>Kode Kab/Kota (BPS)</small></div><div class="col-1">:</div><div class="col-6 text-left"><small>'.$district_code.'</small></div></div>
        <div class="row mb-2"><div class="col-5"><small>Kode Kab/Kota (DAPODIK)</small></div><div class="col-1">:</div><div class="col-6 text-left"><small>'.$district_code_dapodik.'</small></div></div>
        <div class="row mb-2"><div class="col-5"><small>Nama Kab/Kota</small></div><div class="col-1">:</div><div class="col-6 text-left"><small>'.$district_name.'</small></div></div>

        <div class="row mb-2"><div class="col-5"><small>ABK</small></div><div class="col-1">:</div><div class="col-6 text-left"><small>'.$abk_formatted.'</small></div></div>
        <div class="row mb-2"><div class="col-5"><small>ASN</small></div><div class="col-1">:</div><div class="col-6 text-left"><small>'.$asn_formatted.'</small></div></div>
        <div class="row mb-2"><div class="col-5"><small>PPPK 2024</small></div><div class="col-1">:</div><div class="col-6 text-left"><small>'.$PPPK2024_formatted.'</small></div></div>
        <div class="row mb-2"><div class="col-5"><small>Non ASN &lt; Okt 2022</small></div><div class="col-1">:</div><div class="col-6 text-left"><small>'.$NonASN_sblmOkt2022_formatted.'</small></div></div>
        <div class="row mb-2"><div class="col-5"><small>Non ASN &gt; Okt 2022</small></div><div class="col-1">:</div><div class="col-6 text-left"><small>'.$NonASN_stlhOkt2022_formatted.'</small></div></div>
        <div class="row mb-2"><div class="col-5"><small>Jumlah Guru</small></div><div class="col-1">:</div><div class="col-6 text-left"><small>'.$JmlGuru_formatted.'</small></div></div>
        <div class="row mb-2"><div class="col-5"><small>Kurang Guru</small></div><div class="col-1">:</div><div class="col-6 text-left"><small>'.$KurangGuru_formatted.'</small></div></div>
        <div class="row mb-2"><div class="col-5"><small>Jumlah ASN</small></div><div class="col-1">:</div><div class="col-6 text-left"><small>'.$JmlASN_formatted.'</small></div></div>
        <div class="row mb-2"><div class="col-5"><small>Kurang ASN</small></div><div class="col-1">:</div><div class="col-6 text-left"><small>'.$KrngASN_formatted.'</small></div></div>

        <div class="row mb-2 mt-3">
            <div class="col-12">
                <small class="text-muted">Statistik: '.number_format($row['total_sekolah'], 0, ',', '.').' sekolah diproses</small>
            </div>
        </div>

        <script>
            $(document).ready(function(){
                $("#ButtonSelengkapnya").prop("disabled", false);
            });
        </script>
    ';
} else {
    // Ambil dari geo_region jika tidak ada di region
    $province_code  = GetDetailData($Conn, 'geo_region', 'district_code', $district_code, 'province_code');
    $province_name  = GetDetailData($Conn, 'geo_region', 'district_code', $district_code, 'province_name');
    $district_name  = GetDetailData($Conn, 'geo_region', 'district_code', $district_code, 'district_name');

    echo '
        <input type="hidden" name="Page" value="DashboardDistrict">
        <input type="hidden" name="district_code" value="'.$district_code.'">
        <div class="row mb-2"><div class="col-5"><small>Kode Provinsi (BPS)</small></div><div class="col-1">:</div><div class="col-6 text-left"><small>'.$province_code.'</small></div></div>
        <div class="row mb-2"><div class="col-5"><small>Nama Provinsi</small></div><div class="col-1">:</div><div class="col-6 text-left"><small>'.$province_name.'</small></div></div>
        <div class="row mb-2"><div class="col-5"><small>Kode Kab/Kota (BPS)</small></div><div class="col-1">:</div><div class="col-6 text-left"><small>'.$district_code.'</small></div></div>
        <div class="row mb-2"><div class="col-5"><small>Nama Kab/Kota</small></div><div class="col-1">:</div><div class="col-6 text-left"><small>'.$district_name.'</small></div></div>

        <div class="alert alert-info mt-3">
            <small><i class="fas fa-info-circle"></i> Data statistik guru tidak tersedia untuk wilayah ini.</small>
        </div>

        <script>
            $(document).ready(function(){
                $("#ButtonSelengkapnya").prop("disabled", false);
            });
        </script>
    ';
}

$stmt->close();
?>
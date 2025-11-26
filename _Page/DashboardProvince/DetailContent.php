<?php
//Zona Waktu
date_default_timezone_set('Asia/Jakarta');

//Koneksi
include "../../_Config/Connection.php";
include "../../_Config/SettingGeneral.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";

//Validasi Sesi Akses
if (empty($SessionIdAccess)) {
    echo '
        <div class="alert alert-danger">
            <small>Sesi akses sudah berakhir. Silahkan <b>login</b> ulang!</small>
        </div>
    ';
    exit;
}

//Validasi province_code
if(empty($_POST['province_code'])){
    echo '
        <div class="alert alert-danger">
            <small>Kode Provinsi tidak boleh kosong!</small>
        </div>
    ';
    exit;
}

//Validasi school_level
if(empty($_POST['school_level'])){
    echo '
        <div class="alert alert-danger">
            <small>Jenjang Pendidikan tidak boleh kosong!</small>
        </div>
    ';
    exit;
}

//Buat Variabel
$province_code = $_POST['province_code'];
$school_level  = $_POST['school_level'];

//Ambil Nama Provinsi
$province_name = GetDetailData($Conn, 'region', 'province_code', $province_code, 'province_name');

//=========================================
// Hitung Total kebutuhan guru (semua jenjang)
//=========================================
$query_total = "
    SELECT SUM(ps.KurangGuru) as total_kebutuhan
    FROM position_school ps
    INNER JOIN school s ON ps.id_school = s.id_school
    INNER JOIN region r ON s.id_region = r.id_region
    WHERE r.category = 'District' AND r.province_code = ?
";
$stmt_total = mysqli_prepare($Conn, $query_total);
mysqli_stmt_bind_param($stmt_total, "s", $province_code);
mysqli_stmt_execute($stmt_total);
mysqli_stmt_bind_result($stmt_total, $total_kebutuhan_guru);
mysqli_stmt_fetch($stmt_total);
mysqli_stmt_close($stmt_total);

$kurang_guru_total = $total_kebutuhan_guru ?? 0;
$kurang_guru_total_format = number_format($kurang_guru_total, 0, ',', '.');

//=========================================
// Hitung kebutuhan guru untuk jenjang tertentu
//=========================================
$query_level = "
    SELECT SUM(ps.KurangGuru) as kebutuhan_jenjang
    FROM position_school ps
    INNER JOIN school s ON ps.id_school = s.id_school
    INNER JOIN region r ON s.id_region = r.id_region
    WHERE r.category = 'District' AND r.province_code = ? AND s.school_level = ?
";
$stmt_level = mysqli_prepare($Conn, $query_level);
mysqli_stmt_bind_param($stmt_level, "ss", $province_code, $school_level);
mysqli_stmt_execute($stmt_level);
mysqli_stmt_bind_result($stmt_level, $kebutuhan_jenjang);
mysqli_stmt_fetch($stmt_level);
mysqli_stmt_close($stmt_level);

$kurang_guru_jenjang = $kebutuhan_jenjang ?? 0;
$kurang_guru_jenjang_format = number_format($kurang_guru_jenjang, 0, ',', '.');

//=========================================
// Hitung Persentase
//=========================================
$persentase = ($kurang_guru_total > 0) ? round(($kurang_guru_jenjang / $kurang_guru_total) * 100, 2) : 0;

//=========================================
// Tampilkan Data
//=========================================
echo '
    <div class="row mb-2">
        <div class="col-5"><small>Kode Provinsi</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6"><small class="text text-grayish" id="province_code_by_jenjang">'.$province_code.'</small></div>
    </div>
    <div class="row mb-2">
        <div class="col-5"><small>Nama Provinsi</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6"><small class="text text-grayish">'.$province_name.'</small></div>
    </div>
    <div class="row mb-2">
        <div class="col-5"><small>Jenjang Pendidikan</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6"><small class="text text-grayish" id="get_school_level_from_detail">'.$school_level.'</small></div>
    </div>
    <div class="row mb-2">
        <div class="col-5"><small>Kebutuhan Guru</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6"><small class="text text-grayish">'.$kurang_guru_jenjang_format.'</small></div>
    </div>
    <div class="row mb-2">
        <div class="col-5"><small>Persentase</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6"><small class="text text-primary">'.$persentase.' %</small></div>
    </div>
';
?>

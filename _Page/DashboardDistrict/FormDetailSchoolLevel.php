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

    //Validasi district_code
    if(empty($_POST['district_code'])){
        echo '
            <div class="alert alert-danger">
                <small>Kode Kabupaten/Kota tidak boleh kosong!</small>
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
    $district_code = mysqli_real_escape_string($Conn, $_POST['district_code']);
    $school_level  = mysqli_real_escape_string($Conn, $_POST['school_level']);

    //Membuka informasi region
    $province_code = GetDetailData($Conn, 'region', 'district_code', $district_code, 'province_code');
    $province_name = GetDetailData($Conn, 'region', 'district_code', $district_code, 'province_name');
    $district_name = GetDetailData($Conn, 'region', 'district_code', $district_code, 'district_name');

    //================== Hitung KurangGuru ==================//

    //Jumlah KurangGuru pada district tertentu untuk jenjang yang dipilih
    $sql_district = "
        SELECT SUM(ps.KurangGuru) AS kurang_guru_jenjang
        FROM position_school ps
        INNER JOIN school s ON ps.id_school = s.id_school
        INNER JOIN region r ON s.id_region = r.id_region
        WHERE r.district_code='$district_code' 
          AND s.school_level='$school_level'
    ";
    $res_district = mysqli_query($Conn, $sql_district);
    $row_district = mysqli_fetch_assoc($res_district);
    $kurang_guru_jenjang = (int)$row_district['kurang_guru_jenjang'];

    //Jumlah total KurangGuru di seluruh provinsi
    $sql_province = "
        SELECT SUM(ps.KurangGuru) AS kurang_guru_total
        FROM position_school ps
        INNER JOIN school s ON ps.id_school = s.id_school
        INNER JOIN region r ON s.id_region = r.id_region
       WHERE r.district_code='$district_code' 
    ";
    $res_province = mysqli_query($Conn, $sql_province);
    $row_province = mysqli_fetch_assoc($res_province);
    $kurang_guru_total = (int)$row_province['kurang_guru_total'];

    //Format angka
    $kurang_guru_total_format = number_format($kurang_guru_total, 0, ',', '.');
    $kurang_guru_jenjang_format = number_format($kurang_guru_jenjang, 0, ',', '.');

    //Hitung persentase
    $persentase = ($kurang_guru_total > 0) ? round(($kurang_guru_jenjang / $kurang_guru_total) * 100, 2) : 0;

    //Tampilkan Hasil
    echo '
        <div class="row mb-2">
            <div class="col-5"><small>Provinsi</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small class="text text-grayish">'.ucwords(strtolower($province_name)).'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>Kabupaten/Kota</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small class="text text-grayish">'.ucwords(strtolower($district_name)).'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>Jenjang Pendidikan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small class="text text-grayish" id="get_school_level_from_detail">'.$school_level.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>Kebutuhan Guru<br>(Jenjang '.$school_level.')</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small class="text text-grayish">'.$kurang_guru_jenjang_format.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>Kebutuhan Guru<br>(Semua Jenjan)</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small class="text text-grayish">'.$kurang_guru_total_format.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>Persentase</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small class="text text-primary">'.$persentase.' %</small></div>
        </div>
    ';
?>

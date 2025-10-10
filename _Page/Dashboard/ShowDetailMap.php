<?php
    // Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    // Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        echo '
            <div class="alert alert-danger">
                <small>Sesi akses sudah berakhir. Silahkan <b>login</b> ulang!</small>
            </div>
        ';
        exit;
    }

    // Validasi province_code
    if(empty($_POST['province_code'])){
        echo '
            <div class="alert alert-danger">
                <small>Province Code tidak boleh kosong!</small>
            </div>
        ';
        exit;
    }

    // Buat variabel
    $province_code = validateAndSanitizeInput($_POST['province_code']);

    // Query tunggal untuk mendapatkan data provinsi
    $query = "SELECT 
                r.province_code_dapodik,
                COALESCE(r.province_name, gr.province_name) as province_name
              FROM region r 
              LEFT JOIN geo_region gr ON r.province_code = gr.province_code 
              WHERE r.province_code = ? 
              LIMIT 1";
    
    $stmt = mysqli_prepare($Conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $province_code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if(mysqli_num_rows($result) == 0) {
        echo '
            <div class="alert alert-danger">
                <small>Data provinsi tidak ditemukan!</small>
            </div>
        ';
        exit;
    }
    
    $data_province = mysqli_fetch_assoc($result);
    $province_code_dapodik = $data_province['province_code_dapodik'];
    $province_name = $data_province['province_name'];
    
    mysqli_stmt_close($stmt);

    // Query tunggal untuk menghitung aggregasi data
    $query = "SELECT 
                SUM(ps.abk) as total_abk,
                SUM(ps.asn) as total_asn,
                SUM(ps.JmlGuru) as total_jumlah_guru,
                SUM(ps.KurangGuru) as total_kurang_guru,
                SUM(ps.KrngASN) as total_kurang_asn
              FROM position_school ps
              INNER JOIN school s ON ps.id_school = s.id_school
              INNER JOIN region r ON s.id_region = r.id_region
              WHERE r.category = 'District' AND r.province_code = ?";
    
    $stmt = mysqli_prepare($Conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $province_code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data_position = mysqli_fetch_assoc($result);
    
    // Assign nilai dengan default 0 jika NULL
    $abk = $data_position['total_abk'] ?? 0;
    $asn = $data_position['total_asn'] ?? 0;
    $jumlah_guru = $data_position['total_jumlah_guru'] ?? 0;
    $kurang_guru = $data_position['total_kurang_guru'] ?? 0;
    $kurang_asn = $data_position['total_kurang_asn'] ?? 0;
    
    mysqli_stmt_close($stmt);

    // Tampilkan hasil
    echo '
        <input type="hidden" name="Page" value="DashboardProvince">
        <input type="hidden" name="province_code" value="'.$province_code.'">
        <div class="row mb-2">
            <div class="col-5"><small>Kode Provinsi (BPS)</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6 text-left"><small>'.$province_code.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>Kode Provinsi (DAPODIK)</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6 text-left"><small>'.$province_code_dapodik.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>Nama Provinsi</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6 text-left"><small>'.$province_name.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>ABK</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6 text-left"><small>'.number_format($abk).'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>ASN</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6 text-left"><small>'.number_format($asn).'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>Jumlah Guru</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6 text-left"><small>'.number_format($jumlah_guru).'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>Kurang Guru</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6 text-left"><small>'.number_format($kurang_guru).'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>Kurang ASN</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6 text-left"><small>'.number_format($kurang_asn).'</small></div>
        </div>
        <script>
            $(document).ready(function(){
                $("#ButtonSelengkapnya").prop("disabled", false);
            });
        </script>
    ';
?>
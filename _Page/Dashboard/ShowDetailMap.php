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
                <small>
                    Sesi akses sudah berakhir. Silahkan <b>login</b> ulang!
                </small>
            </div>
        ';
        exit;
    }

    //Validasi province_code
    if(empty($_POST['province_code'])){
        echo '
            <div class="alert alert-danger">
                <small>
                    Province Code tidak boleh kosong!
                </small>
            </div>
        ';
        exit;
    }

    //Buat variabel
    $province_code = validateAndSanitizeInput($_POST['province_code']);

    //Query ke database
    $sql = "
        SELECT 
            r.province_code,
            r.province_code_dapodik,
            r.province_name,
            r.district_code,
            r.district_code_dapodik,
            r.district_name,
            r.code_map,
            SUM(pr.abk) AS abk,
            SUM(pr.asn) AS asn,
            SUM(pr.jumlah_guru) AS jumlah_guru,
            SUM(pr.kurang_guru) AS kurang_guru,
            SUM(pr.kurang_asn) AS kurang_asn
        FROM region r
        LEFT JOIN position_region pr ON r.id_region = pr.id_region
        WHERE r.province_code = ?
        GROUP BY r.province_code
    ";
    $stmt = $Conn->prepare($sql);
    $stmt->bind_param("s", $province_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $province_code          = htmlspecialchars($row['province_code']);
        $province_code_dapodik  = htmlspecialchars($row['province_code_dapodik']);
        $province_name          = htmlspecialchars($row['province_name']);
        $abk                    = htmlspecialchars($row['abk']);
        $asn                    = htmlspecialchars($row['asn']);
        $jumlah_guru            = htmlspecialchars($row['jumlah_guru']);
        $kurang_guru            = htmlspecialchars($row['kurang_guru']);
        $kurang_asn             = htmlspecialchars($row['kurang_asn']);
        
        //Tampilkan data
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
                <div class="col-6 text-left"><small>'.$abk.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>ASN</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6 text-left"><small>'.$asn.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Jumlah Guru</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6 text-left"><small>'.$jumlah_guru.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Kurang Guru</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6 text-left"><small>'.$kurang_guru.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Kurang ASN</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6 text-left"><small>'.$kurang_asn.'</small></div>
            </div>
            <script>
                $(document).ready(function(){
                    $("#ButtonSelengkapnya").prop("disabled", false);
                });
            </script>
        ';
    } else {
        $province_code  = "";
        echo '
            <div class="alert alert-warning">
                <small>Data provinsi tidak ditemukan!</small>
            </div>
        ';
    }

    $stmt->close();
?>

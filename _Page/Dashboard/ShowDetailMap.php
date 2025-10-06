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
    $province_code          = validateAndSanitizeInput($_POST['province_code']);

    //Buka Data pada tabel region
    $province_code_dapodik  = GetDetailData($Conn, 'region', 'province_code', $province_code, 'province_code_dapodik');
    $province_name          = GetDetailData($Conn, 'region', 'province_code', $province_code, 'province_name');

    if(empty($province_name)){
        //Jika Belum Terdaftar Pada Tabel region maka buka Data pada tabel geo_region
        $province_name          = GetDetailData($Conn, 'geo_region', 'province_code', $province_code, 'province_name');
    }

    //Menghitung abk, asn, jumlah_guru, kurang_guru, kurang_asn
    
    // Inisialisasi akumulasi
    $abk = $asn = $jumlah_guru = $kurang_guru = $kurang_asn = 0;

    // Loop semua district di provinsi
    $query_region = mysqli_query($Conn, "SELECT id_region FROM region WHERE category='District' AND province_code='$province_code'");
    while ($data_region = mysqli_fetch_assoc($query_region)) {
        $id_region = $data_region['id_region'];

        // Loop posisi guru di district
        $query_position_region = mysqli_query($Conn, "SELECT abk, asn, jumlah_guru, kurang_guru, kurang_asn FROM position_region WHERE id_region='$id_region'");
        while ($data_position_region = mysqli_fetch_assoc($query_position_region)) {
            $abk            += (int)$data_position_region['abk'];
            $asn            += (int)$data_position_region['asn'];
            $jumlah_guru    += (int)$data_position_region['jumlah_guru'];
            $kurang_guru    += (int)$data_position_region['kurang_guru'];
            $kurang_asn     += (int)$data_position_region['kurang_asn'];
        }
    }
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
?>

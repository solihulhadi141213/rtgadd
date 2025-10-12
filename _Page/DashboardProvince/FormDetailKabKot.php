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
    $district_code  = validateAndSanitizeInput($_POST['district_code']);

    //Mencari id_region
    $id_region      = GetDetailData($Conn, 'region', 'district_code', $district_code, 'id_region');
    
    //Jika ditemukan
    if(!empty($id_region)){
    

        //Buka detail region
        $district_code_dapodik      = GetDetailData($Conn, 'region', 'district_code', $district_code, 'district_code_dapodik');
        $district_name              = GetDetailData($Conn, 'region', 'district_code', $district_code, 'district_name');
        $province_code              = GetDetailData($Conn, 'region', 'district_code', $district_code, 'province_code');
        $province_code_dapodik      = GetDetailData($Conn, 'region', 'district_code', $district_code, 'province_code_dapodik');
        $province_name              = GetDetailData($Conn, 'region', 'district_code', $district_code, 'province_name');

        // Hitung Agregat Data

        //Inisiasi Variabel Agar Tidak Error
        $abk                = 0;
        $asn                = 0;
        $PPPK2024           = 0;
        $NonASN_sblmOkt2022 = 0;
        $NonASN_stlhOkt2022 = 0;
        $JmlGuru            = 0;
        $KurangGuru         = 0;
        $JmlASN             = 0;
        $KrngASN            = 0;
        
        //Looping school
        $total_sekolah=0;
        $query_school = mysqli_query($Conn, "SELECT id_school FROM school WHERE id_region='$id_region'");
        if ($query_school) {
            while ($data_school = mysqli_fetch_assoc($query_school)) {
                $id_school = $data_school['id_school'];

                //Looping position_school
                $query_position = mysqli_query($Conn, "SELECT * FROM position_school WHERE id_school='$id_school'");
                if ($query_position) {
                    while ($data_position = mysqli_fetch_assoc($query_position)) {
                        $abk += intval($data_position['abk'] ?? 0);
                        $asn += intval($data_position['asn'] ?? 0);
                        $PPPK2024 += intval($data_position['PPPK2024'] ?? 0);
                        $NonASN_sblmOkt2022 += intval($data_position['NonASN_sblmOkt2022'] ?? 0);
                        $NonASN_stlhOkt2022 += intval($data_position['NonASN_stlhOkt2022'] ?? 0);
                        $JmlGuru += intval($data_position['JmlGuru'] ?? 0);
                        $KurangGuru += intval($data_position['KurangGuru'] ?? 0);
                        $JmlASN += intval($data_position['JmlASN'] ?? 0);
                        $KrngASN += intval($data_position['KrngASN'] ?? 0);
                    }
                }
                $total_sekolah=$total_sekolah+1;
            }
        }
        // Format angka ribuan
        $total_sekolah_formatted        = number_format($total_sekolah, 0, ',', '.');
        $abk_formatted                  = number_format($abk, 0, ',', '.');
        $asn_formatted                  = number_format($asn, 0, ',', '.');
        $PPPK2024_formatted             = number_format($PPPK2024, 0, ',', '.');
        $NonASN_sblmOkt2022_formatted   = number_format($NonASN_sblmOkt2022, 0, ',', '.');
        $NonASN_stlhOkt2022_formatted   = number_format($NonASN_stlhOkt2022, 0, ',', '.');
        $JmlGuru_formatted              = number_format($JmlGuru, 0, ',', '.');
        $KurangGuru_formatted           = number_format($KurangGuru, 0, ',', '.');
        $JmlASN_formatted               = number_format($JmlASN, 0, ',', '.');
        $KrngASN_formatted              = number_format($KrngASN, 0, ',', '.');

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
                <div class="col-12 text-center">
                    <small class="text-muted">Jumlah Agregat Pada Tingkat Kabupaten/Kota Dari <b>'.$total_sekolah_formatted.'</b> Sekolah</small>
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
?>
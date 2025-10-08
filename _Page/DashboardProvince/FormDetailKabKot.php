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

    //Validasi district_code
    if(empty($_POST['district_code'])){
        echo '
            <div class="alert alert-danger">
                <small>
                    Kode Kab/Kota tidak boleh kosong!
                </small>
            </div>
        ';
        exit;
    }

    //Buat variabel
    $district_code = validateAndSanitizeInput($_POST['district_code']);

    //Query ke database
    $sql = "SELECT * FROM region WHERE district_code = ?";
    $stmt = $Conn->prepare($sql);
    $stmt->bind_param("s", $district_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $id_region          = htmlspecialchars($row['id_region']);
        $province_code          = htmlspecialchars($row['province_code']);
        $province_code_dapodik  = htmlspecialchars($row['province_code_dapodik']);
        $province_name          = htmlspecialchars($row['province_name']);
        $district_code          = htmlspecialchars($row['district_code']);
        $district_code_dapodik  = htmlspecialchars($row['district_code_dapodik']);
        $district_name          = htmlspecialchars($row['district_name']);
        
        //Menghitung Kurang Guru ABK dll
        $abk            = 0;
        $asn            = 0;
        $asn_di_negeri  = 0;
        $asn_di_swasta  = 0;
        $NonASN_sblmOkt2022 = 0;
        $NonASN_stlhOkt2022 = 0;
        $pppk2024       = 0;
        $jumlah_guru    = 0;
        $kurang_guru    = 0;
        $jumlah_asn     = 0;
        $kurang_asn     = 0;
        $query = mysqli_query($Conn, "SELECT*FROM position_region WHERE id_region='$id_region'");
        while ($data = mysqli_fetch_array($query)) {
            $abk_list = $data['abk'];
            $asn_list = $data['asn'];
            $asn_di_negeri_list = $data['asn_di_negeri'];
            $asn_di_swasta_list = $data['asn_di_swasta'];
            $NonASN_sblmOkt2022_list = $data['NonASN_sblmOkt2022'];
            $NonASN_stlhOkt2022_list = $data['NonASN_stlhOkt2022'];
            $pppk2024_list = $data['pppk2024'];
            $jumlah_guru_list = $data['jumlah_guru'];
            $kurang_guru_list = $data['kurang_guru'];
            $jumlah_asn_list = $data['jumlah_asn'];
            $kurang_asn_list = $data['kurang_asn'];
            //Akumulasi
            $abk            = $abk+$abk_list;
            $asn            = $asn+$asn_list ;
            $asn_di_negeri  = $asn_di_negeri+ $asn_di_negeri_list ;
            $asn_di_swasta  = $asn_di_swasta+$asn_di_swasta_list ;
            $NonASN_sblmOkt2022 = $NonASN_sblmOkt2022+$NonASN_sblmOkt2022_list ;
            $NonASN_stlhOkt2022 = $NonASN_stlhOkt2022+$NonASN_stlhOkt2022_list ;
            $pppk2024       = $pppk2024+$pppk2024_list ;
            $jumlah_guru    = $jumlah_guru+$jumlah_guru_list ;
            $kurang_guru    = $kurang_guru+$kurang_guru_list ;
            $jumlah_asn     = $jumlah_asn+$jumlah_asn_list ;
            $kurang_asn     = $kurang_asn+$kurang_asn_list ;
        }
        //Tampilkan data
        echo '
            <input type="hidden" name="Page" value="DashboardDistrict">
            <input type="hidden" name="district_code" value="'.$district_code.'">
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
                <div class="col-5"><small>Kode Kab/Kota (BPS)</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6 text-left"><small>'.$district_code.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Kode Kab/Kota (DAPODIK)</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6 text-left"><small>'.$district_code_dapodik.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Nama Kab/Kota</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6 text-left"><small>'.$district_name.'</small></div>
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
        //Buka Detail dari tabel geo_region berdasarkan district_code
        $province_code           = GetDetailData($Conn, 'geo_region', 'district_code', $district_code,'province_code');
        $province_name           = GetDetailData($Conn, 'geo_region', 'district_code', $district_code,'province_name');
        $district_name           = GetDetailData($Conn, 'geo_region', 'district_code', $district_code,'district_name');
        $province_code  = "";
        echo '
            <input type="hidden" name="Page" value="DashboardDistrict">
            <input type="hidden" name="district_code" value="'.$district_code.'">
            <div class="row mb-2">
                <div class="col-5"><small>Kode Provinsi (BPS)</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6 text-left"><small>'.$province_code.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Nama Provinsi</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6 text-left"><small>'.$province_name.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Kode Kab/Kota (BPS)</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6 text-left"><small>'.$district_code.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Nama Kab/Kota</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6 text-left"><small>'.$district_name.'</small></div>
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

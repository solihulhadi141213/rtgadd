<?php
    // Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    // Koneksi dan dependensi
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Fungsi untuk menghitung statistik guru
    function hitungStatistikGuru($Conn, $id_region, $school_level) {
        // Query untuk menghitung total berdasarkan id_region dan school_level
        $query = "
            SELECT 
                COALESCE(SUM(ps.abk), 0) as total_abk,
                COALESCE(SUM(ps.asn), 0) as total_asn,
                COALESCE(SUM(ps.PPPK2024), 0) as total_pppk2024,
                COALESCE(SUM(ps.KurangGuru), 0) as total_kurang_guru,
                COUNT(DISTINCT s.id_school) as jumlah_sekolah,
                COUNT(DISTINCT ps.id_position) as jumlah_jabatan
            FROM position_school ps
            INNER JOIN school s ON ps.id_school = s.id_school
            WHERE s.id_region = '$id_region' 
            AND s.school_level = '$school_level'
        ";
        
        $result = mysqli_query($Conn, $query);
        
        if($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        } else {
            // Return default values jika tidak ada data
            return [
                'total_abk' => 0,
                'total_asn' => 0,
                'total_pppk2024' => 0,
                'total_kurang_guru' => 0,
                'jumlah_sekolah' => 0,
                'jumlah_jabatan' => 0
            ];
        }
    }

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

    // Validasi school_level
    if (empty($_POST['school_level'])) {
        echo '
            <div class="alert alert-danger">
                <small>Jenjang Sekolah tidak boleh kosong!</small>
            </div>
        ';
        exit;
    }

    // Sanitasi input
    $district_code  = validateAndSanitizeInput($_POST['district_code']);
    $school_level  = validateAndSanitizeInput($_POST['school_level']);

    //Mencari id_region
    $id_region      = GetDetailData($Conn, 'region', 'district_code', $district_code, 'id_region');
    
    //Jika Tidak ditemukan
    if(empty($id_region)){
        echo '
            <div class="alert alert-info mt-3">
                <small><i class="fas fa-info-circle"></i> Data statistik guru tidak tersedia untuk wilayah ini.</small>
            </div>
            <script>
                $(document).ready(function(){
                    $("#ButtonSelengkapnyaJenjang").prop("disabled", false);
                });
            </script>
        ';
        exit;
    }
    
    //JIKA DITEMUKAN

    # Form Hide
    echo '
        <input type="hidden" name="Page" value="DashboardDistrict">
        <input type="hidden" name="district_code" value="'.$district_code.'">
    ';

    # Buka detail region
    $district_code_dapodik      = GetDetailData($Conn, 'region', 'district_code', $district_code, 'district_code_dapodik');
    $district_name              = GetDetailData($Conn, 'region', 'district_code', $district_code, 'district_name');
    $province_code              = GetDetailData($Conn, 'region', 'district_code', $district_code, 'province_code');
    $province_code_dapodik      = GetDetailData($Conn, 'region', 'district_code', $district_code, 'province_code_dapodik');
    $province_name              = GetDetailData($Conn, 'region', 'district_code', $district_code, 'province_name');
    
    # Menampilkan informasi wilayah
    $statistik = hitungStatistikGuru($Conn, $id_region, $school_level);
    $jumlah_sekolah_semua = number_format($statistik['jumlah_sekolah'], 0, ',', '.');
    $jumlah_jabatan = number_format($statistik['jumlah_jabatan'], 0, ',', '.');
    $total_abk = number_format($statistik['total_abk'], 0, ',', '.');
    $total_asn = number_format($statistik['total_asn'], 0, ',', '.');
    $total_pppk2024 = number_format($statistik['total_pppk2024'], 0, ',', '.');
    $total_kurang_guru = number_format($statistik['total_kurang_guru'], 0, ',', '.');
    echo '
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="row mb-2">
                    <div class="col-5"><small>Provinsi</small></div>
                    <div class="col-1">:</div>
                    <div class="col-6 text-left"><small>'.$province_name.'</small></div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Kabupaten/Kota</small></div>
                    <div class="col-1">:</div>
                    <div class="col-6 text-left"><small>'.$district_name.'</small></div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Jenjang Sekolah</small></div>
                    <div class="col-1">:</div>
                    <div class="col-6 text-left"><small>'.$school_level.'</small></div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Jumlah Sekolah</small></div>
                    <div class="col-1">:</div>
                    <div class="col-6 text-left"><small>'.$jumlah_sekolah_semua.'</small></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row mb-2">
                    <div class="col-5"><small>ABK</small></div>
                    <div class="col-1">:</div>
                    <div class="col-6 text-left"><small>'.$total_abk.'</small></div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>ASN</small></div>
                    <div class="col-1">:</div>
                    <div class="col-6 text-left"><small>'.$total_asn.'</small></div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>PPPK 2024</small></div>
                    <div class="col-1">:</div>
                    <div class="col-6 text-left"><small>'.$total_pppk2024.'</small></div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Kurang Guru</small></div>
                    <div class="col-1">:</div>
                    <div class="col-6 text-left"><small>'.$total_kurang_guru.'</small></div>
                </div>
            </div>
        </div>
    ';
?>
    <div class="row">
        <div class="col-12" style="overflow-y:auto; height:400px;">
            <div class="table table-responsive border-1 border-top table_dengan_scroll">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <td align="center"><small><b>No</b></small></td>
                            <td align="left"><small><b>Sekolah</b></small></td>
                            <td align="left"><small><b>Jabatan</b></small></td>
                            <td align="center"><small><b>ABK</b></small></td>
                            <td align="center"><small><b>ASN</b></small></td>
                            <td align="center"><small><b>PPPK 2024</b></small></td>
                            <td align="center"><small><b>Kebutuhan Guru</b></small></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // Query untuk menampilkan data sekolah dan jabatan
                            $query = "
                                SELECT 
                                    s.id_school,
                                    s.npsn,
                                    s.school_name,
                                    s.school_level,
                                    p.position_name,
                                    ps.abk,
                                    ps.asn,
                                    ps.PPPK2024,
                                    ps.KurangGuru
                                FROM school s
                                INNER JOIN position_school ps ON ps.id_school = s.id_school
                                INNER JOIN position p ON p.id_position = ps.id_position
                                WHERE s.id_region = '$id_region' 
                                AND s.school_level = '$school_level'
                                ORDER BY ps.KurangGuru DESC
                            ";
                            
                            $result = mysqli_query($Conn, $query);
                            $no = 1;
                            $total_abk = 0;
                            $total_asn = 0;
                            $total_pppk2024 = 0;
                            $total_kurang_guru = 0;
                            
                            if(mysqli_num_rows($result) > 0) {
                                while($data = mysqli_fetch_assoc($result)) {
                                    $npsn = $data['npsn'];
                                    $school_name = $data['school_name'];
                                    $school_level = $data['school_level'];
                                    $position_name = $data['position_name'];
                                    $abk = $data['abk'];
                                    $asn = $data['asn'];
                                    $pppk2024 = $data['PPPK2024'];
                                    $kurang_guru = $data['KurangGuru'];
                                    
                                    // Akumulasi total
                                    $total_abk += $abk;
                                    $total_asn += $asn;
                                    $total_pppk2024 += $pppk2024;
                                    $total_kurang_guru += $kurang_guru;
                                    
                                    echo '
                                    <tr>
                                        <td align="center"><small>'.$no.'</small></td>
                                        <td><small>'.$school_name.'</small></td>
                                        <td><small>'.$position_name.'</small></td>
                                        <td align="center"><small>'.number_format($abk, 0, ',', '.').'</small></td>
                                        <td align="center"><small>'.number_format($asn, 0, ',', '.').'</small></td>
                                        <td align="center"><small>'.number_format($pppk2024, 0, ',', '.').'</small></td>
                                        <td align="center"><small>'.number_format($kurang_guru, 0, ',', '.').'</small></td>
                                    </tr>
                                    ';
                                    $no++;
                                }
                                
                                // Baris total
                                echo '
                                    <tr class="end_row">
                                        <td colspan="3"><small><b>JUMLAH TOTAL</b></small></td>
                                        <td align="center"><small><b>'.number_format($total_abk, 0, ',', '.').'</b></small></td>
                                        <td align="center"><small><b>'.number_format($total_asn, 0, ',', '.').'</b></small></td>
                                        <td align="center"><small><b>'.number_format($total_pppk2024, 0, ',', '.').'</b></small></td>
                                        <td align="center"><small><b>'.number_format($total_kurang_guru, 0, ',', '.').'</b></small></td>
                                    </tr>
                                ';
                            } else {
                                echo '
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <small class="text-muted">Tidak ada data sekolah untuk jenjang '.$school_level.' di wilayah ini.</small>
                                    </td>
                                </tr>
                                ';
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php
    # Enable Tombol Selengkapnya
    echo '
        <script>
            $(document).ready(function(){
                $("#ButtonSelengkapnyaJenjang").prop("disabled", false);
            });
        </script>
    ';
?>
<?php
    //Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Inisiasi Variabel id_position_region
    $id_position_region="";

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
    //Tangkap id_position_region
    if(empty($_POST['id_position_region'])){
         echo '
            <div class="alert alert-danger">
                <small>
                    ID Jabatan Per Wilayah Tidak Boleh Kosong!
                </small>
            </div>
        ';
        exit;
    }

    //Buat variabel
    $id_position_region=validateAndSanitizeInput($_POST['id_position_region']);

    //Buka Data Jabatan Per Wilayah
    $Qry = $Conn->prepare("SELECT * FROM position_region WHERE id_position_region = ?");
    $Qry->bind_param("s", $id_position_region);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
            </div>
        ';
        exit;
    }
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    //Buat Variabel
    $id_region          = $Data['id_region'];
    $id_position        = $Data['id_position'];
    $abk                = $Data['abk'];
    $asn                = $Data['asn'];
    $asn_di_negeri      = $Data['asn_di_negeri'];
    $asn_di_swasta      = $Data['asn_di_swasta'];
    $NonASN_sblmOkt2022 = $Data['NonASN_sblmOkt2022'];
    $NonASN_stlhOkt2022 = $Data['NonASN_stlhOkt2022'];
    $pppk2024           = $Data['pppk2024'];
    $jumlah_guru        = $Data['jumlah_guru'];
    $kurang_guru        = $Data['kurang_guru'];
    $jumlah_asn         = $Data['jumlah_asn'];
    $kurang_asn         = $Data['kurang_asn'];

    //Buka Provinsi dan Kab/Kota
    $province_code      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_code');
    $province_name      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_name');
    $district_code      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_code');
    $district_name      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_name');

    //Buka Ddata Jabatan
    $position_code      = GetDetailData($Conn, 'position', 'id_position', $id_position, 'position_code');
    $position_name      = GetDetailData($Conn, 'position', 'id_position', $id_position, 'position_name');
        
    //Tampilkan Data
    echo '
        <input type="hidden" name="id_position_region" value="'.$id_position_region.'">
        <div class="row mb-2">
            <div class="col-4"><small>Provinsi</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$province_name.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Kab/Kota</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$district_name.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Jabatan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$position_name.'</small>
            </div>
        </div>
    ';
?>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="abk_edit"><small>ABK</small></label>
        </div>
        <div class="col-md-8">
            <input type="number" name="abk" id="abk_edit" class="form-control" value="<?php echo $abk; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="asn_edit"><small>ASN</small></label>
        </div>
        <div class="col-md-8">
            <input type="number" name="asn" id="asn_edit" class="form-control" value="<?php echo $asn; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="asn_di_negeri_edit">
                <small>ASN Di Sekolah Negeri</small>
            </label>
        </div>
        <div class="col-md-8">
            <input type="number" name="asn_di_negeri" id="asn_di_negeri_edit" class="form-control" value="<?php echo $asn_di_negeri; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="asn_di_swasta_edit">
                <small>ASN Di Sekolah Swasta</small>
            </label>
        </div>
        <div class="col-md-8">
            <input type="number" name="asn_di_swasta" id="asn_di_swasta_edit" class="form-control" value="<?php echo $asn_di_swasta; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="NonASN_sblmOkt2022_edit">
                <small>Non ASN Sebelum Oktober 2022</small>
            </label>
        </div>
        <div class="col-md-8">
            <input type="number" name="NonASN_sblmOkt2022" id="NonASN_sblmOkt2022_edit" class="form-control" value="<?php echo $NonASN_sblmOkt2022; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="NonASN_stlhOkt2022_edit">
                <small>Non ASN Setelah Oktober 2022</small>
            </label>
        </div>
        <div class="col-md-8">
            <input type="number" name="NonASN_stlhOkt2022" id="NonASN_stlhOkt2022_edit" class="form-control" value="<?php echo $NonASN_stlhOkt2022; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="pppk2024_edit">
                <small>PPPK 2024</small>
            </label>
        </div>
        <div class="col-md-8">
            <input type="number" name="pppk2024" id="pppk2024_edit" class="form-control" value="<?php echo $pppk2024; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="jumlah_guru_edit">
                <small>Jumlah Guru</small>
            </label>
        </div>
        <div class="col-md-8">
            <input type="number" name="jumlah_guru" id="jumlah_guru_edit" class="form-control" value="<?php echo $jumlah_guru; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="kurang_guru_edit">
                <small>Kurang Guru</small>
            </label>
        </div>
        <div class="col-md-8">
            <input type="number" name="kurang_guru" id="kurang_guru_edit" class="form-control" value="<?php echo $kurang_guru; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="jumlah_asn_edit">
                <small>Jumlah ASN</small>
            </label>
        </div>
        <div class="col-md-8">
            <input type="number" name="jumlah_asn" id="jumlah_asn_edit" class="form-control" value="<?php echo $jumlah_asn; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="kurang_asn_edit">
                <small>Kurang ASN</small>
            </label>
        </div>
        <div class="col-md-8">
            <input type="number" name="kurang_asn" id="kurang_asn_edit" class="form-control" value="<?php echo $kurang_asn; ?>">
        </div>
    </div>

<script>
    // Ambil nilai dari PHP sebagai string aman
    var id_position_region = <?php echo json_encode($id_position_region); ?>;

    if(id_position_region === "" || id_position_region === null){
        // Disable tombol
        $('#ButtonEditJabatan').prop('disabled', true);
    }else{
        // Enable tombol
        $('#ButtonEditJabatan').prop('disabled', false);
    }
</script>
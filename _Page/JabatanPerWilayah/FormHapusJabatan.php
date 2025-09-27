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
    }else{
        $Result = $Qry->get_result();
        $Data = $Result->fetch_assoc();
        $Qry->close();

        //Buat Variabel
        $province           = $Data['province'];
        $regency            = $Data['regency'];
        $department         = $Data['department'];
        $workload           = $Data['workload'];
        $officials_public   = $Data['officials_public'];
        $officials_private  = $Data['officials_private'];
        $manpower_gap       = $Data['manpower_gap'];

        

        //Tampilkan Data
        echo '
            <input type="hidden" name="id_position_region" value="'.$id_position_region.'">
        ';
        echo '
            <div class="row mb-2">
                <div class="col-4"><small>Provinsi</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$province.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kab/Kota</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$regency.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Jabatan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$department.'</small>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <div class="alert alert-danger">
                        <small>
                            Apakah anda yakin akan menghapus data tersebut?
                        </small>
                    </div>
                </div>
            </div>
        ';
    }
?>

<script>
    // Ambil nilai dari PHP sebagai string aman
    var id_position_region = <?php echo json_encode($id_position_region); ?>;

    if(id_position_region === "" || id_position_region === null){
        // Disable tombol
        $('#ButtonHapusJabatan').prop('disabled', true);
    }else{
        // Enable tombol
        $('#ButtonHapusJabatan').prop('disabled', false);
    }
</script>
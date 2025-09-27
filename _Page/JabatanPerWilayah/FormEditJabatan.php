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
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="province_edit">
                        <small>Provinsi <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                    </label>
                </div>
                <div class="col-md-8">
                    <input type="text" name="province" id="province_edit" class="form-control" required value="'.$province.'">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="regency_edit">
                        <small>Kab/Kota <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                    </label>
                </div>
                <div class="col-md-8">
                    <input type="text" name="regency" id="regency_edit" class="form-control" value="'.$regency.'" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="department_edit">
                        <small>Jabatan/Posisi <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                    </label>
                </div>
                <div class="col-md-8">
                    <input type="text" name="department" id="department_edit" class="form-control" value="'.$department.'" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="workload_edit">
                        <small>ABK</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <input type="number" name="workload" id="workload_edit" class="form-control" value="'.$workload.'">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="officials_public_edit">
                        <small>ASN Sekolah Negeri</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <input type="number" name="officials_public" id="officials_public_edit" class="form-control" value="'.$officials_public.'">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="officials_private_edit">
                        <small>ASN Sekolah Swasta</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <input type="number" name="officials_private" id="officials_private_edit" class="form-control" value="'.$officials_private.'">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="manpower_gap_edit">
                        <small>Kekurangan ASN</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <input type="number" name="manpower_gap" id="manpower_gap_edit" class="form-control" value="'.$manpower_gap.'">
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
        $('#ButtonEditJabatan').prop('disabled', true);
    }else{
        // Enable tombol
        $('#ButtonEditJabatan').prop('disabled', false);
    }
</script>
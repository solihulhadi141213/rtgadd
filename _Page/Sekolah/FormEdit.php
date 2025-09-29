<?php
    //Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Inisiasi Variabel id_region
    $id_region="";

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

    //Tangkap id_school
    if(empty($_POST['id_school'])){
         echo '
            <div class="alert alert-danger">
                <small>
                    ID Wilayah Tidak Boleh Kosong!
                </small>
            </div>
        ';
        exit;
    }

    //Buat variabel
    $id_school = validateAndSanitizeInput($_POST['id_school']);

    //Fungsi untuk mengganti value kosong menjadi strip
    function showOrDash($value){
        return (isset($value) && $value !== "" && $value !== null) ? $value : "-";
    }

    //Buka Data Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM school WHERE id_school = ?");
    $Qry->bind_param("i", $id_school);
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

    //Buat Variabel dengan fallback "-"
    $id_region      = $Data['id_region'];
    $npsn           = $Data['npsn'];
    $school_name    = $Data['school_name'];

    //Buka Region
    $province_code      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_code');
    $province_name      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_name');
    $district_code      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_code');
    $district_name      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_name');

    //Tampilkan Form
    echo '<input type="hidden" name="id_school" value="'.$id_school.'">';
?>

<div class="row mb-3">
    <div class="col-md-4">
        <label for="province_code_edit"><small>Provinsi <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i> </small></label>
    </div>
    <div class="col-md-8">
        <select name="province_code" id="province_code_edit" class="form-control" required>
            <option value="">Pilih</option>
            <?php
                //Menampilkan list provinsi
                $query_region = mysqli_query($Conn, "SELECT province_code, province_name FROM region WHERE category='Province' ORDER BY province_name ASC");
                while ($data_region = mysqli_fetch_array($query_region)) {
                    $province_code_list      = $data_region['province_code'];
                    $province_name_list      = $data_region['province_name'];
                    if($province_code_list==$province_code){
                        echo '<option selected value="'.$province_code_list.'">'.$province_name_list.'</option>';
                    }else{
                        echo '<option value="'.$province_code_list.'">'.$province_name_list.'</option>';
                    }
                    
                }
            ?>
        </select>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="district_code_edit"><small>Kab/Kota <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i> </small></label>
    </div>
    <div class="col-md-8">
        <select name="district_code" id="district_code_edit" class="form-control" required>
            <option value="">Pilih</option>
            <?php
                //Menampilkan list provinsi
                $query_region_district = mysqli_query($Conn, "SELECT district_code, district_name FROM region WHERE category='District' ORDER BY district_name ASC");
                while ($data_region_district = mysqli_fetch_array($query_region_district)) {
                    $district_code_list      = $data_region_district['district_code'];
                    $district_name_list      = $data_region_district['district_name'];
                    if($district_code_list==$district_code){
                        echo '<option selected value="'.$district_code_list.'">'.$district_name_list.'</option>';
                    }else{
                        echo '<option value="'.$district_code_list.'">'.$district_name_list.'</option>';
                    }
                    
                }
            ?>
        </select>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="npsn_edit"><small>NPSN Sekolah <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i> </small></label>
    </div>
    <div class="col-md-8">
        <input type="text" name="npsn" id="npsn_edit" class="form-control" value="<?php echo $npsn; ?>" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="school_name_edit"><small>Nama Sekolah <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i> </small></label>
    </div>
    <div class="col-md-8">
        <input type="text" name="school_name" id="school_name_edit" class="form-control" value="<?php echo $school_name; ?>" required>
    </div>
</div>

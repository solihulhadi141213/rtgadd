<?php
    //Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Inisiasi Variabel id_organization
    $id_organization="";

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

    //Tangkap id_organization
    if(empty($_POST['id_organization'])){
         echo '
            <div class="alert alert-danger">
                <small>
                    ID Instansi Tidak Boleh Kosong!
                </small>
            </div>
        ';
        exit;
    }

    //Buat variabel
    $id_organization = validateAndSanitizeInput($_POST['id_organization']);

    //Fungsi untuk mengganti value kosong menjadi strip
    function showOrDash($value){
        return (isset($value) && $value !== "" && $value !== null) ? $value : "-";
    }

    //Buka Data Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM organization WHERE id_organization = ?");
    $Qry->bind_param("i", $id_organization);
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
    $id_region              = showOrDash($Data['id_region']);
    $organization_level     = showOrDash($Data['organization_level']);
    $organization_code      = showOrDash($Data['organization_code']);
    $organization_name      = showOrDash($Data['organization_name']);

    //Buka Region
    $category           = GetDetailData($Conn, 'region', 'id_region', $id_region, 'category');
    $province_code      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_code');
    $province_name      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_name');
    if($category=="District"){
        $district_code      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_code');
        $district_name      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_name');
    }else{
        $district_code      = "-";
        $district_name      = "-";
    }
?>
<input type="hidden" name="id_organization" value="<?php echo $id_organization; ?>">
<div class="row mb-3">
    <div class="col-md-4">
        <label for="province_code_edit"><small>Provinsi <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i> </small></label>
    </div>
    <div class="col-md-8">
        <select name="province_code" id="province_code_edit" class="form-control" required>
            <option value="">Pilih</option>
            <?php
                //Menampilkan list provinsi
                $query_province = mysqli_query($Conn, "SELECT province_code, province_name FROM region WHERE category='Province' ORDER BY province_name ASC");
                while ($dataprovince = mysqli_fetch_array($query_province)) {
                    $province_code_list      = $dataprovince['province_code'];
                    $province_name_list      = $dataprovince['province_name'];
                    if($province_code==$province_code_list){
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
        <label for="district_code_edit"><small>Kab/Kota </small></label>
    </div>
    <div class="col-md-8">
        <select name="district_code" id="district_code_edit" class="form-control">
            <option value="">Pilih</option>
             <?php
                //Menampilkan list district
                $query_district = mysqli_query($Conn, "SELECT district_code, district_name FROM region WHERE category='District' AND province_code='$province_code' ORDER BY district_name ASC");
                while ($DataDistrict = mysqli_fetch_array($query_district)) {
                    $district_code_list      = $DataDistrict['district_code'];
                    $district_name_list      = $DataDistrict['district_name'];
                    if($district_code==$district_code_list){
                        echo '<option selected value="'.$district_code_list.'">'.$district_name_list.'</option>';
                    }else{
                        echo '<option value="'.$district_code_list.'">'.$district_name_list.'</option>';
                    }
                }
            ?>
        </select>
        <small class="text text-grayish">Diisi jika instansi berada di tingkat Kab/Kota</small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="organization_code_edit"><small>Kode Instansi <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i> </small></label>
    </div>
    <div class="col-md-8">
        <input type="text" name="organization_code" id="organization_code_edit" class="form-control" value="<?php echo $organization_code; ?>" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="organization_name_edit"><small>Nama Instansi <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i> </small></label>
    </div>
    <div class="col-md-8">
        <input type="text" name="organization_name" id="organization_name_edit" class="form-control" value="<?php echo $organization_name; ?>" required>
    </div>
</div>

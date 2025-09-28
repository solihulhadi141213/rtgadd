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

    //Tangkap id_region
    if(empty($_POST['id_region'])){
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
    $id_region=validateAndSanitizeInput($_POST['id_region']);

    //Buka Data Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM region WHERE id_region = ?");
    $Qry->bind_param("i", $id_region);
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
       $category        = $Data['category']; 
       $province_code   = $Data['province_code']; 
       $province_name   = $Data['province_name']; 
       $district_code   = $Data['district_code']; 
       $district_name   = $Data['district_name']; 
       $code_map        = $Data['code_map'];

        //Tampilkan Form
        echo '
            <input type="hidden" name="id_region" value="'.$id_region.'">
            <input type="hidden" name="category" value="'.$category.'">
        ';
        if($category=="District"){
            echo '<div class="row mb-3">';
            echo '
                    <div class="col-4">
                        <label for="edit_province_code">
                            <small>Provinsi <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                        </label>
                    </div>
            ';
            echo '  <div class="col-8">';
            echo '      <select name="province_code" id="edit_province_code" class="form-control" required>';
            echo '          <option value="">Pilih</option>';
                            $query = mysqli_query($Conn, "SELECT DISTINCT province_code, province_name FROM region ORDER BY province_name");
                            while ($data = mysqli_fetch_array($query)) {
                                $province_code_list = $data['province_code'];
                                $province_name_list = $data['province_name'];
                                if($province_code==$province_code_list){
                                    echo '<option selected value="'.$province_code_list.'">'.$province_name_list.'</option>';
                                }else{
                                    echo '<option value="'.$province_code_list.'">'.$province_name_list.'</option>';
                                }
                            }
            echo '      </select>';
            echo '  </div>';
            echo '</div>';
            echo '
                <div class="row mb-3">
                    <div class="col-4">
                        <label for="edit_district_code">
                            <small>Kode Kab/Kota <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                        </label>
                    </div>
                    <div class="col-8">
                        <input type="text" name="district_code" id="edit_district_code" class="form-control" value="'.$district_code.'" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4">
                        <label for="edit_district_name">
                            <small>Nama Kab/Kota <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                        </label>
                    </div>
                    <div class="col-8">
                        <input type="text" name="district_name" id="edit_district_name" class="form-control" value="'.$district_name.'" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4">
                        <label for="edit_code_map">
                            <small>Kode Peta</small>
                        </label>
                    </div>
                    <div class="col-8">
                        <input type="text" name="code_map" id="edit_code_map" class="form-control" value="'.$code_map.'">
                    </div>
                </div>
            ';
        }else{
            echo '
                <div class="row mb-3">
                    <div class="col-4">
                        <label for="edit_province_code">
                            <small>Kode Provinsi <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                        </label>
                    </div>
                    <div class="col-8">
                        <input type="text" name="province_code" id="edit_province_code" class="form-control" value="'.$province_code.'" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4">
                        <label for="edit_province_name">
                            <small>Nama Provinsi <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                        </label>
                    </div>
                    <div class="col-8">
                        <input type="text" name="province_name" id="edit_province_name" class="form-control" value="'.$province_name.'" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4">
                        <label for="edit_code_map">
                            <small>Kode Peta</small>
                        </label>
                    </div>
                    <div class="col-8">
                        <input type="text" name="code_map" id="edit_code_map" class="form-control" value="'.$code_map.'"> 
                    </div>
                </div>
            ';
        }
    }
?>

<?php
    //Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Inisiasi Variabel id_position
    $id_position="";

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

    //Tangkap id_position
    if(empty($_POST['id_position'])){
         echo '
            <div class="alert alert-danger">
                <small>
                    ID Jabatan Tidak Boleh Kosong!
                </small>
            </div>
        ';
        exit;
    }

    //Buat variabel
    $id_position=validateAndSanitizeInput($_POST['id_position']);

    //Fungsi untuk mengganti value kosong menjadi strip
    function showOrDash($value){
        return (isset($value) && $value !== "" && $value !== null) ? $value : "-";
    }

    //Buka Data Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM position WHERE id_position = ?");
    $Qry->bind_param("i", $id_position);
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

        //Buat Variabel dengan fallback "-"
        $position_code  = showOrDash($Data['position_code']);
        $position_name  = showOrDash($Data['position_name']);

        //Tampilkan Data
        echo '
            <input type="hidden" name="id_position" value="'.$id_position.'">
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="position_code_edit">
                        <small>Kode Jabatan <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                    </label>
                    <input type="text" name="position_code" id="position_code_edit" class="form-control" value="'.$position_code.'" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="position_name_edit">
                        <small>Nama Jabatan <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                    </label>
                    <input type="text" name="position_name" id="position_name_edit" class="form-control" value="'.$position_name.'" required>
                </div>
            </div>
        ';
    }
?>

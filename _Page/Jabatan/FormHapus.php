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
            <div class="row mb-2">
                <div class="col-4"><small>Kode Jabatan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$position_code.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Nama Jabatan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$position_name.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12">
                    <div class="alert alert-danger">
                        <small>Apakah anda yakin akan menghapus data jabatan tersebut?</small>
                    </div>
                </div>
            </div>
        ';
    }
?>

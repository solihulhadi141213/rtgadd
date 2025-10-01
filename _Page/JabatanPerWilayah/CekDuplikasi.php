<?php
    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Time Zone
    date_default_timezone_set('Asia/Jakarta');
    $now = date('Y-m-d H:i:s');

    // --- Validasi singkat ---
    if (empty($SessionIdAccess)) {
        echo '<div class="alert alert-danger"><small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small></div>';
        exit;
    }

    //Validasi Wajib
    if (!empty($_POST['district_code'])) {
        if (!empty($_POST['id_position'])) {
            // Sanitasi input dan buat variabelnya
            $district_code          = validateAndSanitizeInput($_POST['district_code']);
            $id_position            = validateAndSanitizeInput($_POST['id_position']);

            //Cari id_region
            $id_region              = GetDetailData($Conn, 'region', 'district_code', $district_code, 'id_region');

            //Validasi Data Duplikat
            $Qry = $Conn->prepare("SELECT * FROM position_region WHERE id_region = ? AND id_position = ?");
            $Qry->bind_param("ii", $id_region, $id_position);
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
            if(!empty($Data['id_position_region'])){
                echo '
                    <div class="alert alert-warning">
                        <small>Data Jabatan Per Wilayah Sudah Ada! Sistem akan melakukan update jika anda melanjutkan proses ini.</small>
                    </div>
                ';
                exit;
            }
        }
    }
    

   
?>
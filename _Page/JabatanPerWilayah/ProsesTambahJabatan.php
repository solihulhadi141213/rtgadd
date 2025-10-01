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
    if (empty($_POST['province_code'])) {
        echo '<div class="alert alert-danger"><small>Provinsi tidak boleh kosong!</small></div>';
        exit;
    }
    if (empty($_POST['district_code'])) {
        echo '<div class="alert alert-danger"><small>Kabupaten/Kota tidak boleh kosong!</small></div>';
        exit;
    }
    if (empty($_POST['id_position'])) {
        echo '<div class="alert alert-danger"><small>Posisi/Jabatan tidak boleh kosong!</small></div>';
        exit;
    }

    // Sanitasi input dan buat variabelnya
    $province_code          = validateAndSanitizeInput($_POST['province_code']);
    $district_code          = validateAndSanitizeInput($_POST['district_code']);
    $id_position            = validateAndSanitizeInput($_POST['id_position']);
    $abk                    = validateAndSanitizeInput($_POST['abk'] ?? 0);
    $asn                    = validateAndSanitizeInput($_POST['asn'] ?? 0);
    $asn_di_negeri          = validateAndSanitizeInput($_POST['asn_di_negeri'] ?? 0);
    $asn_di_swasta          = validateAndSanitizeInput($_POST['asn_di_swasta'] ?? 0);
    $NonASN_sblmOkt2022     = validateAndSanitizeInput($_POST['NonASN_sblmOkt2022'] ?? 0);
    $NonASN_stlhOkt2022     = validateAndSanitizeInput($_POST['NonASN_stlhOkt2022'] ?? 0);
    $pppk2024               = validateAndSanitizeInput($_POST['pppk2024'] ?? 0);
    $jumlah_guru            = validateAndSanitizeInput($_POST['jumlah_guru'] ?? 0);
    $kurang_guru            = validateAndSanitizeInput($_POST['kurang_guru'] ?? 0);
    $jumlah_asn             = validateAndSanitizeInput($_POST['jumlah_asn'] ?? 0);
    $kurang_asn             = validateAndSanitizeInput($_POST['kurang_asn'] ?? 0);

    //Buka Data Region
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
       //Jika Sudah Ada Lakukan Update data
        $update = $Conn->prepare("UPDATE position_region SET 
            abk=?, 
            asn=?, 
            asn_di_negeri=?, 
            asn_di_swasta=?, 
            NonASN_sblmOkt2022=?, 
            NonASN_stlhOkt2022=?, 
            pppk2024=?, 
            jumlah_guru=?, 
            kurang_guru=?, 
            jumlah_asn=?, 
            kurang_asn=?
        WHERE id_region=? AND id_position=?");
        $update->bind_param("iiiiiiiiiiiii", 
            $abk, 
            $asn, 
            $asn_di_negeri, 
            $asn_di_swasta, 
            $NonASN_sblmOkt2022, 
            $NonASN_stlhOkt2022, 
            $pppk2024, 
            $jumlah_guru, 
            $kurang_guru, 
            $jumlah_asn, 
            $kurang_asn, 
            $id_region, 
            $id_position
        );
        if($update->execute()){
            echo '
                <div class="alert alert-success">
                    <small>Insert data <b id="NotifikasiTambahJabatanBerhasil">Success</span></small>
                </div>
            ';
            exit;
        }else{
            echo '
                <div class="alert alert-danger">
                    <small>Terjadi kesalahan pada saat update data</small>
                </div>
            ';
            exit;
        }
    }else{
        //Jika Belum Ada Lakukan Insert
        $insert = $Conn->prepare("INSERT INTO position_region (
            id_position, 
            id_region, 
            abk, 
            asn, 
            asn_di_negeri, 
            asn_di_swasta, 
            NonASN_sblmOkt2022, 
            NonASN_stlhOkt2022, 
            pppk2024, 
            jumlah_guru, 
            kurang_guru, 
            jumlah_asn, 
            kurang_asn 
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->bind_param(
            "iiiiiiiiiiiii", 
            $id_position, 
            $id_region, 
            $abk, 
            $asn, 
            $asn_di_negeri, 
            $asn_di_swasta, 
            $NonASN_sblmOkt2022, 
            $NonASN_stlhOkt2022, 
            $pppk2024, 
            $jumlah_guru, 
            $kurang_guru, 
            $jumlah_asn, 
            $kurang_asn
        );
        if($insert->execute()){
            echo '
                <div class="alert alert-success">
                    <small>Insert data <b id="NotifikasiTambahJabatanBerhasil">Success</span></small>
                </div>
            ';
            exit;
        }else{
            echo '
                <div class="alert alert-danger">
                    <small>Terjadi kesalahan pada saat insert data</small>
                </div>
            ';
            exit;
        }
    }

?>
